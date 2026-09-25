<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\BibleStudy;
use App\Models\StudySeries;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BibleStudiesPageTest extends TestCase
{
    use RefreshDatabase;

    private BibleStudy $study;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.locale' => 'en_CA']); // Default language when no 'locale' cookie is set (SetLocale)

        $series = StudySeries::create(['name_en' => 'The Gospel of John', 'name_fr' => "L'évangile de Jean"]);
        $this->study = BibleStudy::create(['study_series_id' => $series->id, 'bible_passage' => '3:1-21', 'title_en' => 'A New Birth', 'title_fr' => 'Une nouvelle naissance']);
    }

    public function test_cards_show_title_passage_series_and_question_sheets_in_the_current_language(): void
    {
        $pdf = $this->study->attachments()->create(['locale' => 'en_CA', 'type' => 'question_sheet', 'filename' => 'jn_03.q', 'extension' => 'pdf']);
        $fr = $this->study->attachments()->create(['locale' => 'fr_CA', 'type' => 'question_sheet', 'filename' => 'jn_03.q.fr', 'extension' => 'pdf']);

        $this->get('/bible-studies')
            ->assertOk()
            ->assertSee('A New Birth')
            ->assertSee('3:1–21')
            ->assertSee('Series: The Gospel of John')
            ->assertSee('Question sheet (PDF)')
            ->assertSee(route('attachments.show', $pdf))
            ->assertDontSee(route('attachments.show', $fr))
            ->assertDontSee('Edit'); // View only
    }

    public function test_lectures_and_other_files_are_listed_from_the_user_role_up(): void
    {
        $lecture = $this->study->attachments()->create(['locale' => 'en_CA', 'type' => 'lecture', 'filename' => 'jn_03.lec', 'extension' => 'pdf']);
        $other = $this->study->attachments()->create(['locale' => 'en_CA', 'type' => 'other', 'filename' => 'jn_03.map', 'extension' => 'docx']);

        $this->get('/bible-studies')
            ->assertDontSee(route('attachments.show', $lecture))
            ->assertDontSee(route('attachments.show', $other));

        $this->actingAs(User::factory()->create(['role' => Role::GUEST]))
            ->get('/bible-studies')
            ->assertDontSee(route('attachments.show', $lecture));

        $this->actingAs(User::factory()->create(['role' => Role::USER]))
            ->get('/bible-studies')
            ->assertSee(route('attachments.show', $lecture))
            ->assertSee(route('attachments.show', $other))
            ->assertSee('Lecture (PDF)')
            ->assertSee('Other (DOCX)');
    }

    public function test_group_bible_study_sheets_get_their_own_label_after_the_question_sheet(): void
    {
        config(['app.locale' => 'fr_CA']);
        $this->study->attachments()->create(['locale' => 'fr_CA', 'type' => 'question_sheet', 'filename' => 'jn_03.01-21.gbs.q.fr', 'extension' => 'pdf']);
        $this->study->attachments()->create(['locale' => 'fr_CA', 'type' => 'question_sheet', 'filename' => 'jn_03.01-21.q.fr', 'extension' => 'pdf']);

        $this->get('/bible-studies')
            ->assertOk()
            ->assertSeeInOrder(['Questionnaire (PDF)', 'Étude biblique en groupe (PDF)']);
    }

    public function test_search_and_series_filters(): void
    {
        BibleStudy::create(['title_en' => 'The Good Shepherd']);

        $this->get('/bible-studies?q=birth')
            ->assertSee('1 Bible study found')
            ->assertSee('A New Birth')
            ->assertDontSee('The Good Shepherd')
            ->assertSee('Clear filters');

        $this->get('/bible-studies?series=' . $this->study->study_series_id)
            ->assertSee('1 Bible study found')
            ->assertDontSee('The Good Shepherd');

        $this->get('/bible-studies?q=nothing-matches')
            ->assertSee('No Bible studies match these filters.');
    }
}
