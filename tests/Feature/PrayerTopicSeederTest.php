<?php

namespace Tests\Feature;

use App\Models\PrayerTopic;
use Database\Seeders\PrayerTopicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrayerTopicSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_seeder_loads_the_prayer_topics_of_the_data_file_and_can_run_again(): void
    {
        $data = require database_path('seeders/data/prayer_topics.php');

        $this->seed(PrayerTopicSeeder::class);
        $this->seed(PrayerTopicSeeder::class);

        $this->assertSame(count($data), PrayerTopic::count());

        // Listed in the order of the data file
        $this->assertSame(
            array_keys(array_filter($data, fn ($topic) => $topic['parent_id'] === null)),
            PrayerTopic::whereNull('parent_id')->ordered()->pluck('id')->all()
        );

        foreach ($data as $id => $topic) {
            $this->assertDatabaseHas('prayer_topics', [
                'id' => $id,
                'parent_id' => $topic['parent_id'],
                'topic_en' => $topic['topic_en'],
                'min_role' => $topic['min_role'],
            ]);
        }
    }
}
