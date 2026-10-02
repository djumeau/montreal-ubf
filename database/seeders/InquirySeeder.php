<?php

namespace Database\Seeders;

use App\Models\Inquiry;
use App\Models\ManageInquiry;
use App\Models\User;
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

            // Follow-up of the inquiry on the dashboard (manage_inquiries), for the entries that have one
            if ($management = $inquiry['management'] ?? null) {
                ManageInquiry::updateOrCreate(
                    ['inquiry_id' => $id],
                    [
                        'read_at' => $management['read_at'],
                        'answered_at' => $management['answered_at'],
                        // The data file names who answered by email; null when not answered (or no such user)
                        'answered_by' => $management['answered_by']
                            ? User::where('email', $management['answered_by'])->value('id')
                            : null,
                        'note' => $management['note'],
                    ]
                );
            }
        }

        // Ids come from the data file: move the id sequences past them, so the next contact form message gets a new id
        DatabaseSeeder::resetSequences(['inquiries', 'manage_inquiries']);
    }
}
