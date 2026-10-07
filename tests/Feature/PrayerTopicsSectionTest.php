<?php

namespace Tests\Feature;

use App\Enums\PrayerCategory;
use App\Enums\Role;
use App\Models\PrayerTopic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrayerTopicsSectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $topic = fn (string $text, Role $role, array $attributes = []) => PrayerTopic::create(array_merge([
            'topic_en' => $text,
            'topic_fr' => $text,
            'category' => PrayerCategory::GENERAL,
            'min_role' => $role,
        ], $attributes));

        $public = $topic('Public topic', Role::GUEST, ['position' => 1]);
        $topic('Public subtopic', Role::GUEST, ['parent_id' => $public->id]);
        $topic('Member subtopic', Role::MEMBER, ['parent_id' => $public->id]);
        $topic('User topic', Role::USER, ['position' => 2]);
        $members = $topic('Member topic', Role::MEMBER, ['position' => 3]);
        $topic('Public subtopic of a member topic', Role::GUEST, ['parent_id' => $members->id]);
    }

    public function test_visitors_only_see_the_topics_open_to_guests(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Public topic')
            ->assertSee('Public subtopic')
            ->assertDontSee('Member subtopic')
            ->assertDontSee('User topic')
            ->assertDontSee('Member topic')
            ->assertDontSee('Public subtopic of a member topic'); // A subtopic never shows without its main topic
    }

    public function test_logged_in_viewers_see_the_topics_up_to_their_role(): void
    {
        $this->actingAs(User::factory()->create(['role' => Role::USER]))
            ->get(route('home'))
            ->assertSee('Public topic')
            ->assertSee('User topic')
            ->assertDontSee('Member topic')
            ->assertDontSee('Member subtopic');

        $this->actingAs(User::factory()->create(['role' => Role::MEMBER]))
            ->get(route('home'))
            ->assertSee('User topic')
            ->assertSee('Member topic')
            ->assertSee('Member subtopic')
            ->assertSee('Public subtopic of a member topic');
    }

    public function test_need_prayer_opens_the_contact_form_on_the_prayer_support_subject(): void
    {
        $this->get(route('home'))
            ->assertSee(route('contact', ['inquiry' => 'prayer']), false);

        $this->get(route('contact', ['inquiry' => 'prayer']))
            ->assertOk()
            ->assertSee('<option value="prayer" selected', false);

        // Only the subjects the viewer is offered: visitors cannot preselect the message to the pastoral team
        $this->get(route('contact', ['inquiry' => 'pastoral']))
            ->assertOk()
            ->assertDontSee('selected', false);
    }

    public function test_the_topics_show_in_the_dashboard_order_five_per_page(): void
    {
        PrayerTopic::query()->delete();

        foreach (range(1, 7) as $position) {
            PrayerTopic::create([
                'topic_en' => "Topic {$position}.",
                'topic_fr' => "Topic {$position}.",
                'category' => PrayerCategory::GENERAL,
                'min_role' => Role::GUEST,
                'position' => $position,
            ]);
        }

        $this->get(route('home'))
            ->assertSeeInOrder(['Topic 1.', 'Topic 2.', 'Topic 3.', 'Topic 4.', 'Topic 5.'])
            ->assertDontSee('Topic 6.')
            ->assertSee('prayer_page=2#prayer-topics', false);

        $this->get(route('home', ['prayer_page' => 2]))
            ->assertSeeInOrder(['Topic 6.', 'Topic 7.'])
            ->assertDontSee('Topic 5.');
    }
}
