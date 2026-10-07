<?php

namespace App\Http\Controllers;

use App\Enums\PrayerCategory;
use App\Enums\Role;
use App\Models\PrayerTopic;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

class PrayerTopicsController extends Controller
{
    // @desc Show the manage prayer topics page: main topics in their chosen order, each followed by its subtopics
    // @route GET /manage-prayer-topics
    public function index(Request $request): View
    {
        // Topics can be reserved for a role: refused below the management roles, not only hidden in the page
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        $user = $request->user();

        // 10 main topics per page; their subtopics come with them, whatever their number
        $prayerTopics = PrayerTopic::whereNull('parent_id')
            ->with('subtopics')
            ->ordered()
            ->paginate(10);

        // Every main topic, for the "Subtopic of" select
        $mainTopics = PrayerTopic::whereNull('parent_id')->ordered()->get();

        return view('pages.dashboards.manage-prayer-topics', compact('user', 'prayerTopics', 'mainTopics'));
    }

    // @desc Create a new prayer topic
    // @route POST /manage-prayer-topics
    public function store(Request $request): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        // Named error bag so validation errors reopen the Add modal (not the Edit modal)
        $validated = $request->validateWithBag('createPrayerTopic', $this->rules());

        $attributes = $this->attributes($request, $validated);

        // A new main topic goes to the top of the list; a new subtopic goes under the last one of its main topic
        $prayerTopic = PrayerTopic::create($attributes + ['position' => PrayerTopic::newPosition($attributes['parent_id'])]);

        // Saved first: the image file is named from the topic's id
        if ($request->hasFile('image')) {
            $prayerTopic->update(['image' => $this->storeImage($request, $prayerTopic)]);
        }

        return back()->with('status', __('dashboard/manage-prayer-topics/index.topic_created'));
    }

    // @desc Update an existing prayer topic
    // @route PUT /manage-prayer-topics/{prayerTopic}
    public function update(Request $request, PrayerTopic $prayerTopic): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        $validated = $request->validateWithBag('updatePrayerTopic', $this->rules($prayerTopic));

        $prayerTopic->fill($this->attributes($request, $validated));

        // Moved under another main topic (or made a main topic): placed as a new topic would be in that list
        if ($prayerTopic->isDirty('parent_id')) {
            $prayerTopic->position = PrayerTopic::newPosition($prayerTopic->parent_id);
        }

        // A new image replaces the current one; "Remove the current image" only applies without a new one
        if ($request->hasFile('image')) {
            $prayerTopic->deleteImage();
            $prayerTopic->image = $this->storeImage($request, $prayerTopic);
        } elseif ($request->boolean('remove_image')) {
            $prayerTopic->deleteImage();
            $prayerTopic->image = null;
        }

        $prayerTopic->save();

        return back()->with('status', __('dashboard/manage-prayer-topics/index.topic_updated'));
    }

    // @desc Move a prayer topic one place up or down: among the main topics, or among the subtopics of its main topic
    // @route PUT /manage-prayer-topics/{prayerTopic}/move
    public function move(Request $request, PrayerTopic $prayerTopic): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        $validated = $request->validate([
            'direction' => ['required', Rule::in(['up', 'down'])],
        ]);

        // The topics listed with this one, in their current order
        $siblings = PrayerTopic::where('parent_id', $prayerTopic->parent_id)->ordered()->get()->values();

        $from = $siblings->search(fn (PrayerTopic $sibling) => $sibling->is($prayerTopic));
        $to = $validated['direction'] === 'up' ? $from - 1 : $from + 1;

        // Already first (or last): nothing to move
        if (!$siblings->has($to)) {
            return back();
        }

        // Swap with its neighbour, then renumber the whole list from 1 so the positions stay distinct
        [$siblings[$from], $siblings[$to]] = [$siblings[$to], $siblings[$from]];

        DB::transaction(function () use ($siblings) {
            foreach ($siblings as $index => $sibling) {
                // Query builder: a change of order is not an update of the topic (updated_at is kept)
                PrayerTopic::whereKey($sibling->id)->toBase()->update(['position' => $index + 1]);
            }
        });

        return back();
    }

    // @desc Delete a prayer topic; its subtopics are kept and become main topics, at the top of the list in the order they had
    // @route DELETE /manage-prayer-topics/{prayerTopic}
    public function destroy(Request $request, PrayerTopic $prayerTopic): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        DB::transaction(function () use ($prayerTopic) {
            // Last subtopic first: each one goes to the top, so the first subtopic ends up first
            foreach ($prayerTopic->subtopics->reverse() as $subtopic) {
                $subtopic->update(['parent_id' => null, 'position' => PrayerTopic::newPosition(null)]);
            }

            $prayerTopic->delete();
        });

        $prayerTopic->deleteImage();

        return back()->with('status', __('dashboard/manage-prayer-topics/index.topic_deleted'));
    }

    /**
     * Validation rules shared by store() and update().
     */
    private function rules(?PrayerTopic $prayerTopic = null): array
    {
        // One level only: the parent must be a main topic, other than the topic itself
        $parent = [
            'nullable',
            'integer',
            Rule::exists('prayer_topics', 'id')->whereNull('parent_id'),
        ];

        if ($prayerTopic) {
            $parent[] = Rule::notIn([$prayerTopic->id]);

            // A topic that has subtopics stays a main topic
            if ($prayerTopic->subtopics()->exists()) {
                $parent[] = 'prohibited';
            }
        }

        return [
            'parent_id' => $parent,
            'topic_en' => ['required', 'string', 'max:2048'],
            'topic_fr' => ['required', 'string', 'max:2048'],
            'category' => ['required', new Enum(PrayerCategory::class)],
            'min_role' => ['required', new Enum(Role::class)],
            'url' => ['nullable', 'url:http,https', 'max:2048'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    /**
     * Save the uploaded image to storage/app/public/images/prayer-topics and return its file name.
     */
    private function storeImage(Request $request, PrayerTopic $prayerTopic): string
    {
        // Timestamp in the name so browsers don't show a cached copy of the old image
        $file = $request->file('image');
        $filename = $prayerTopic->id . '-' . now()->timestamp . '.' . $file->extension();

        $file->storeAs(PrayerTopic::IMAGE_DIRECTORY, $filename, 'public');

        return $filename;
    }

    /**
     * Column values from the validated form; an unticked Answered checkbox is not sent, so it is read as false.
     */
    private function attributes(Request $request, array $validated): array
    {
        return [
            'parent_id' => $validated['parent_id'] ?? null,
            'topic_en' => $validated['topic_en'],
            'topic_fr' => $validated['topic_fr'],
            'category' => $validated['category'],
            'min_role' => $validated['min_role'],
            'url' => $validated['url'] ?? null,
            'answered' => $request->boolean('answered'),
        ];
    }
}
