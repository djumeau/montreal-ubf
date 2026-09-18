<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Database\Seeders\InitUserSeeder;
use Database\Seeders\StudySeriesSeeder;
use Database\Seeders\BibleBookSeeder;
use Database\Seeders\BibleStudySeeder;
use Database\Seeders\InquirySeeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // 1. Temporarily disable database foreign key constraints
        Schema::disableForeignKeyConstraints();

        // Remove tables
        DB::table('users')->truncate();
        DB::table('study_series')->truncate();
        DB::table('bible_books')->truncate();
        DB::table('bible_studies')->truncate();
        DB::table('study_attachments')->truncate();
        DB::table('inquiries')->truncate();

        $this->call(InitUserSeeder::class);
        $this->call(StudySeriesSeeder::class);
        $this->call(BibleBookSeeder::class);
        $this->call(BibleStudySeeder::class); // Your attachments get created implicitly here
        $this->call(InquirySeeder::class);

    }
}
