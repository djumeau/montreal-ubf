<?php

namespace Tests\Feature;

use App\Enums\EventCategory;
use App\Enums\Role;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeaturedOnHomePageTest extends TestCase
{
    use RefreshDatabase;

    private function event(string $title, array $attributes = []): Event
    {
        return Event::create(array_merge([
            'title_en' => $title,
            'title_fr' => $title,
            'category' => EventCategory::CONFERENCE,
            'minimum_profile' => Role::GUEST,
            'start_date' => now()->addWeek(),
            'featured_on_home_page' => true,
        ], $attributes));
    }

    public function test_the_home_page_shows_the_upcoming_featured_events_open_to_the_viewer(): void
    {
        $featured = $this->event('Fall Conference', ['location' => 'Camp Kinkora, 123 Road, Quebec']);
        $this->event('Not featured', ['featured_on_home_page' => false]);
        $this->event('Past conference', ['start_date' => now()->subWeek()]);
        $this->event('Members retreat', ['minimum_profile' => Role::MEMBER]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Fall Conference')
            ->assertSee('Camp Kinkora')
            ->assertSee(route('evenements.show', $featured), false) // The test locale is fr_CA
            ->assertDontSee('Not featured')
            ->assertDontSee('Past conference')
            ->assertDontSee('Members retreat');

        $this->actingAs(User::factory()->create(['role' => Role::MEMBER]))
            ->get(route('home'))
            ->assertSee('Members retreat');
    }

    public function test_a_featured_group_bible_study_shows_without_a_link_to_an_event_page(): void
    {
        $study = $this->event('Genesis study', ['category' => EventCategory::GBS_IN_PERSON]);

        $this->get(route('home'))
            ->assertSee('Genesis study')
            ->assertDontSee(route('evenements.show', $study), false);
    }
}
