<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BibleStudy;

class BibleStudiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load Init Study Series data
        $study_series_en = require database_path('seeders/data/bible_studies_en.php');
        // $study_series_fr = require database_path('seeders/data/bible_studies_fr.php');

        // Insert Bible books into the database
        foreach ($study_series as $id => $series) {
            BibleStudy::updateOrCreate(
                ['id' => $id],
                [
                    'study_series_id' => $series['study_series_id'],
                    'book_id' => $series['book_id'],
                    'bible_passage' => $series['bible_passage'],
                    'title' => $series['title'],
                    'image_links' => $series['image_links'],
                    'passage_links' => $series['passage_links'],
                    'question_sheet' => $series['question_sheet'],
                    'lecture' => $series['lecture'],

                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}