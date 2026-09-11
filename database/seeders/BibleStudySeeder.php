<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BibleStudy;
use Illuminate\Support\Arr;

class BibleStudySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load Init Study Series data
        $studies = require database_path('seeders/data/bible_studies.php');

        foreach ($studies as $id => $studyData) {

            // Isolate and extract attachment data keys so they do not error out during standard table insertion
            $attachments = Arr::pull($studyData, 'attachments', []);

            // 1. Persist parent baseline entity row entry properties safely inside core tables
            $bibleStudy = BibleStudy::create(array_merge(['id' => $id], $studyData));

            // 2. Loop across extracted attachment records and attach them using Eloquent relationship helpers
            foreach ($attachments as $attachmentData) {
                $bibleStudy->attachments()->create($attachmentData);
            }
        }

    }

}