<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BibleBook extends Model
{
    protected $table = 'bible_books';

    protected $fillable = [
        'name_en',
        'abbreviation_en',
        'name_fr',
        'abbreviation_fr',
        'testament',
        'chapters',
    ];

    /**
     * Get all Bible Studies linked to this specific book of the Bible.
     */
    public function bibleStudies(): HasMany
    {
        return $this->hasMany(BibleStudy::class, 'book_id');
    }
}
