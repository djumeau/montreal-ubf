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
        // Load Init Study Series data
        $study_series = require database_path('seeders/data/study_series.php');

        // Insert Bible books into the database
        foreach ($study_series as $id => $series) {
            StudySeries::updateOrCreate(
                ['id' => $id],
                [
                    'name_en' => $series['name_en'],
                    'name_fr' => $series['name_fr'],
                    'book_id' => $series['book_id'] ?? null,
                    'dates' => $series['dates'] ?? null,
                    'images' => $series['images'] ?? null, // Cast to JSON by the model

                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}