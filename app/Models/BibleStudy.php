<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Support\StudyStorage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            $bookName = $this->book?->current_name ?? '';

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
            $bookName = $this->book->current_name;
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
     * Resolves to storage/app/public/images/{series}/{book}_{passage}, e.g. images/john_2026/jn_01.01-18 (see folderPath()).
     * Capture it before changing the series, book or passage, then pass it to moveImagesFrom().
     */
    public function imageDirectory(): string
    {
        return 'images/' . $this->folderPath();
    }

    /**
     * Folder on the private "local" disk holding this study's attachments (EN and FR: French file names end in ".fr").
     * Resolves to storage/app/private/documents/{series}/{book}_{passage}, e.g. documents/john_2026/jn_01.01-18 (see folderPath()).
     * Capture it before changing the series, book or passage, then pass it to moveDocumentsFrom().
     */
    public function documentDirectory(): string
    {
        return 'documents/' . $this->folderPath();
    }

    /**
     * "{series}/{book}_{passage}" part shared by imageDirectory() and documentDirectory(), e.g. "john_2026/jn_01.01-18".
     * The series part comes from StudySeries::folderName(), "no_series" when the study has none.
     * The book is its English abbreviation ("1Co" -> "1co"), left out when the study has no book.
     * The passage is written like the file names: "1:1-18" -> "01.01-18", "4:43-5:15" -> "04.43-05.15".
     * Studies without a passage fall back to "study_{id}", the only thing that tells them apart.
     */
    private function folderPath(): string
    {
        // Reload the series / book when their ids changed after they were loaded
        if ($this->series?->id !== $this->study_series_id) {
            $this->load('series');
        }
        if ($this->book?->id !== $this->book_id) {
            $this->load('book');
        }

        $series = $this->series?->folderName() ?? 'no_series';

        $passage = str_replace(':', '.', strtolower(preg_replace('/\s+/', '', $this->bible_passage ?? '')));
        $passage = preg_replace('/(?<!\d)(\d)(?!\d)/', '0$1', $passage); // Single digits get a leading zero
        $passage = trim(preg_replace('/[^a-z0-9.-]+/', '_', $passage), '_'); // "1:1-5,9" -> "01.01-05_09"

        if ($passage === '') {
            $passage = "study_{$this->id}";
        } elseif ($this->book) {
            $passage = Str::slug($this->book->abbreviation_en, '_') . "_{$passage}";
        }

        return "{$series}/{$passage}";
    }

    /**
     * Move this study's image files from their old folder (taken from imageDirectory() before the change) to imageDirectory().
     */
    public function moveImagesFrom(string $oldDirectory): void
    {
        StudyStorage::move(Storage::disk('public'), array_values($this->image_links ?? []), $oldDirectory, $this->imageDirectory());
    }

    /**
     * Move this study's attachment files from their old folder (taken from documentDirectory() before the change)
     * to documentDirectory(), so the attachments' storage_path still points at their files.
     */
    public function moveDocumentsFrom(string $oldDirectory): void
    {
        StudyStorage::move(Storage::disk('local'), $this->documentNames(), $oldDirectory, $this->documentDirectory());
    }

    /**
     * Delete this study's image and attachment files, then their folders once empty.
     */
    public function deleteFiles(): void
    {
        StudyStorage::delete(Storage::disk('public'), array_values($this->image_links ?? []), $this->imageDirectory());
        StudyStorage::delete(Storage::disk('local'), $this->documentNames(), $this->documentDirectory());
    }

    /**
     * File names of this study's attachments, e.g. ["jn_01.01-18.q.pdf", "jn_01.01-18.q.fr.pdf"].
     */
    private function documentNames(): array
    {
        return $this->attachments->map->name_with_extension->all();
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
