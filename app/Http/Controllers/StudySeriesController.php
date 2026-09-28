<?php

namespace App\Http\Controllers;

use App\Models\StudySeries;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class StudySeriesController extends Controller
{
    // Image slots stored in the "images" JSON column
    private const IMAGE_TYPES = ['desktop', 'mobile', 'thumbnail'];

    // @desc Create a new study series
    // @route POST /manage-series
    public function store(Request $request): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        // Named error bag so validation errors reopen the Add modal (not the Edit modal)
        $validated = $request->validateWithBag('createSeries', $this->rules());

        // Save first, then store the images in its folder (images/{series}, e.g. images/john_2026)
        $series = StudySeries::create([
            'name_en' => $validated['name_en'],
            'name_fr' => $validated['name_fr'],
            'book_id' => $validated['book_id'] ?? null,
            'dates' => $validated['dates'] ?? null,
        ]);

        $series->update([
            'images' => $this->storeImages($request, $series),
        ]);

        return back()->with('status', __('dashboard/index.series_created', ['name' => $series->name_en]));
    }

    // @desc Update an existing study series
    // @route PUT /manage-series/{series}
    public function update(Request $request, StudySeries $series): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        $validated = $request->validateWithBag('updateSeries', $this->rules());

        // Images and documents live in images/{series}/... and documents/{series}/..., named from the book and start year
        // (e.g. john_2026), so capture the folders before the change and move the files after
        $studies = $series->bibleStudies()->with('attachments')->get();
        $oldStudyDirectories = $studies->mapWithKeys(fn ($study) => [$study->id => [$study->imageDirectory(), $study->documentDirectory()]]);
        $oldDirectory = $series->imageDirectory();

        $series->fill([
            'name_en' => $validated['name_en'],
            'name_fr' => $validated['name_fr'],
            'book_id' => $validated['book_id'] ?? null,
            'dates' => $validated['dates'] ?? null,
        ]);

        // Studies first: the old series folder is removed once the series images leave too
        foreach ($studies as $study) {
            $study->setRelation('series', $series);
            $study->moveImagesFrom($oldStudyDirectories[$study->id][0]);
            $study->moveDocumentsFrom($oldStudyDirectories[$study->id][1]);
        }
        $series->moveImagesFrom($oldDirectory);

        $series->images = $this->storeImages($request, $series);
        $series->save();

        return back()->with('status', __('dashboard/index.series_updated', ['name' => $series->name_en]));
    }

    // @desc Delete a study series and its image folder
    // @route DELETE /manage-series/{series}
    public function destroy(Request $request, StudySeries $series): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        // Bible studies in this series are kept; the foreign key sets study_series_id to null,
        // so move their images and documents from {series}/{book}_{passage} to no_series/{book}_{passage}
        // (the emptied series folders are removed once the last files leave)
        foreach ($series->bibleStudies()->with('attachments')->get() as $study) {
            $oldDirectory = $study->imageDirectory();
            $oldDocumentDirectory = $study->documentDirectory();
            $study->study_series_id = null;
            $study->moveImagesFrom($oldDirectory);
            $study->moveDocumentsFrom($oldDocumentDirectory);
        }

        $series->deleteImages();

        $name = $series->name_en;
        $series->delete();

        return back()->with('status', __('dashboard/index.series_deleted', ['name' => $name]));
    }

    /**
     * Validation rules shared by store() and update().
     */
    private function rules(): array
    {
        $rules = [
            'name_en' => ['required', 'string', 'max:255'],
            'name_fr' => ['required', 'string', 'max:255'],
            'book_id' => ['nullable', 'integer', 'exists:bible_books,id'], // Empty = multiple books
            'dates' => ['nullable', 'string', 'max:255'],
        ];

        foreach (self::IMAGE_TYPES as $type) {
            $rules[$type] = ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'];
        }

        return $rules;
    }

    /**
     * Save any uploaded images to storage/app/public/images/{series}
     * and return the merged "images" array. Slots without an upload keep their current file.
     */
    private function storeImages(Request $request, StudySeries $series): array
    {
        $images = $series->images ?? [];
        $disk = Storage::disk('public');

        foreach (self::IMAGE_TYPES as $type) {
            if (!$request->hasFile($type)) {
                continue;
            }

            // Remove the file being replaced to keep storage clean
            if (!empty($images[$type])) {
                $disk->delete($series->imageDirectory() . '/' . $images[$type]);
            }

            // Timestamp in the name so browsers don't show a cached copy of the old image
            $file = $request->file($type);
            $filename = $type . '-' . now()->timestamp . '.' . $file->getClientOriginalExtension();

            $file->storeAs($series->imageDirectory(), $filename, 'public');

            $images[$type] = $filename;
        }

        return $images;
    }
}
