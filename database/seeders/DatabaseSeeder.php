<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\BibleBook;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Enums\UserPrivilege;

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
        DB::table('bible_books')->truncate();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => bcrypt('123456'), // Replace with a secure password
            'privileges' => UserPrivilege::GUEST,
        ]);

        $this->call(BibleBookSeeder::class);

    }
}
