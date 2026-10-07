<?php

namespace Database\Seeders;

use App\Models\PrayerTopic;
use Illuminate\Database\Seeder;

class PrayerTopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load Init Prayer Topics data
        $prayerTopics = require database_path('seeders/data/prayer_topics.php');

        // Insert prayer topics into the database (main topics come before their subtopics in the data file)
        foreach ($prayerTopics as $id => $prayerTopic) {
            PrayerTopic::updateOrCreate(
                ['id' => $id],
                [
                    'parent_id' => $prayerTopic['parent_id'],
                    'position' => $prayerTopic['position'],
                    'topic_en' => $prayerTopic['topic_en'],
                    'topic_fr' => $prayerTopic['topic_fr'],
                    'category' => $prayerTopic['category'],
                    'min_role' => $prayerTopic['min_role'],
                    'url' => $prayerTopic['url'],
                    'answered' => $prayerTopic['answered'],

                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Ids come from the data file: move the id sequence past them, so the next prayer topic gets a new id
        DatabaseSeeder::resetSequences(['prayer_topics']);
    }
}
