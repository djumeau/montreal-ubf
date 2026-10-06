<?php

namespace Tests\Feature;

use App\Models\PrayerTopic;
use Database\Seeders\PrayerTopicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrayerTopicSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_seeder_loads_the_sample_prayer_topics_and_can_run_again(): void
    {
        $this->seed(PrayerTopicSeeder::class);
        $this->seed(PrayerTopicSeeder::class);

        $this->assertSame(5, PrayerTopic::count());
        $this->assertSame(2, PrayerTopic::where('min_role', 'user')->count());
        $this->assertSame(1, PrayerTopic::where('min_role', 'member')->count());
        $this->assertSame(2, PrayerTopic::find(3)->parent->id);
    }
}
