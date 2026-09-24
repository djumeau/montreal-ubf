<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class BibleStudy extends Model
{
    protected $table = 'bible_studies'; // Points to the table name in the database

    protected $fillable = [ // Specifies which attributes should be mass-assignable
        'study_series_id',
        'book_id',
        'bible_passage',
        'title_en',
        'title_fr',
        'image_links',
    ];

    protected $casts = [
        'image_links' => 'array', // Keys: square (1:1), desktop (16:9), mobile (3:2); shared by EN and FR
    ];

    /**
     * Relationship to the Bible Books catalog table.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(BibleBook::class, 'book_id');
    }

    /**
     * Relationship to the Study Series lookup table.
     */
    public function series(): BelongsTo
    {
        return $this->belongsTo(StudySeries::class, 'study_series_id');
    }

    /**
     * Relationship to the decoupled tracking file database records.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(StudyAttachment::class, 'bible_study_id');
    }

    /**
     * Search panel filters shared by Manage Studies and the public Bible Studies page.
     * Series / book narrow the list; every search word must match a title, the passage or the book name (e.g. "Jean 3").
     * Usage: BibleStudy::filter($series, $book, $search)
     */
    public function scopeFilter(Builder $query, ?StudySeries $series, ?BibleBook $book, string $search = ''): void
    {
        $terms = $search === '' ? [] : preg_split('/\s+/', $search);

        $query->when($series, fn ($query) => $query->where('study_series_id', $series->id))
            ->when($book, fn ($query) => $query->where('book_id', $book->id))
            ->when($terms, function ($query) use ($terms) {
                foreach ($terms as $term) {
                    $query->where(function ($query) use ($term) {
                        $query->whereLike('title_en', "%{$term}%")
                            ->orWhereLike('title_fr', "%{$term}%")
                            // Matched from the start, so "3" finds chapter 3 and not 1:19-34; French writes 3.16, the database stores 3:16
                            ->orWhereLike('bible_passage', str_replace('.', ':', $term) . '%')
                            ->orWhereHas('book', fn ($book) => $book->whereLike('name_en', "%{$term}%")
                                ->orWhereLike('name_fr', "%{$term}%"));
                    });
                }
            });
    }

    /**
     * Dynamic Contextual Language Title Accessor.
     * Usage: $bibleStudy->current_title
     */
    protected function currentTitle(): Attribute
    {
        return Attribute::get(function () {
            return app()->getLocale() === 'fr_CA' ? $this->title_fr : $this->title_en;
        });
    }

    /**
     * Book and passage for display: "Jean 3.1–21" / "John 3:1–21" (French verse separator, en dash for ranges).
     * Empty when the study has neither a book nor a passage.
     * Usage: $bibleStudy->display_passage
     */
    protected function displayPassage(): Attribute
    {
        return Attribute::get(function () {
            $isFrench = app()->getLocale() === 'fr_CA';
            $passage = str_replace('-', '–', $this->bible_passage ?? '');
            if ($isFrench) {
                $passage = str_replace(':', '.', $passage);
            }
            $bookName = $this->book ? ($isFrench ? $this->book->name_fr : $this->book->name_en) : '';

            return trim("{$bookName} {$passage}");
        });
    }

    /**
     * Context-aware Dynamic External URL Generator.
     * Usage: $bibleStudy->bible_gateway_url
     */
    protected function bibleGatewayUrl(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->book) {
                return '#';
            }

            $locale = substr(app()->getLocale(), 0, 2); // Bible Gateway uses "en" / "fr", the app uses "en_CA" / "fr_CA"
            $bookName = ($locale === 'fr') ? $this->book->name_fr : $this->book->name_en;
            $version  = ($locale === 'fr') ? 'SG21' : 'NIV';
            $passage  = $this->bible_passage;

            // Mutation layer to modify delimiter characters seamlessly for French
            if ($locale === 'fr' && $passage) {
                $passage = str_replace(':', '.', $passage);
            }

            // e.g. https://www.biblegateway.com/passage/?search=Jean+3.1-21&version=SG21
            return 'https://www.biblegateway.com/passage/?' . http_build_query([
                'search' => trim("{$bookName} {$passage}"),
                'version' => $version,
            ]);
        });
    }

    /**
     * Folder on the "public" disk holding this study's images (shared by EN and FR).
     * Resolves to storage/app/public/images/series_{id}/study_{id} (series_none when the study has no series)
     */
    public function imageDirectory(): string
    {
        $series = $this->study_series_id ? "series_{$this->study_series_id}" : 'series_none';

        return "images/{$series}/study_{$this->id}";
    }

    /**
     * Folder on the private "local" disk holding this study's attachments for one locale.
     * Resolves to storage/app/private/documents/series_{id}/{locale}/study_{id} (series_none when the study has no series)
     */
    public function documentDirectory(string $locale): string
    {
        $series = $this->study_series_id ? "series_{$this->study_series_id}" : 'series_none';

        return "documents/{$series}/{$locale}/study_{$this->id}";
    }

    /**
     * This study's document folders on the "local" disk, keyed by locale.
     * Capture them before changing study_series_id, then pass them to moveDocumentsFrom().
     */
    public function documentDirectories(): array
    {
        $directories = [];

        foreach (StudyAttachment::LOCALES as $locale) {
            $directories[$locale] = $this->documentDirectory($locale);
        }

        return $directories;
    }

    /**
     * Move this study's image folder from an old location (e.g. after its series changed) to imageDirectory().
     * Does nothing when the old folder doesn't exist or already matches.
     */
    public function moveImagesFrom(string $oldDirectory): void
    {
        self::moveFolder(Storage::disk('public'), $oldDirectory, $this->imageDirectory());
    }

    /**
     * Move this study's document folders from their old locations (taken from documentDirectories()) to documentDirectory(),
     * so the attachments' storage_path still points at their files after the series changed.
     */
    public function moveDocumentsFrom(array $oldDirectories): void
    {
        foreach ($oldDirectories as $locale => $oldDirectory) {
            self::moveFolder(Storage::disk('local'), $oldDirectory, $this->documentDirectory($locale));
        }
    }

    /**
     * Delete this study's document folders (every locale) from the "local" disk.
     */
    public function deleteDocuments(): void
    {
        foreach ($this->documentDirectories() as $directory) {
            Storage::disk('local')->deleteDirectory($directory);
        }
    }

    /**
     * Move every file in a folder to a new folder on the same disk, then remove the old folder.
     * Does nothing when the old folder doesn't exist or already matches.
     */
    private static function moveFolder(FilesystemAdapter $disk, string $oldDirectory, string $newDirectory): void
    {
        if ($oldDirectory === $newDirectory || !$disk->directoryExists($oldDirectory)) {
            return;
        }

        foreach ($disk->files($oldDirectory) as $path) {
            $disk->move($path, $newDirectory . '/' . basename($path));
        }

        $disk->deleteDirectory($oldDirectory);
    }

    /**
     * Public URL for one of the study images, served through the public/storage symlink.
     * asset() uses the current request's host, so URLs work locally and in production whatever APP_URL is.
     * Falls back to the series image, then the default series image.
     * Usage: $bibleStudy->imageUrl('square' | 'desktop' | 'mobile')
     */
    public function imageUrl(string $type): string
    {
        if ($file = $this->image_links[$type] ?? null) {
            return asset('storage/' . $this->imageDirectory() . '/' . $file);
        }

        // Series images call the square one "thumbnail"
        $seriesType = $type === 'square' ? 'thumbnail' : $type;

        return $this->series
            ? $this->series->imageUrl($seriesType)
            : asset("storage/images/study-series/default-{$seriesType}.jpg");
    }

    /**
     * Scoped collection retriever based entirely on client context locale patterns.
     */
    public function localizedAttachments(string $type)
    {
        $targetLocale = (app()->getLocale() === 'fr_CA') ? 'fr_CA' : 'en_CA';

        return $this->attachments()
                    ->where('type', $type)
                    ->where('locale', $targetLocale)
                    ->get();
    }

}
