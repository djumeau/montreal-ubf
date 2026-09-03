<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudySeries extends Model
{
    protected $table = 'study_series'; // Points to the table name in the database

    protected $fillable = [ // Specifies which attributes should be mass-assignable
        'name_en',
        'name_fr',
        'dates'
    ];
}