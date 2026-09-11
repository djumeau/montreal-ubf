<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'image_links' => 'array',
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

            $locale = app()->getLocale();
            $bookName = ($locale === 'fr') ? $this->book->name_fr : $this->book->name_en;
            $version  = ($locale === 'fr') ? 'SG21' : 'NIV';
            $passage  = $this->bible_passage;

            // Mutation layer to modify delimiter characters seamlessly for French
            if ($locale === 'fr' && $passage) {
                $passage = str_replace(':', '.', $passage);
            }

            return "https://biblegateway.com" . urlencode("{$bookName} {$passage}") . "&version={$version}";
        });
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
