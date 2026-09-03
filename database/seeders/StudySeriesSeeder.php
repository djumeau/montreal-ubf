<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StudySeries;

class StudySeriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Load English Bible books data
        $study_series = require database_path('seeders/data/study_series.php');

        // Insert Bible books into the database
        foreach ($study_series as $id => $series) {
            StudySeries::updateOrCreate(
                ['id' => $id],
                [
                    'name_en' => $series['name_en'],
                    'name_fr' => $series['name_fr'],
                    'dates' => $series['dates'] ?? null,

                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}