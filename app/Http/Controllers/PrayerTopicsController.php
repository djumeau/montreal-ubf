<?php

namespace App\Http\Controllers;

use App\Enums\PrayerCategory;
use App\Enums\Role;
use App\Models\PrayerTopic;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

class PrayerTopicsController extends Controller
{
    // @desc Show the manage prayer topics page: main topics, newest first, each followed by its subtopics
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
            ->latest()
            ->orderByDesc('id')
            ->paginate(10);

        // Every main topic, for the "Subtopic of" select
        $mainTopics = PrayerTopic::whereNull('parent_id')->latest()->orderByDesc('id')->get();

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

        PrayerTopic::create($this->attributes($request, $validated));

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

        $prayerTopic->update($this->attributes($request, $validated));

        return back()->with('status', __('dashboard/manage-prayer-topics/index.topic_updated'));
    }

    // @desc Delete a prayer topic; its subtopics are kept and become main topics (the foreign key sets their parent_id to null)
    // @route DELETE /manage-prayer-topics/{prayerTopic}
    public function destroy(Request $request, PrayerTopic $prayerTopic): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        $prayerTopic->delete();

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
        ];
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
