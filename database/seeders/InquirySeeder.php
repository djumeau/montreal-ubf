<?php

namespace Database\Seeders;

use App\Models\Inquiry;
use Illuminate\Database\Seeder;

class InquirySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load Init Inquiries data
        $inquiries = require database_path('seeders/data/inquiries.php');

        // Insert inquiries into the database
        foreach ($inquiries as $id => $inquiry) {
            Inquiry::updateOrCreate(
                ['id' => $id],
                [
                    'name' => $inquiry['name'],
                    'email' => $inquiry['email'],
                    'user' => $inquiry['user'],
                    'inquiry' => $inquiry['inquiry'],
                    'message' => $inquiry['message'],

                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
