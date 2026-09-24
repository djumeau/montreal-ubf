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

    public function test_non_managers_cannot_upload_or_delete(): void
    {
        $member = User::factory()->create(['role' => Role::MEMBER]);
        $attachment = $this->study->attachments()->create(['locale' => 'en_CA', 'type' => 'lecture', 'filename' => 'jn_01.lec', 'extension' => 'pdf']);

        $this->actingAs($member)->post(route('attachments.store', $this->study), [])->assertForbidden();
        $this->actingAs($member)->delete(route('attachments.destroy', $attachment))->assertForbidden();

        $this->assertModelExists($attachment);
    }
}
