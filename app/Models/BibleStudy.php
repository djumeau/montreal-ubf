<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BibleStudy extends Model
{

    protected $table = 'bible_studies'; // Points to the table name in the database

    protected $fillable = [
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
