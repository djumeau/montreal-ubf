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

    private string $seriesUrl; // Study cards show once a series is picked

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.locale' => 'en_CA']); // Default language when no 'locale' cookie is set (SetLocale)

        $series = StudySeries::create(['name_en' => 'The Gospel of John', 'name_fr' => "L'évangile de Jean"]);
        $this->study = BibleStudy::create(['study_series_id' => $series->id, 'bible_passage' => '3:1-21', 'title_en' => 'A New Birth', 'title_fr' => 'Une nouvelle naissance']);
        $this->seriesUrl = '/bible-studies?series=' . $series->id;
    }

    public function test_page_starts_with_series_cards_most_recent_first(): void
    {
        StudySeries::find($this->study->study_series_id)->update(['dates' => '2026-04-01 to present']);
        $joshua = StudySeries::create(['name_en' => 'Joshua', 'name_fr' => 'Josué', 'dates' => '2026-01-10 to 2026-03-15']);
        $ephesians = StudySeries::create(['name_en' => 'Ephesians', 'name_fr' => 'Éphésiens', 'dates' => '2025-08-30 to 2025-11-08']);
        $empty = StudySeries::create(['name_en' => 'Empty Series', 'name_fr' => 'Série vide', 'dates' => '2027-01-01 to present']);
        BibleStudy::create(['study_series_id' => $joshua->id, 'title_en' => 'Be Strong and Courageous']);
        BibleStudy::create(['study_series_id' => $ephesians->id, 'title_en' => 'Chosen in Christ']);

        $this->get('/bible-studies')
            ->assertOk()
            ->assertSee('Choose a series')
            ->assertSeeInOrder([$this->seriesUrl, '/bible-studies?series=' . $joshua->id, '/bible-studies?series=' . $ephesians->id])
            ->assertSee('1 Bible study')
            ->assertDontSee('A New Birth') // No study cards until a series is picked
            ->assertDontSee('/bible-studies?series=' . $empty->id); // Series without studies get no card
    }

    public function test_cards_show_title_passage_series_and_question_sheets_in_the_current_language(): void
    {
        $pdf = $this->study->attachments()->create(['locale' => 'en_CA', 'type' => 'question_sheet', 'filename' => 'jn_03.q', 'extension' => 'pdf']);
        $fr = $this->study->attachments()->create(['locale' => 'fr_CA', 'type' => 'question_sheet', 'filename' => 'jn_03.q.fr', 'extension' => 'pdf']);

        $this->get($this->seriesUrl)
            ->assertOk()
            ->assertSee('A New Birth')
            ->assertSee('3:1–21')
            ->assertSee('Series: The Gospel of John')
            ->assertSee('jn_03.q.pdf (Q)')
            ->assertSee(route('attachments.show', $pdf))
            ->assertDontSee(route('attachments.show', $fr))
            ->assertDontSee('Edit'); // View only
    }

    public function test_lectures_and_other_files_are_listed_from_the_user_role_up(): void
    {
        $lecture = $this->study->attachments()->create(['locale' => 'en_CA', 'type' => 'lecture', 'filename' => 'jn_03.lec', 'extension' => 'pdf']);
        $other = $this->study->attachments()->create(['locale' => 'en_CA', 'type' => 'other', 'filename' => 'jn_03.map', 'extension' => 'docx']);

        $this->get($this->seriesUrl)
            ->assertDontSee(route('attachments.show', $lecture))
            ->assertDontSee(route('attachments.show', $other));

        $this->actingAs(User::factory()->create(['role' => Role::GUEST]))
            ->get($this->seriesUrl)
            ->assertDontSee(route('attachments.show', $lecture));

        $this->actingAs(User::factory()->create(['role' => Role::USER]))
            ->get($this->seriesUrl)
            ->assertSee(route('attachments.show', $lecture))
            ->assertSee(route('attachments.show', $other))
            ->assertSee('jn_03.lec.pdf (L)')
            ->assertSee('jn_03.map.docx (O)');
    }

    public function test_card_title_shows_the_view_count_of_its_files_only_when_not_zero(): void
    {
        $sheet = $this->study->attachments()->create(['locale' => 'en_CA', 'type' => 'question_sheet', 'filename' => 'jn_03.q', 'extension' => 'pdf']);

        $this->get($this->seriesUrl)->assertDontSee('fa-eye');

        $sheet->update(['views' => 7]);
        $this->study->attachments()->create(['locale' => 'en_CA', 'type' => 'question_sheet', 'filename' => 'jn_03.q', 'extension' => 'docx', 'views' => 5]);

        $this->get($this->seriesUrl)
            ->assertSee('fa-eye')
            ->assertSee('– 12')
            ->assertSee('12 views');
    }

    public function test_other_files_are_marked_a_for_autre_in_french(): void
    {
        config(['app.locale' => 'fr_CA']);
        $this->study->attachments()->create(['locale' => 'fr_CA', 'type' => 'other', 'filename' => 'jn_03.carte.fr', 'extension' => 'docx']);

        $this->actingAs(User::factory()->create(['role' => Role::USER]))
            ->get($this->seriesUrl)
            ->assertSee('jn_03.carte.fr.docx (A)');
    }

    public function test_group_bible_study_sheets_are_listed_after_the_question_sheet(): void
    {
        config(['app.locale' => 'fr_CA']);
        $this->study->attachments()->create(['locale' => 'fr_CA', 'type' => 'question_sheet', 'filename' => 'jn_03.01-21.gbs.q.fr', 'extension' => 'pdf']);
        $this->study->attachments()->create(['locale' => 'fr_CA', 'type' => 'question_sheet', 'filename' => 'jn_03.01-21.q.fr', 'extension' => 'pdf']);

        $this->get($this->seriesUrl)
            ->assertOk()
            ->assertSeeInOrder(['jn_03.01-21.q.fr.pdf (Q)', 'jn_03.01-21.gbs.q.fr.pdf (Q)']);
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
