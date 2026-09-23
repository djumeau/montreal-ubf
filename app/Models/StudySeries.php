<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class StudySeries extends Model
{
    protected $table = 'study_series'; // Points to the table name in the database

    protected $fillable = [ // Specifies which attributes should be mass-assignable
        'name_en',
        'name_fr',
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
     * Folder on the "public" disk holding this series' images.
     * Resolves to storage/app/public/images/study-series/series_{id}
     */
    public function imageDirectory(): string
    {
        return "images/study-series/series_{$this->id}";
    }

    /**
     * Public URL for one of the series images, or null if not set.
     * Usage: $series->imageUrl('desktop' | 'mobile' | 'thumbnail')
     */
    public function imageUrl(string $type): ?string
    {
        $file = $this->images[$type] ?? null;

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return $file ? $disk->url($this->imageDirectory() . '/' . $file) : null;
    }
}