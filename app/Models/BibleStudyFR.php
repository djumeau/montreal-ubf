<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BibleStudyFR extends Model
{
    protected $table = 'bible_studies_fr'; // Points to the table name in the database

    protected $fillable = [ // Specifies which attributes should be mass-assignable
        'study_series_id',
        'book_id',
        'bible_passage',
        'title',
        'image_links',
        'passage_links',
        'question_sheet',
        'lecture',
    ];

}
