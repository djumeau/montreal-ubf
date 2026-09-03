<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class InitUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load Init Study Series data
        $user_series = require database_path('seeders/data/user_data.php');

        // Insert Bible books into the database
        foreach ($user_series as $id => $series) {
            User::updateOrCreate(
                ['id' => $id],
                [
                    'name' => $series['name'],
                    'email' => $series['email'],
                    'password' => $series['password'],
                    'privileges' => $series['privileges'],

                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
