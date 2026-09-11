<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudySeries extends Model
{
    protected $table = 'study_series'; // Points to the table name in the database

    protected $fillable = [ // Specifies which attributes should be mass-assignable
        'name_en',
        'name_fr',
        'dates'
    ];

    /**
     * Get all Bible Studies assigned to this particular series.
     */
    public function bibleStudies(): HasMany
    {
        return $this->hasMany(BibleStudy::class, 'study_series_id');
    }
}