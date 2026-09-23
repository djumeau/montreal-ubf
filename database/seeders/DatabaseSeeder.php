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
        $this->call(BibleBookSeeder::class); // Before StudySeriesSeeder: study_series.book_id references bible_books
        $this->call(StudySeriesSeeder::class);
        $this->call(BibleStudySeeder::class); // Your attachments get created implicitly here
        $this->call(InquirySeeder::class);

        $this->resetSequences(['users', 'study_series', 'bible_books', 'bible_studies', 'study_attachments', 'inquiries']);

    }

    /**
     * The seeders insert rows with explicit ids, which does not advance PostgreSQL's id sequences.
     * Move each sequence to the table's highest id so the next insert does not reuse an existing id.
     */
    private function resetSequences(array $tables): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        foreach ($tables as $table) {
            // An empty table resets to 1 with is_called = false, so the next id is 1
            DB::statement(
                "SELECT setval(pg_get_serial_sequence(?, 'id'), COALESCE(MAX(id), 1), MAX(id) IS NOT NULL) FROM {$table}",
                [$table]
            );
        }
    }
}
