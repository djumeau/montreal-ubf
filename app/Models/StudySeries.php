<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Support\StudyStorage;

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
     * Most recent series first. "dates" starts with the ISO start date ("2026-04-01 to present"), so it sorts as text;
     * series without dates go last.
     * Usage: StudySeries::newestFirst()->get()
     */
    public function scopeNewestFirst(Builder $query): void
    {
        $query->orderByRaw('dates IS NULL')->orderByDesc('dates')->orderByDesc('id');
    }

    /**
     * Name in the current locale: "L'évangile de Jean" in fr_CA, "John's Gospel" otherwise.
     * Usage: $series->current_name
     */
    protected function currentName(): Attribute
    {
        return Attribute::get(fn () => app()->getLocale() === 'fr_CA' ? $this->name_fr : $this->name_en);
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
     * Folder on the "public" disk holding this series' images; its studies' image folders sit inside it.
     * Resolves to storage/app/public/images/{series}, e.g. images/john_2026
     * Capture it before changing the book or dates, then pass it to moveImagesFrom().
     */
    public function imageDirectory(): string
    {
        return 'images/' . $this->folderName();
    }

    /**
     * Move this series' image files from their old folder (taken from imageDirectory() before the change) to imageDirectory().
     */
    public function moveImagesFrom(string $oldDirectory): void
    {
        StudyStorage::move(Storage::disk('public'), array_values($this->images ?? []), $oldDirectory, $this->imageDirectory());
    }

    /**
     * Delete this series' image files, then its folder once empty.
     */
    public function deleteImages(): void
    {
        StudyStorage::delete(Storage::disk('public'), array_values($this->images ?? []), $this->imageDirectory());
    }

    /**
     * Folder name for this series' images and documents: the book (else the series name) and the start year,
     * e.g. "john_2026" for John's Gospel starting 2026-04-01, or "johns_gospel" with neither a book nor dates.
     * It changes with the book or dates, so StudySeriesController::update() moves the files.
     */
    public function folderName(): string
    {
        // Reload the book when book_id changed after it was loaded
        if ($this->book?->id !== $this->book_id) {
            $this->load('book');
        }

        $name = $this->book?->name_en ?? $this->name_en;
        $year = preg_match('/^\d{4}/', $this->dates ?? '', $match) ? $match[0] : '';

        return Str::slug("{$name} {$year}", '_') ?: 'series';
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