<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BibleBook;

class BibleBookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load English Bible books data
        $bibleBooksEn = require database_path('seeders/data/bible_en.php');

        // Load French Bible books data
        $bibleBooksFr = require database_path('seeders/data/bible_fr.php');

        // Insert Bible books into the database
        foreach ($bibleBooksEn as $id => $bookEn) {

            $bookFr = $bibleBooksFr[$id] ?? null;

            BibleBook::updateOrCreate(
                ['id' => $id],
                [
                    'name_en' => $bookEn['name'],
                    'abbreviation_en' => $bookEn['abbreviation'],
                    'name_fr' => $bookFr ? $bookFr['name'] : null,
                    'abbreviation_fr' => $bookFr ? $bookFr['abbreviation'] : null,
                    'testament' => $bookEn['testament'],
                    'chapters' => $bookEn['chapters'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
        echo "Bible books seeded successfully.\n";
    }
}
