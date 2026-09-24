<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\BibleBook;
use App\Models\BibleStudy;
use App\Models\StudySeries;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ManageStudiesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private BibleBook $john;
    private StudySeries $series;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::factory()->create(['role' => Role::ADMIN]);
        $this->john = BibleBook::create(['name_en' => 'John', 'abbreviation_en' => 'Jn', 'name_fr' => 'Jean', 'abbreviation_fr' => 'Jn', 'testament' => 'nt', 'chapters' => 21]);
        $this->series = StudySeries::create(['name_en' => "John's Gospel", 'name_fr' => "L'évangile de Jean", 'book_id' => $this->john->id]);
    }

    private function studyWithImages(): BibleStudy
    {
        $study = BibleStudy::create([
            'study_series_id' => $this->series->id,
            'book_id' => $this->john->id,
            'bible_passage' => '3:1-21',
            'title_en' => 'For God So Loved the World',
            'title_fr' => 'Car Dieu a tant aimé le monde',
            'image_links' => ['square' => 'sq.jpg', 'desktop' => 'dt.jpg', 'mobile' => 'mb.jpg'],
        ]);

        foreach ($study->image_links as $file) {
            Storage::disk('public')->put($study->imageDirectory() . '/' . $file, 'x');
        }

        return $study;
    }

    public function test_page_lists_studies_with_add_edit_and_delete_modals(): void
    {
        $study = $this->studyWithImages();

        $this->actingAs($this->admin)->get('/manage-studies')
            ->assertOk()
            ->assertSee($study->title_en)
            ->assertSee('openEdit(JSON.parse', false) // Row data (with the update / destroy URLs) is JSON-escaped by @js
            ->assertSee('openDelete(JSON.parse', false)
            ->assertSee(':action="deleteStudy.destroy_url"', false)
            ->assertSee('id="add_study_series_id"', false);
    }

    public function test_update_saves_fields_and_replaces_only_uploaded_images(): void
    {
        $study = $this->studyWithImages();

        $this->actingAs($this->admin)
            ->from('/manage-studies')
            ->put(route('study.update', $study), [
                'study_series_id' => $this->series->id,
                'book_id' => $this->john->id,
                'bible_passage' => '3:1-15',
                'title_en' => 'New title',
                'title_fr' => '',
                'square' => UploadedFile::fake()->image('new.jpg'),
            ])
            ->assertRedirect('/manage-studies')
            ->assertSessionHas('status');

        $study->refresh();
        $this->assertSame('3:1-15', $study->bible_passage);
        $this->assertSame('New title', $study->title_en);
        $this->assertNull($study->title_fr); // Optional field cleared
        $this->assertStringStartsWith('square-', $study->image_links['square']);
        $this->assertSame('dt.jpg', $study->image_links['desktop']); // Not uploaded, kept

        $disk = Storage::disk('public');
        $disk->assertMissing($study->imageDirectory() . '/sq.jpg'); // Replaced file removed
        $disk->assertExists($study->imageDirectory() . '/' . $study->image_links['square']);
    }

    public function test_update_with_all_fields_empty_is_allowed(): void
    {
        $study = $this->studyWithImages();

        $this->actingAs($this->admin)
            ->put(route('study.update', $study), [])
            ->assertSessionHasNoErrors();

        $study->refresh();
        $this->assertNull($study->study_series_id);
        $this->assertNull($study->title_en);
    }

    public function test_changing_series_moves_the_image_folder(): void
    {
        $study = $this->studyWithImages();
        $oldDirectory = $study->imageDirectory();
        $otherSeries = StudySeries::create(['name_en' => 'Joshua', 'name_fr' => 'Josué']);

        $this->actingAs($this->admin)
            ->put(route('study.update', $study), ['study_series_id' => $otherSeries->id])
            ->assertSessionHasNoErrors();

        $study->refresh();
        $disk = Storage::disk('public');
        $this->assertSame("images/series_{$otherSeries->id}/study_{$study->id}", $study->imageDirectory());
        $disk->assertExists($study->imageDirectory() . '/sq.jpg');
        $disk->assertExists($study->imageDirectory() . '/mb.jpg');
        $this->assertFalse($disk->directoryExists($oldDirectory));
    }

    public function test_invalid_update_uses_the_update_error_bag(): void
    {
        $study = $this->studyWithImages();

        $this->actingAs($this->admin)
            ->put(route('study.update', $study), ['book_id' => 999, 'study_id' => $study->id])
            ->assertSessionHasErrors('book_id', null, 'updateStudy');
    }

    public function test_store_creates_a_study_with_images(): void
    {
        $this->actingAs($this->admin)
            ->post(route('study.store'), [
                'study_series_id' => $this->series->id,
                'book_id' => $this->john->id,
                'bible_passage' => '4:1-26',
                'title_en' => 'The Woman at the Well',
                'desktop' => UploadedFile::fake()->image('d.png'),
            ])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        $study = BibleStudy::firstOrFail();
        $this->assertSame('4:1-26', $study->bible_passage);
        $this->assertArrayNotHasKey('square', $study->image_links);
        Storage::disk('public')->assertExists($study->imageDirectory() . '/' . $study->image_links['desktop']);
    }

    public function test_invalid_store_uses_the_create_error_bag(): void
    {
        $this->actingAs($this->admin)
            ->post(route('study.store'), ['square' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf')])
            ->assertSessionHasErrors('square', null, 'createStudy');

        $this->assertSame(0, BibleStudy::count());
    }

    public function test_destroy_deletes_study_attachments_and_images(): void
    {
        $study = $this->studyWithImages();
        $study->attachments()->create(['locale' => 'en_CA', 'type' => 'lecture', 'filename' => 'jn_03', 'extension' => 'pdf']);
        $directory = $study->imageDirectory();

        $this->actingAs($this->admin)
            ->delete(route('study.destroy', $study))
            ->assertSessionHas('status');

        $this->assertModelMissing($study);
        $this->assertDatabaseCount('study_attachments', 0);
        $this->assertFalse(Storage::disk('public')->directoryExists($directory));
    }

    public function test_deleting_a_series_moves_its_study_images_to_series_none(): void
    {
        $study = $this->studyWithImages();

        $this->actingAs($this->admin)
            ->delete(route('series.destroy', $this->series))
            ->assertSessionHas('status');

        $study->refresh();
        $this->assertNull($study->study_series_id);

        $disk = Storage::disk('public');
        $disk->assertExists("images/series_none/study_{$study->id}/sq.jpg");
        $this->assertFalse($disk->directoryExists("images/series_{$this->series->id}"));
    }

    public function test_non_managers_cannot_change_studies(): void
    {
        $study = $this->studyWithImages();
        $member = User::factory()->create(['role' => Role::MEMBER]);

        $this->actingAs($member)->put(route('study.update', $study), ['title_en' => 'Hacked'])->assertForbidden();
        $this->actingAs($member)->delete(route('study.destroy', $study))->assertForbidden();
        $this->actingAs($member)->post(route('study.store'), [])->assertForbidden();

        $this->assertSame('For God So Loved the World', $study->fresh()->title_en);
    }
}
