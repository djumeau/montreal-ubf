<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\BibleStudy;
use App\Models\StudyAttachment;
use App\Models\StudySeries;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudyAttachmentsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private BibleStudy $study;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->admin = User::factory()->create(['role' => Role::ADMIN]);
        $series = StudySeries::create(['name_en' => "John's Gospel", 'name_fr' => "L'évangile de Jean"]);
        $this->study = BibleStudy::create(['study_series_id' => $series->id, 'title_en' => 'In Him Was Life', 'title_fr' => 'En elle il y avait la vie']);
    }

    private function upload(array $data)
    {
        return $this->actingAs($this->admin)
            ->from('/manage-studies')
            ->post(route('attachments.store', $this->study), $data + ['attachments_study_id' => $this->study->id]);
    }

    public function test_uploads_files_to_the_private_study_folder_keeping_their_names(): void
    {
        $this->upload([
            'locale' => 'fr_CA',
            'type' => 'question_sheet',
            'files' => [
                UploadedFile::fake()->create('jn_01.01-18.q.fr.pdf', 50, 'application/pdf'),
                UploadedFile::fake()->create('Jean 1 (FR).DOCX', 50, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
            ],
        ])
            ->assertRedirect('/manage-studies')
            ->assertSessionHas('attachments_study', $this->study->id)
            ->assertSessionHasNoErrors();

        $directory = "documents/series_{$this->study->study_series_id}/fr_CA/study_{$this->study->id}";
        Storage::disk('local')->assertExists("$directory/jn_01.01-18.q.fr.pdf");
        Storage::disk('local')->assertExists("$directory/jean_01_fr.q.fr.docx"); // Lower case, spaces to underscores, brackets dropped, 1 -> 01, ".q.fr" added

        $this->assertSame(
            ['jean_01_fr.q.fr.docx', 'jn_01.01-18.q.fr.pdf'], // Already had ".q.fr": not doubled
            $this->study->attachments()->get()->map->name_with_extension->sort()->values()->all()
        );
        $this->assertSame(['fr_CA'], $this->study->attachments()->pluck('locale')->unique()->values()->all());
    }

    public function test_lectures_get_the_lec_marker(): void
    {
        $this->upload([
            'locale' => 'en_CA',
            'type' => 'lecture',
            'files' => [
                UploadedFile::fake()->create('JN_01.1-18.pdf', 50, 'application/pdf'),
                UploadedFile::fake()->create('jn_02.lec.pdf', 50, 'application/pdf'),
            ],
        ])->assertSessionHasNoErrors();

        $this->assertSame(
            ['jn_01.01-18.lec.pdf', 'jn_02.lec.pdf'],
            $this->study->attachments()->get()->map->name_with_extension->sort()->values()->all()
        );
    }

    public function test_french_names_get_type_then_language_marker_and_padded_numbers(): void
    {
        $this->upload([
            'locale' => 'fr_CA',
            'type' => 'lecture',
            'files' => [
                UploadedFile::fake()->create('JN 1.1-18.pdf', 50, 'application/pdf'),         // No markers yet
                UploadedFile::fake()->create('jn_2.13-25.fr.pdf', 50, 'application/pdf'),     // Language only: type goes before it
                UploadedFile::fake()->create('jn_10.1-42.lec.fr.pdf', 50, 'application/pdf'), // Complete: 10 and 42 stay
            ],
        ])->assertSessionHasNoErrors();

        $this->assertSame(
            ['jn_01.01-18.lec.fr.pdf', 'jn_02.13-25.lec.fr.pdf', 'jn_10.01-42.lec.fr.pdf'],
            $this->study->attachments()->get()->map->name_with_extension->sort()->values()->all()
        );
    }

    public function test_other_type_and_file_name_clean_up(): void
    {
        $this->upload([
            'locale' => 'fr_CA',
            'type' => 'other',
            'files' => [UploadedFile::fake()->create('Notes de Leçon  Jean 3 .PDF', 50, 'application/pdf')],
        ])->assertSessionHasNoErrors();

        $attachment = $this->study->attachments()->firstOrFail();
        $this->assertSame('other', $attachment->type);
        $this->assertSame('notes_de_lecon_jean_03.fr.pdf', $attachment->name_with_extension);
        Storage::disk('local')->assertExists($attachment->storage_path);
    }

    public function test_same_name_in_same_language_replaces_and_keeps_views(): void
    {
        $existing = $this->study->attachments()->create(['locale' => 'en_CA', 'type' => 'question_sheet', 'filename' => 'jn_01.q', 'extension' => 'pdf', 'views' => 12]);

        $this->upload([
            'locale' => 'en_CA',
            'type' => 'question_sheet',
            'files' => [UploadedFile::fake()->create('jn_01.pdf', 50, 'application/pdf')],
        ])->assertSessionHasNoErrors();

        $this->assertSame(1, $this->study->attachments()->count());
        $existing->refresh();
        $this->assertSame(12, $existing->views);
        $this->assertSame('question_sheet', $existing->type);
    }

    public function test_rejects_other_file_types_into_the_upload_error_bag(): void
    {
        $this->upload([
            'locale' => 'en_CA',
            'type' => 'lecture',
            'files' => [UploadedFile::fake()->image('photo.jpg')],
        ])->assertSessionHasErrors('files.0', null, 'uploadAttachments');

        $this->upload(['locale' => 'de_DE', 'type' => 'sermon'])
            ->assertSessionHasErrors(['locale', 'type', 'files'], null, 'uploadAttachments');

        $this->assertSame(0, StudyAttachment::count());
    }

    public function test_delete_removes_the_row_and_the_file(): void
    {
        $attachment = $this->study->attachments()->create(['locale' => 'en_CA', 'type' => 'lecture', 'filename' => 'jn_01.lec', 'extension' => 'pdf']);
        Storage::disk('local')->put($attachment->storage_path, 'x');

        $this->actingAs($this->admin)
            ->from('/manage-studies')
            ->delete(route('attachments.destroy', $attachment))
            ->assertRedirect('/manage-studies')
            ->assertSessionHas('attachments_study', $this->study->id);

        $this->assertModelMissing($attachment);
        Storage::disk('local')->assertMissing($attachment->storage_path);
    }

    public function test_page_reopens_the_modal_after_an_upload(): void
    {
        $this->study->attachments()->create(['locale' => 'en_CA', 'type' => 'lecture', 'filename' => 'jn_01.lec', 'extension' => 'pdf']);

        $this->actingAs($this->admin)
            ->withSession(['attachments_study' => $this->study->id, 'status' => '1 attachment uploaded.'])
            ->get('/manage-studies')
            ->assertOk()
            ->assertSee('showAttachmentsModal: true', false)
            ->assertSee('1 attachment uploaded.');
    }

    public function test_modal_view_button_links_each_file_with_its_view_count(): void
    {
        $pdf = $this->study->attachments()->create(['locale' => 'en_CA', 'type' => 'question_sheet', 'filename' => 'jn_01.q', 'extension' => 'pdf', 'views' => 7]);

        // Modal data is JSON-escaped twice by @js: a quote comes out as backslash + "u0022", a slash as three backslashes + "/"
        $q = chr(92) . 'u0022';
        $slash = str_repeat(chr(92), 3) . '/';

        $this->actingAs($this->admin)->get('/manage-studies')
            ->assertOk()
            ->assertSee("{$q}views{$q}:7,{$q}show_url{$q}", false)
            ->assertSee("{$slash}documents{$slash}{$pdf->id}{$q}", false)
            ->assertSee('fa-solid fa-eye', false);
    }

    public function test_anyone_can_view_a_pdf_inline_and_download_a_docx_counting_views(): void
    {
        $pdf = $this->study->attachments()->create(['locale' => 'en_CA', 'type' => 'question_sheet', 'filename' => 'jn_01.q', 'extension' => 'pdf']);
        $docx = $this->study->attachments()->create(['locale' => 'en_CA', 'type' => 'question_sheet', 'filename' => 'jn_01.q', 'extension' => 'docx']);
        Storage::disk('local')->put($pdf->storage_path, 'pdf');
        Storage::disk('local')->put($docx->storage_path, 'docx');

        $this->get(route('attachments.show', $pdf))
            ->assertOk()
            ->assertHeader('content-disposition', 'inline; filename=jn_01.q.pdf');
        $this->get(route('attachments.show', $docx))
            ->assertOk()
            ->assertDownload('jn_01.q.docx');

        $this->assertSame(1, $pdf->fresh()->views);
        $this->assertSame(1, $docx->fresh()->views);
    }

    public function test_missing_file_is_a_404_and_not_counted(): void
    {
        $attachment = $this->study->attachments()->create(['locale' => 'en_CA', 'type' => 'lecture', 'filename' => 'jn_01.lec', 'extension' => 'pdf']);

        $this->get(route('attachments.show', $attachment))->assertNotFound();

        $this->assertSame(0, $attachment->fresh()->views);
    }

    public function test_admin_and_elder_views_are_not_counted_but_members_are(): void
    {
        $pdf = $this->study->attachments()->create(['locale' => 'en_CA', 'type' => 'question_sheet', 'filename' => 'jn_01.q', 'extension' => 'pdf']);
        Storage::disk('local')->put($pdf->storage_path, 'pdf');

        foreach ([Role::ADMIN, Role::ELDER] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->get(route('attachments.show', $pdf))
                ->assertOk();
        }
        $this->assertSame(0, $pdf->fresh()->views);

        $this->actingAs(User::factory()->create(['role' => Role::MEMBER]))
            ->get(route('attachments.show', $pdf))
            ->assertOk();
        $this->assertSame(1, $pdf->fresh()->views);
    }

    public function test_home_page_links_the_featured_study_question_sheets_in_the_current_language(): void
    {
        $featured = BibleStudy::forceCreate(['id' => __('home/study.studyId'), 'title_en' => 'See Your King is Coming!']);
        $en = $featured->attachments()->create(['locale' => 'en_CA', 'type' => 'question_sheet', 'filename' => 'jn_11.55-12.19.q', 'extension' => 'pdf']);
        $fr = $featured->attachments()->create(['locale' => 'fr_CA', 'type' => 'question_sheet', 'filename' => 'jn_11.55-12.19.q.fr', 'extension' => 'pdf']);

        config(['app.locale' => 'en_CA']); // Default language when no 'locale' cookie is set (SetLocale)

        $this->get('/')
            ->assertOk()
            ->assertSee(route('attachments.show', $en))
            ->assertDontSee(route('attachments.show', $fr))
            ->assertDontSee('(.docx)'); // No DOCX uploaded, no link
    }

    public function test_non_managers_cannot_upload_or_delete(): void
    {
        $member = User::factory()->create(['role' => Role::MEMBER]);
        $attachment = $this->study->attachments()->create(['locale' => 'en_CA', 'type' => 'lecture', 'filename' => 'jn_01.lec', 'extension' => 'pdf']);

        $this->actingAs($member)->post(route('attachments.store', $this->study), [])->assertForbidden();
        $this->actingAs($member)->delete(route('attachments.destroy', $attachment))->assertForbidden();

        $this->assertModelExists($attachment);
    }
}
