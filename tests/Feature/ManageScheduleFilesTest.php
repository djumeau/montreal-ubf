<?php

namespace Tests\Feature;

use App\Enums\EventCategory;
use App\Enums\Role;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ManageScheduleFilesTest extends TestCase
{
    use RefreshDatabase;

    private const DOCUMENTS = 'documents/events/conference/2026-11-20/';
    private const IMAGES = 'images/events/conference/2026-11-20/';

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

    public function test_documents_and_media_can_be_uploaded_to_an_event(): void
    {
        $event = $this->event();

        $this->actingAs($this->admin)
            ->post(route('event-attachments.store', $event), [
                'type' => 'document',
                'locale' => 'fr_CA',
                'files' => [UploadedFile::fake()->create('Horaire de la Conférence.PDF', 10, 'application/pdf')],
            ])
            ->assertSessionHas('status')
            ->assertSessionHas('files_event', $event->id);

        $this->actingAs($this->admin)
            ->post(route('event-attachments.store', $event), [
                'type' => 'media',
                'locale' => 'fr_CA', // Ignored: media have no language
                'files' => [UploadedFile::fake()->image('Group Photo.jpg')],
            ])
            ->assertSessionHas('status');

        $this->assertDatabaseHas('event_attachments', ['event_id' => $event->id, 'type' => 'document', 'locale' => 'fr_CA', 'document_name' => 'horaire_de_la_conference.pdf']);
        $this->assertDatabaseHas('event_attachments', ['event_id' => $event->id, 'type' => 'media', 'locale' => null, 'document_name' => 'group_photo.jpg']);
        Storage::disk('local')->assertExists(self::DOCUMENTS . 'horaire_de_la_conference.pdf');
        Storage::disk('local')->assertExists(self::DOCUMENTS . 'group_photo.jpg');

        // The same name replaces the attachment instead of adding another one
        $this->actingAs($this->admin)
            ->post(route('event-attachments.store', $event), [
                'type' => 'document',
                'locale' => 'en_CA',
                'files' => [UploadedFile::fake()->create('horaire_de_la_conference.pdf', 10, 'application/pdf')],
            ]);

        $this->assertSame(2, $event->attachments()->count());
        $this->assertDatabaseHas('event_attachments', ['document_name' => 'horaire_de_la_conference.pdf', 'locale' => 'en_CA']);
    }

    public function test_an_upload_is_refused_for_the_wrong_file_type_or_below_the_management_roles(): void
    {
        $event = $this->event();

        // A photo is not a document, a PDF is not a media file, and a document needs a language
        $this->actingAs($this->admin)
            ->post(route('event-attachments.store', $event), ['type' => 'document', 'locale' => 'en_CA', 'files' => [UploadedFile::fake()->image('photo.jpg')]])
            ->assertSessionHasErrorsIn('uploadEventFiles', ['files.0']);

        $this->actingAs($this->admin)
            ->post(route('event-attachments.store', $event), ['type' => 'media', 'files' => [UploadedFile::fake()->create('schedule.pdf', 10, 'application/pdf')]])
            ->assertSessionHasErrorsIn('uploadEventFiles', ['files.0']);

        $this->actingAs($this->admin)
            ->post(route('event-attachments.store', $event), ['type' => 'document', 'files' => [UploadedFile::fake()->create('schedule.pdf', 10, 'application/pdf')]])
            ->assertSessionHasErrorsIn('uploadEventFiles', ['locale']);

        $this->actingAs(User::factory()->create(['role' => Role::LEADER]))
            ->post(route('event-attachments.store', $event), ['type' => 'document', 'locale' => 'en_CA', 'files' => [UploadedFile::fake()->create('schedule.pdf', 10, 'application/pdf')]])
            ->assertForbidden();

        $this->assertDatabaseCount('event_attachments', 0);
    }

    public function test_deleting_an_attachment_removes_its_file_unless_another_event_of_the_day_uses_it(): void
    {
        $event = $this->event();
        $own = $event->attachments()->create(['type' => 'document', 'document_name' => 'schedule.pdf', 'locale' => 'en_CA']);
        $shared = $event->attachments()->create(['type' => 'document', 'document_name' => 'map.pdf', 'locale' => 'en_CA']);
        $this->event(['title_en' => 'Evening session'])->attachments()->create(['type' => 'document', 'document_name' => 'map.pdf', 'locale' => 'en_CA']);
        Storage::disk('local')->put(self::DOCUMENTS . 'schedule.pdf', 'x');
        Storage::disk('local')->put(self::DOCUMENTS . 'map.pdf', 'x');

        $this->actingAs($this->admin)->delete(route('event-attachments.destroy', $own))->assertSessionHas('files_event', $event->id);
        $this->actingAs($this->admin)->delete(route('event-attachments.destroy', $shared));

        $this->assertSame(0, $event->attachments()->count());
        Storage::disk('local')->assertMissing(self::DOCUMENTS . 'schedule.pdf');
        Storage::disk('local')->assertExists(self::DOCUMENTS . 'map.pdf');
    }

    public function test_event_images_can_be_uploaded_replaced_and_deleted(): void
    {
        $event = $this->event();

        $this->actingAs($this->admin)
            ->post(route('event-images.store', $event), ['square' => UploadedFile::fake()->image('square.jpg'), 'desktop' => UploadedFile::fake()->image('wide.png')])
            ->assertSessionHas('status')
            ->assertSessionHas('files_event', $event->id);

        $images = $event->refresh()->images;
        $this->assertSame(['desktop', 'square'], collect($images)->keys()->sort()->values()->all());
        Storage::disk('public')->assertExists(self::IMAGES . $images['square']);
        Storage::disk('public')->assertExists(self::IMAGES . $images['desktop']);

        // Only the slot sent is replaced
        $this->travel(1)->minutes();
        $this->actingAs($this->admin)->post(route('event-images.store', $event), ['square' => UploadedFile::fake()->image('other.jpg')]);

        $replaced = $event->refresh()->images;
        $this->assertNotSame($images['square'], $replaced['square']);
        $this->assertSame($images['desktop'], $replaced['desktop']);
        Storage::disk('public')->assertMissing(self::IMAGES . $images['square']);
        Storage::disk('public')->assertExists(self::IMAGES . $replaced['square']);

        $this->actingAs($this->admin)->delete(route('event-images.destroy', [$event, 'square']))->assertSessionHas('status');

        $this->assertSame(['desktop'], array_keys($event->refresh()->images));
        Storage::disk('public')->assertMissing(self::IMAGES . $replaced['square']);

        $this->actingAs($this->admin)->delete(route('event-images.destroy', [$event, 'poster']))->assertNotFound();
    }

    public function test_an_image_upload_needs_an_image_and_a_management_role(): void
    {
        $event = $this->event();

        $this->actingAs($this->admin)
            ->post(route('event-images.store', $event), [])
            ->assertSessionHasErrorsIn('uploadEventImages', ['desktop']);

        $this->actingAs($this->admin)
            ->post(route('event-images.store', $event), ['mobile' => UploadedFile::fake()->create('schedule.pdf', 10, 'application/pdf')])
            ->assertSessionHasErrorsIn('uploadEventImages', ['mobile']);

        $this->actingAs(User::factory()->create(['role' => Role::LEADER]))
            ->post(route('event-images.store', $event), ['square' => UploadedFile::fake()->image('square.jpg')])
            ->assertForbidden();

        $this->assertNull($event->refresh()->images);
    }

    public function test_the_files_follow_an_event_moved_to_another_day(): void
    {
        $event = $this->event(['images' => ['square' => 'sq.jpg']]);
        $event->attachments()->create(['type' => 'document', 'document_name' => 'schedule.pdf', 'locale' => 'en_CA']);
        $event->attachments()->create(['type' => 'document', 'document_name' => 'map.pdf', 'locale' => 'en_CA']);
        $this->event(['title_en' => 'Evening session'])->attachments()->create(['type' => 'document', 'document_name' => 'map.pdf', 'locale' => 'en_CA']);
        Storage::disk('public')->put(self::IMAGES . 'sq.jpg', 'x');
        Storage::disk('local')->put(self::DOCUMENTS . 'schedule.pdf', 'x');
        Storage::disk('local')->put(self::DOCUMENTS . 'map.pdf', 'x');

        $this->actingAs($this->admin)
            ->put(route('schedule.update', $event), [
                'category' => 'conference',
                'title_en' => 'Fall Conference',
                'date' => '2026-11-27',
                'start_time' => '19:00',
                'minimum_profile' => 'guest',
            ])
            ->assertSessionHas('status');

        Storage::disk('public')->assertExists('images/events/conference/2026-11-27/sq.jpg');
        Storage::disk('public')->assertMissing(self::IMAGES . 'sq.jpg');
        Storage::disk('local')->assertExists('documents/events/conference/2026-11-27/schedule.pdf');
        Storage::disk('local')->assertMissing(self::DOCUMENTS . 'schedule.pdf');

        // The file the other event of the old day still uses is copied, not taken away
        Storage::disk('local')->assertExists('documents/events/conference/2026-11-27/map.pdf');
        Storage::disk('local')->assertExists(self::DOCUMENTS . 'map.pdf');
    }

    public function test_the_manage_schedule_page_lists_an_event_s_attachments_and_images(): void
    {
        $event = $this->event(['images' => ['square' => 'sq.jpg']]);
        $event->attachments()->create(['type' => 'document', 'document_name' => 'schedule.pdf', 'locale' => 'en_CA']);
        $event->attachments()->create(['type' => 'media', 'document_name' => 'group_photo.jpg']);

        $this->actingAs($this->admin)
            ->get(route('manage-schedule', ['date' => '2026-11-20']))
            ->assertOk()
            ->assertSee('schedule.pdf')
            ->assertSee('group_photo.jpg')
            ->assertSee('sq.jpg');
    }

    public function test_an_event_can_be_featured_on_the_home_page_and_unfeatured(): void
    {
        $fields = ['category' => 'conference', 'title_en' => 'Fall Conference', 'date' => '2026-11-20', 'start_time' => '19:00', 'minimum_profile' => 'guest'];

        $this->actingAs($this->admin)
            ->post(route('schedule.store'), $fields + ['featured_on_home_page' => '1'])
            ->assertSessionHas('status');

        $event = Event::sole();
        $this->assertTrue($event->featured_on_home_page);

        // Unticked: the form sends the hidden 0
        $this->actingAs($this->admin)->put(route('schedule.update', $event), $fields + ['featured_on_home_page' => '0']);

        $this->assertFalse($event->refresh()->featured_on_home_page);
    }
}
