<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

use Illuminate\View\View;

use App\Models\BibleBook;
use App\Models\BibleStudy;
use App\Models\StudyAttachment;
use App\Models\StudySeries;

class BibleStudyController extends Controller
{
    // Image slots stored in the "image_links" JSON column (shared by EN and FR)
    private const IMAGE_TYPES = ['square', 'desktop', 'mobile'];

    // @desc Show Bible studies as cards, with the same search and filters as Manage Studies (view only)
    // @route GET /bible-studies
    public function index(Request $request): View
    {
        // Optional ?series={id} and ?book={id} filters; ignored if they don't exist
        $currentSeries = StudySeries::find($request->integer('series')) ?: null;
        $currentBook = BibleBook::find($request->integer('book')) ?: null;

        // Optional ?q= search (see BibleStudy::scopeFilter)
        $search = trim($request->string('q'));

        // Files in the current language only; question sheets for everyone, every type from the User role up
        $locale = app()->getLocale() === 'fr_CA' ? 'fr_CA' : 'en_CA';
        $allTypes = (bool) $request->user()?->canViewAllAttachments();

        $studies = BibleStudy::with([
                'series',
                'book',
                'attachments' => fn ($query) => $query->where('locale', $locale)
                    ->when(!$allTypes, fn ($query) => $query->whereIn('type', StudyAttachment::PUBLIC_TYPES))
                    ->orderBy('filename')
                    ->orderBy('extension'),
            ])
            ->filter($currentSeries, $currentBook, $search)
            ->orderBy('id')
            ->paginate(12) // 3 rows of 4 cards
            ->withQueryString(); // Keep ?series=, ?book= and ?q= on the pagination links

        // Study counts show next to each name in the filter dropdowns, e.g. "The Gospel of John (7)"
        $seriesList = StudySeries::withCount('bibleStudies')->orderBy('id')->get();
        $books = BibleBook::withCount('bibleStudies')->orderBy('id')->get(); // Canonical order

        // Banner: the filtered series' desktop image, else the default series image
        $heroImage = $currentSeries?->imageUrl('desktop') ?? asset('storage/images/study-series/default-desktop.jpg');

        return view('pages.bible-studies.index', compact('studies', 'currentSeries', 'currentBook', 'search', 'seriesList', 'books', 'heroImage'));
    }

    // @desc Show bible study id
    // @route GET /bible-studies/{id}
    public function show(int $id):View
    {
        $biblestudy = BibleStudy::findOrFail($id);

        return view('pages.bible-studies.show')->with('biblestudy', $biblestudy);
    }

    // @desc Create bible study lesson
    // @route GET /bible-studies/create
    public function create(): View
    {
        return view('pages.bible-studies.create');
    }

    // @desc Create a new Bible study
    // @route POST /manage-studies
    public function store(Request $request): RedirectResponse
    {
        // Double layer check at the controller endpoint (?-> because the public /bible-studies/store route has no auth)
        if (!$request->user()?->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        // Named error bag so validation errors reopen the Add modal (not the Edit modal)
        $validated = $request->validateWithBag('createStudy', $this->rules());

        // Save first so the study has an id for its image folder
        $study = BibleStudy::create($this->studyFields($validated));

        $study->update([
            'image_links' => $this->storeImages($request, $study),
        ]);

        return back()->with('status', __('dashboard/index.study_created', ['name' => $this->displayName($study)]));
    }

    // @desc Update an existing Bible study
    // @route PUT /manage-studies/{study}
    public function update(Request $request, BibleStudy $study): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        $validated = $request->validateWithBag('updateStudy', $this->rules());

        // Images and documents live under the series folder, so a new series means moving them
        $oldDirectory = $study->imageDirectory();
        $oldDocumentDirectories = $study->documentDirectories();
        $study->fill($this->studyFields($validated));
        $study->moveImagesFrom($oldDirectory);
        $study->moveDocumentsFrom($oldDocumentDirectories);

        $study->image_links = $this->storeImages($request, $study);
        $study->save();

        return back()->with('status', __('dashboard/index.study_updated', ['name' => $this->displayName($study)]));
    }

    // @desc Delete a Bible study with its image and document folders
    // @route DELETE /manage-studies/{study}
    public function destroy(Request $request, BibleStudy $study): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        // Attachment rows go with it (cascadeOnDelete on study_attachments.bible_study_id); their files don't
        Storage::disk('public')->deleteDirectory($study->imageDirectory());
        $study->deleteDocuments();

        $name = $this->displayName($study);
        $study->delete();

        return back()->with('status', __('dashboard/index.study_deleted', ['name' => $name]));
    }

    /**
     * Validation rules shared by store() and update(); every field is optional.
     */
    private function rules(): array
    {
        $rules = [
            'study_series_id' => ['nullable', 'integer', 'exists:study_series,id'], // Empty = no series
            'book_id' => ['nullable', 'integer', 'exists:bible_books,id'],
            'bible_passage' => ['nullable', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'title_fr' => ['nullable', 'string', 'max:255'],
        ];

        foreach (self::IMAGE_TYPES as $type) {
            $rules[$type] = ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'];
        }

        return $rules;
    }

    /**
     * Study columns from validated input; missing optional fields become null.
     */
    private function studyFields(array $validated): array
    {
        return collect(['study_series_id', 'book_id', 'bible_passage', 'title_en', 'title_fr'])
            ->mapWithKeys(fn ($field) => [$field => $validated[$field] ?? null])
            ->all();
    }

    /**
     * Save any uploaded images to storage/app/public/images/series_{id}/study_{id}
     * and return the merged "image_links" array. Slots without an upload keep their current file.
     */
    private function storeImages(Request $request, BibleStudy $study): array
    {
        $images = $study->image_links ?? [];
        $disk = Storage::disk('public');

        foreach (self::IMAGE_TYPES as $type) {
            if (!$request->hasFile($type)) {
                continue;
            }

            // Remove the file being replaced to keep storage clean
            if (!empty($images[$type])) {
                $disk->delete($study->imageDirectory() . '/' . $images[$type]);
            }

            // Timestamp in the name so browsers don't show a cached copy of the old image
            $file = $request->file($type);
            $filename = $type . '-' . now()->timestamp . '.' . $file->getClientOriginalExtension();

            $file->storeAs($study->imageDirectory(), $filename, 'public');

            $images[$type] = $filename;
        }

        return $images;
    }

    /**
     * Name used in status messages: the title in the current locale, else the other one, else "#id".
     */
    private function displayName(BibleStudy $study): string
    {
        return $study->current_title ?: ($study->title_en ?: ($study->title_fr ?: "#{$study->id}"));
    }

}
