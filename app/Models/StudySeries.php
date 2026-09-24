<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudySeries extends Model
{
    protected $table = 'study_series'; // Points to the table name in the database

    protected $fillable = [ // Specifies which attributes should be mass-assignable
        'name_en',
        'name_fr',
        'book_id',
        'dates',
        'images',
    ];

    protected $casts = [
        'images' => 'array', // Keys: desktop (16:9), mobile (3:2), thumbnail (1:1)
    ];

    /**
     * Get all Bible Studies assigned to this particular series.
     */
    public function bibleStudies(): HasMany
    {
        return $this->hasMany(BibleStudy::class, 'study_series_id');
    }

    /**
     * Related Bible book for this series; null means the series covers multiple books.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(BibleBook::class, 'book_id');
    }

    /**
     * Dates translated for display in the current locale.
     * In French: "to present" becomes "à présent" and "to" becomes "à".
     * Usage: $series->localized_dates
     */
    protected function localizedDates(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->dates || app()->getLocale() !== 'fr_CA') {
                return $this->dates;
            }

            // "to present" first, so its "to" is not replaced on its own
            return preg_replace(['/\bto present\b/i', '/\bto\b/i'], ['à présent', 'à'], $this->dates);
        });
    }

    /**
     * Folder on the "public" disk holding this series' images.
     * Resolves to storage/app/public/images/study-series/series_{id}
     */
    public function imageDirectory(): string
    {
        return "images/study-series/series_{$this->id}";
    }

    /**
     * Public URL for one of the series images, served through the public/storage symlink.
     * asset() uses the current request's host, so URLs work locally and in production whatever APP_URL is.
     * Falls back to images/study-series/default-{type}.jpg when not set.
     * Usage: $series->imageUrl('desktop' | 'mobile' | 'thumbnail')
     */
    public function imageUrl(string $type): string
    {
        $file = $this->images[$type] ?? null;

        return $file
            ? asset('storage/' . $this->imageDirectory() . '/' . $file)
            : asset("storage/images/study-series/default-{$type}.jpg");
    }
}