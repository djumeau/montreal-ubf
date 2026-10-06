<?php

namespace Tests\Feature;

use App\Enums\EventCategory;
use App\Enums\Role;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ManageScheduleDeleteTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Storage::fake('local');

        $this->admin = User::factory()->create(['role' => Role::ADMIN]);
    }

    private function event(array $attributes = []): Event
    {
        return Event::create(array_merge([
            'title_en' => 'Fall Conference',
            'category' => EventCategory::CONFERENCE,
            'minimum_profile' => Role::GUEST,
            'start_date' => '2026-11-20 19:00',
        ], $attributes));
    }

    public function test_deleting_an_event_keeps_its_files_unless_asked_to_delete_them(): void
    {
        $event = $this->event(['images' => ['square' => 'sq.jpg']]);
        $event->attachments()->create(['type' => 'document', 'document_name' => 'schedule.pdf', 'locale' => 'en_CA']);
        Storage::disk('public')->put('images/events/conference/2026-11-20/sq.jpg', 'x');
        Storage::disk('local')->put('documents/events/conference/2026-11-20/schedule.pdf', 'x');

        $this->actingAs($this->admin)
            ->delete(route('schedule.destroy', $event))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('events', ['id' => $event->id]);
        Storage::disk('public')->assertExists('images/events/conference/2026-11-20/sq.jpg');
        Storage::disk('local')->assertExists('documents/events/conference/2026-11-20/schedule.pdf');
    }

    public function test_deleting_an_event_with_its_files_removes_its_attachments_and_images(): void
    {
        $event = $this->event(['images' => ['square' => 'sq.jpg']]);
        $event->attachments()->create(['type' => 'document', 'document_name' => 'schedule.pdf', 'locale' => 'en_CA']);
        Storage::disk('public')->put('images/events/conference/2026-11-20/sq.jpg', 'x');
        Storage::disk('local')->put('documents/events/conference/2026-11-20/schedule.pdf', 'x');

        $this->actingAs($this->admin)
            ->delete(route('schedule.destroy', $event), ['delete_files' => '1'])
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('events', ['id' => $event->id]);
        $this->assertDatabaseMissing('event_attachments', ['event_id' => $event->id]);
        Storage::disk('public')->assertMissing('images/events/conference/2026-11-20/sq.jpg');
        Storage::disk('local')->assertMissing('documents/events/conference/2026-11-20/schedule.pdf');
        $this->assertFalse(Storage::disk('local')->directoryExists('documents/events/conference/2026-11-20'));
    }

    public function test_files_used_by_another_event_of_the_same_day_are_kept(): void
    {
        // Same category and day: both events read their files from the same folders
        $event = $this->event(['images' => ['square' => 'shared.jpg', 'mobile' => 'own.jpg']]);
        $other = $this->event(['title_en' => 'Evening Session', 'start_date' => '2026-11-20 21:00', 'images' => ['square' => 'shared.jpg']]);
        $event->attachments()->create(['type' => 'document', 'document_name' => 'schedule.pdf', 'locale' => 'en_CA']);
        $other->attachments()->create(['type' => 'document', 'document_name' => 'schedule.pdf', 'locale' => 'en_CA']);
        Storage::disk('public')->put('images/events/conference/2026-11-20/shared.jpg', 'x');
        Storage::disk('public')->put('images/events/conference/2026-11-20/own.jpg', 'x');
        Storage::disk('local')->put('documents/events/conference/2026-11-20/schedule.pdf', 'x');

        $this->actingAs($this->admin)->delete(route('schedule.destroy', $event), ['delete_files' => '1']);

        $this->assertDatabaseHas('events', ['id' => $other->id]);
        Storage::disk('public')->assertExists('images/events/conference/2026-11-20/shared.jpg');
        Storage::disk('public')->assertMissing('images/events/conference/2026-11-20/own.jpg');
        Storage::disk('local')->assertExists('documents/events/conference/2026-11-20/schedule.pdf');
    }

    public function test_only_management_roles_may_delete_an_event(): void
    {
        $event = $this->event();

        $this->actingAs(User::factory()->create(['role' => Role::MEMBER]))
            ->delete(route('schedule.destroy', $event))
            ->assertForbidden();

        $this->assertDatabaseHas('events', ['id' => $event->id]);
    }

    public function test_the_event_modal_offers_delete_for_the_events_of_the_week(): void
    {
        $this->event();

        $this->actingAs($this->admin)
            ->get('/manage-schedule?date=2026-11-20')
            ->assertOk()
            ->assertSee('id="delete_event_form"', false)
            ->assertSee('delete_url')
            ->assertSee(__('dashboard/manage-study-schedule/index.delete_event_files'))
            ->assertSee('name="delete_files" value="1" form="delete_event_form"', false);
    }
}
