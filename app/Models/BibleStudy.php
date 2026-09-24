<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
     * Move this study's image folder from an old location (e.g. after its series changed) to imageDirectory().
     * Does nothing when the old folder doesn't exist or already matches.
     */
    public function moveImagesFrom(string $oldDirectory): void
    {
        $disk = Storage::disk('public');
        $newDirectory = $this->imageDirectory();

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
