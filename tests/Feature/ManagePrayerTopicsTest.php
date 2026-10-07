<?php

namespace Tests\Feature;

use App\Enums\PrayerCategory;
use App\Enums\Role;
use App\Models\PrayerTopic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ManagePrayerTopicsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => Role::ADMIN]);
    }

    private function topic(array $attributes = []): PrayerTopic
    {
        return PrayerTopic::create(array_merge([
            'topic_en' => 'Pray for the fall conference',
            'topic_fr' => "Prier pour la conférence d'automne",
            'category' => PrayerCategory::CONFERENCES,
            'min_role' => Role::MEMBER,
        ], $attributes));
    }

    private function form(array $fields = []): array
    {
        return array_merge([
            'topic_en' => 'Pray for missionaries in Africa',
            'topic_fr' => 'Prier pour les missionnaires en Afrique',
            'category' => 'world_missions',
            'min_role' => 'guest',
        ], $fields);
    }

    public function test_the_page_lists_prayer_topics_and_their_subtopics_for_management_roles_only(): void
    {
        $topic = $this->topic();
        $this->topic(['parent_id' => $topic->id, 'topic_en' => 'Pray for the speakers']);

        $this->actingAs($this->admin)
            ->get(route('manage-prayer-topics'))
            ->assertOk()
            ->assertSeeInOrder(['Pray for the fall conference', 'Pray for the speakers']);

        $this->actingAs(User::factory()->create(['role' => Role::MEMBER]))
            ->get(route('manage-prayer-topics'))
            ->assertForbidden();
    }

    public function test_a_prayer_topic_can_be_created(): void
    {
        $this->actingAs($this->admin)
            ->post(route('prayer-topics.store'), $this->form(['url' => 'https://example.org/report']))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('prayer_topics', [
            'parent_id' => null,
            'topic_en' => 'Pray for missionaries in Africa',
            'topic_fr' => 'Prier pour les missionnaires en Afrique',
            'category' => 'world_missions',
            'min_role' => 'guest',
            'url' => 'https://example.org/report',
            'answered' => false,
        ]);
    }

    public function test_a_prayer_topic_is_refused_with_an_unknown_category_role_or_link(): void
    {
        $this->actingAs($this->admin)
            ->post(route('prayer-topics.store'), [
                'topic_en' => '',
                'topic_fr' => '',
                'category' => 'weather',
                'min_role' => 'pope',
                'url' => 'javascript:alert(1)',
            ])
            ->assertSessionHasErrorsIn('createPrayerTopic', ['topic_en', 'topic_fr', 'category', 'min_role', 'url']);

        $this->assertDatabaseCount('prayer_topics', 0);
    }

    public function test_a_subtopic_can_only_be_added_under_a_main_topic(): void
    {
        $main = $this->topic();

        $this->actingAs($this->admin)
            ->post(route('prayer-topics.store'), $this->form(['parent_id' => $main->id]))
            ->assertSessionHas('status');

        $subtopic = PrayerTopic::where('parent_id', $main->id)->sole();

        // One level only: not under a subtopic, and a topic with subtopics cannot become one
        $this->actingAs($this->admin)
            ->post(route('prayer-topics.store'), $this->form(['parent_id' => $subtopic->id]))
            ->assertSessionHasErrorsIn('createPrayerTopic', ['parent_id']);

        $other = $this->topic();

        $this->actingAs($this->admin)
            ->put(route('prayer-topics.update', $main), $this->form(['parent_id' => $other->id]))
            ->assertSessionHasErrorsIn('updatePrayerTopic', ['parent_id']);

        $this->actingAs($this->admin)
            ->put(route('prayer-topics.update', $other), $this->form(['parent_id' => $other->id]))
            ->assertSessionHasErrorsIn('updatePrayerTopic', ['parent_id']);
    }

    public function test_a_prayer_topic_can_be_updated_and_marked_answered(): void
    {
        $topic = $this->topic(['url' => 'https://example.org']);

        $this->actingAs($this->admin)
            ->put(route('prayer-topics.update', $topic), [
                'topic_en' => 'Thanks for the fall conference',
                'topic_fr' => "Merci pour la conférence d'automne",
                'category' => 'general',
                'min_role' => 'leader',
                'url' => '',
                'answered' => '1',
            ])
            ->assertSessionHas('status');

        $topic->refresh();
        $this->assertSame('Thanks for the fall conference', $topic->topic_en);
        $this->assertSame("Merci pour la conférence d'automne", $topic->topic_fr);
        $this->assertSame(PrayerCategory::GENERAL, $topic->category);
        $this->assertSame(Role::LEADER, $topic->min_role);
        $this->assertNull($topic->url);
        $this->assertTrue($topic->answered);
    }

    public function test_a_prayer_topic_can_be_deleted_by_management_roles_only(): void
    {
        $topic = $this->topic();

        $this->actingAs(User::factory()->create(['role' => Role::LEADER]))
            ->delete(route('prayer-topics.destroy', $topic))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(route('prayer-topics.destroy', $topic))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('prayer_topics', ['id' => $topic->id]);
    }

    public function test_deleting_a_main_topic_keeps_its_subtopics_as_main_topics(): void
    {
        $main = $this->topic();
        $subtopic = $this->topic(['parent_id' => $main->id]);

        $this->actingAs($this->admin)
            ->delete(route('prayer-topics.destroy', $main))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('prayer_topics', ['id' => $main->id]);
        $this->assertDatabaseHas('prayer_topics', ['id' => $subtopic->id, 'parent_id' => null]);
    }

    private function order(?int $parentId = null): array
    {
        return PrayerTopic::where('parent_id', $parentId)->ordered()->pluck('topic_en')->all();
    }

    public function test_a_new_main_topic_goes_to_the_top_and_a_new_subtopic_to_the_bottom(): void
    {
        foreach (['First', 'Second'] as $topic) {
            $this->actingAs($this->admin)->post(route('prayer-topics.store'), $this->form(['topic_en' => $topic]));
        }

        $this->assertSame(['Second', 'First'], $this->order());

        $main = PrayerTopic::where('topic_en', 'First')->sole();

        foreach (['Sub A', 'Sub B'] as $topic) {
            $this->actingAs($this->admin)->post(route('prayer-topics.store'), $this->form(['topic_en' => $topic, 'parent_id' => $main->id]));
        }

        $this->assertSame(['Sub A', 'Sub B'], $this->order($main->id));
    }

    public function test_a_prayer_topic_can_be_moved_up_and_down_within_its_list(): void
    {
        $a = $this->topic(['topic_en' => 'A', 'position' => 1]);
        $b = $this->topic(['topic_en' => 'B', 'position' => 2]);
        $c = $this->topic(['topic_en' => 'C', 'position' => 3]);
        $sub1 = $this->topic(['topic_en' => 'Sub 1', 'parent_id' => $a->id, 'position' => 1]);
        $sub2 = $this->topic(['topic_en' => 'Sub 2', 'parent_id' => $a->id, 'position' => 2]);

        $this->actingAs($this->admin)->put(route('prayer-topics.move', $c), ['direction' => 'up'])->assertRedirect();
        $this->assertSame(['A', 'C', 'B'], $this->order());

        $this->actingAs($this->admin)->put(route('prayer-topics.move', $a), ['direction' => 'down']);
        $this->assertSame(['C', 'A', 'B'], $this->order());

        // Already first or last: stays where it is
        $this->actingAs($this->admin)->put(route('prayer-topics.move', $c), ['direction' => 'up']);
        $this->actingAs($this->admin)->put(route('prayer-topics.move', $b), ['direction' => 'down']);
        $this->assertSame(['C', 'A', 'B'], $this->order());

        // Subtopics move among themselves, and follow their main topic
        $this->actingAs($this->admin)->put(route('prayer-topics.move', $sub2), ['direction' => 'up']);
        $this->assertSame(['Sub 2', 'Sub 1'], $this->order($a->id));
        $this->assertSame(['C', 'A', 'B'], $this->order());

        $this->actingAs($this->admin)
            ->get(route('manage-prayer-topics'))
            ->assertSeeInOrder(['(C)', '(A)', '(Sub 2)', '(Sub 1)', '(B)']); // The test locale is fr_CA: the English text is the one in parentheses

        $this->actingAs($this->admin)
            ->put(route('prayer-topics.move', $a), ['direction' => 'sideways'])
            ->assertSessionHasErrors('direction');

        $this->actingAs(User::factory()->create(['role' => Role::LEADER]))
            ->put(route('prayer-topics.move', $a), ['direction' => 'up'])
            ->assertForbidden();
    }

    public function test_the_subtopics_of_a_deleted_main_topic_go_to_the_top_in_their_order(): void
    {
        $a = $this->topic(['topic_en' => 'A', 'position' => 1]);
        $b = $this->topic(['topic_en' => 'B', 'position' => 2]);
        $this->topic(['topic_en' => 'Sub 1', 'parent_id' => $b->id, 'position' => 1]);
        $this->topic(['topic_en' => 'Sub 2', 'parent_id' => $b->id, 'position' => 2]);

        $this->actingAs($this->admin)->delete(route('prayer-topics.destroy', $b));

        $this->assertSame(['Sub 1', 'Sub 2', 'A'], $this->order());
    }

    public function test_a_prayer_topic_image_can_be_uploaded_replaced_and_removed(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)
            ->post(route('prayer-topics.store'), $this->form(['image' => UploadedFile::fake()->image('conference.jpg')]))
            ->assertSessionHas('status');

        $topic = PrayerTopic::sole();
        $first = $topic->image;
        $this->assertNotNull($first);
        Storage::disk('public')->assertExists('images/prayer-topics/' . $first);

        $this->actingAs($this->admin)->get(route('manage-prayer-topics'))->assertSee($topic->image_url);

        // A new image replaces the file
        $this->travel(1)->minutes();
        $this->actingAs($this->admin)
            ->put(route('prayer-topics.update', $topic), $this->form(['image' => UploadedFile::fake()->image('other.png')]));

        $second = $topic->refresh()->image;
        $this->assertNotSame($first, $second);
        Storage::disk('public')->assertMissing('images/prayer-topics/' . $first);
        Storage::disk('public')->assertExists('images/prayer-topics/' . $second);

        // Saving without a file keeps it; "Remove the current image" deletes it
        $this->actingAs($this->admin)->put(route('prayer-topics.update', $topic), $this->form());
        $this->assertSame($second, $topic->refresh()->image);

        $this->actingAs($this->admin)->put(route('prayer-topics.update', $topic), $this->form(['remove_image' => '1']));
        $this->assertNull($topic->refresh()->image);
        Storage::disk('public')->assertMissing('images/prayer-topics/' . $second);
    }

    public function test_a_prayer_topic_image_must_be_an_image_and_goes_with_the_deleted_topic(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)
            ->post(route('prayer-topics.store'), $this->form(['image' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf')]))
            ->assertSessionHasErrorsIn('createPrayerTopic', ['image']);

        $this->assertDatabaseCount('prayer_topics', 0);

        $this->actingAs($this->admin)
            ->post(route('prayer-topics.store'), $this->form(['image' => UploadedFile::fake()->image('conference.jpg')]));

        $topic = PrayerTopic::sole();

        $this->actingAs($this->admin)->delete(route('prayer-topics.destroy', $topic));

        Storage::disk('public')->assertMissing('images/prayer-topics/' . $topic->image);
    }
}
