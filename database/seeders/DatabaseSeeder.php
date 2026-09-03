<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Database\Seeders\InitUserSeeder;
use Database\Seeders\StudySeriesSeeder;
use Database\Seeders\BibleBookSeeder;

use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Remove tables
        DB::table('users')->truncate();
        DB::table('study_series')->truncate();
        DB::table('bible_books')->truncate();

        $this->call(InitUserSeeder::class);
        $this->call(StudySeriesSeeder::class);
        $this->call(BibleBookSeeder::class);

    }
}
