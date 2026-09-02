<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
