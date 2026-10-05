<?php

namespace Tests\Feature;

use App\Enums\EventCategory;
use App\Enums\Role;
use App\Models\BibleStudy;
use App\Models\Event;
use App\Models\StudySeries;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventImagesTest extends TestCase
{
    use RefreshDatabase;

    private StudySeries $series;
    private BibleStudy $study;

    protected function setUp(): void
    {
        parent::setUp();

        $this->series = StudySeries::create(['name_en' => 'The Gospel of John', 'name_fr' => "L'évangile de Jean"]);
        $this->study = BibleStudy::create(['study_series_id' => $this->series->id, 'bible_passage' => '3:1-21', 'title_en' => 'A New Birth']);
    }

    private function event(array $attributes = []): Event
    {
        return Event::create(array_merge([
            'title_en' => 'Sunday Worship Service',
            'category' => EventCategory::SUNDAY_SERVICE,
            'minimum_profile' => Role::GUEST,
            'start_date' => now()->addWeek()->setTime(11, 0),
        ], $attributes));
    }

    public function test_an_event_shows_its_bible_study_image_first(): void
    {
        $this->study->update(['image_links' => ['square' => 'study-sq.jpg']]);
        $this->series->update(['images' => ['thumbnail' => 'series-sq.jpg']]);
        $event = $this->event(['bible_study_id' => $this->study->id, 'images' => ['square' => 'event-sq.jpg']]);

        $this->assertStringEndsWith('storage/images/the_gospel_of_john/03.01-21/study-sq.jpg', $event->imageUrl('square'));
    }

    public function test_an_event_shows_the_series_image_when_its_bible_study_has_none(): void
    {
        $this->series->update(['images' => ['thumbnail' => 'series-sq.jpg', 'mobile' => 'series-mb.jpg']]);
        $event = $this->event(['bible_study_id' => $this->study->id, 'images' => ['square' => 'event-sq.jpg']]);

        $this->assertStringEndsWith('storage/images/the_gospel_of_john/series-sq.jpg', $event->imageUrl('square'));
        $this->assertStringEndsWith('storage/images/the_gospel_of_john/series-mb.jpg', $event->imageUrl('mobile'));
    }

    public function test_an_event_falls_back_to_its_own_image_then_the_default_one(): void
    {
        // Linked study and series without images: their default image is not used
        $linked = $this->event(['bible_study_id' => $this->study->id, 'images' => ['square' => 'event-sq.jpg']]);
        $unlinked = $this->event(['category' => EventCategory::SUNDAY_SERVICE]);

        $this->assertStringContainsString('storage/images/events/', $linked->imageUrl('square'));
        $this->assertStringEndsWith('/event-sq.jpg', $linked->imageUrl('square'));
        $this->assertStringEndsWith('storage/images/events/events-mobile.jpg', $linked->imageUrl('mobile'));
        $this->assertStringEndsWith('storage/images/events/events-square.jpg', $unlinked->imageUrl('square'));
    }

    public function test_the_events_page_shows_the_bible_study_image_as_thumbnail(): void
    {
        $this->study->update(['image_links' => ['square' => 'study-sq.jpg', 'mobile' => 'study-mb.jpg']]);
        $this->event(['bible_study_id' => $this->study->id]);

        $this->get('/events')
            ->assertOk()
            ->assertSee('storage/images/the_gospel_of_john/03.01-21/study-sq.jpg')
            ->assertSee('storage/images/the_gospel_of_john/03.01-21/study-mb.jpg');
    }
}
