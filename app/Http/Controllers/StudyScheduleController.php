<?php

namespace App\Http\Controllers;

use App\Enums\EventCategory;
use App\Models\BibleStudy;
use App\Models\Event;
use App\Models\StudySeries;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudyScheduleController extends Controller
{
    // Schedule views: both show a week, Sunday to Saturday
    private const VIEWS = ['week', 'list'];

    // What the public schedule shows: group Bible studies (default), or events and conferences
    private const TYPES = ['studies', 'events'];

    // @desc Show the group Bible study schedule for a week
    // @route GET /bible-study-schedule
    public function index(Request $request): View
    {
        $period = $this->period($request);

        // ?type=studies|events (anything else is studies): group Bible studies, or events and conferences
        $type = in_array($request->query('type'), self::TYPES, true) ? $request->query('type') : 'studies';

        // Visitors and members only see the studies / events open to their profile
        $studies = $this->studies($period['start'], $period['end'], $type)->visibleTo($request->user())->get();

        return view('pages.bible-study-schedule.index', ['studies' => $studies, 'type' => $type] + $period);
    }

    // @desc Show the schedule on the admin dashboard, where every event (Bible studies included) is added and edited
    // @route GET /manage-schedule
    public function manage(Request $request): View
    {
        $user = Auth::user();
        $period = $this->period($request);

        // Every event of the period, whatever its category or minimum profile
        $studies = $this->events($period['start'], $period['end'])->get();

        // For the Event modal: the series select, and the Bible studies it narrows down
        $seriesList = StudySeries::orderBy('id')->get();
        $bibleStudies = BibleStudy::with('book')->orderBy('id')->get();

        return view('pages.dashboards.manage-study-schedule', compact('user', 'studies', 'seriesList', 'bibleStudies') + $period);
    }

    // @desc Add an event to the schedule
    // @route POST /manage-schedule
    public function store(Request $request): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        // Named error bag so validation errors reopen the Event modal
        $validated = $request->validateWithBag('saveSchedule', $this->rules($request), [], $this->attributes());

        $event = Event::create($this->eventFields($validated));

        return back()->with('status', __('dashboard/manage-study-schedule/index.schedule_event_created'))
            ->with('warning', $this->overlapWarning($event));
    }

    // @desc Update an event of the schedule
    // @route PUT /manage-schedule/{event}
    public function update(Request $request, Event $event): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        $validated = $request->validateWithBag('saveSchedule', $this->rules($request), [], $this->attributes());

        $event->update($this->eventFields($validated));

        return back()->with('status', __('dashboard/manage-study-schedule/index.schedule_event_updated'))
            ->with('warning', $this->overlapWarning($event));
    }

    // @desc Delete an event of the schedule; its images and attachments too when "delete_files" is ticked
    // @route DELETE /manage-schedule/{event}
    public function destroy(Request $request, Event $event): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        // Attachment rows always go with it (cascadeOnDelete on event_attachments.event_id);
        // the files only when asked for, otherwise they stay in the event's folders
        if ($request->boolean('delete_files')) {
            $event->deleteFiles();
        }

        $name = $event->current_title ?: $event->category->label();
        $event->delete();

        return back()->with('status', __('dashboard/manage-study-schedule/index.schedule_event_deleted', ['name' => $name]));
    }

    // @desc Copy the recurring events of the week shown to the following week, then show that week
    // @route POST /manage-schedule/copy-week
    public function copyWeek(Request $request): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        // ?date= (any day of the week to copy) and ?view= are on the form's action, read as on the page
        $period = $this->period($request);

        $recurring = $this->events($period['start'], $period['end'])->where('recurring', true)->get();
        $nextWeek = $this->events($period['next'], $period['next']->copy()->addDays(6))->get();

        $copied = 0;
        $skipped = []; // Not copied, e.g. "Genesis (Friday, October 9th, 2026, 8:00 PM)"
        foreach ($recurring as $event) {
            $startDate = $event->start_date->copy()->addWeek();

            // The following week already has an event starting that day at that time (copied before, or added by hand,
            // whatever it is): it is kept as it is and nothing is copied over or next to it.
            // $nextWeek was read before copying, so two events copied together at the same time do not block each other
            if ($nextWeek->contains(fn (Event $other) => $other->start_date->equalTo($startDate))) {
                $skipped[] = ($event->current_title ?: $event->category->label())
                    . ' (' . ucfirst($startDate->isoFormat(__('bible-study-schedule/index.list_day_format')))
                    . ', ' . $startDate->isoFormat(__('bible-study-schedule/index.time_format')) . ')';

                continue;
            }

            // Images and attachments stay with the original: their folders are named after its start date
            $copy = $event->replicate(['images']);
            $copy->start_date = $startDate;
            $copy->end_date = $event->end_date?->copy()->addWeek();
            $copy->save();
            $copied++;
        }

        $status = trans_choice('dashboard/manage-study-schedule/index.schedule_week_copied', $copied, ['count' => $copied]);
        $warning = $skipped
            ? trans_choice('dashboard/manage-study-schedule/index.schedule_week_skipped', count($skipped), ['events' => implode(', ', $skipped)])
            : null;

        // Back to Manage Schedule (the page the form is on), on the following week
        $query = http_build_query(['view' => $period['view'], 'date' => $period['next']->toDateString()]);

        return redirect()->to(strtok(url()->previous(), '?') . '?' . $query)->with('status', $status)->with('warning', $warning);
    }

    /**
     * Warning shown after saving an event at the same time as others that day, naming them
     * (e.g. "… Genesis (20 h 00 – 21 h 30), Prayer meeting (19 h 00 – 20 h 30)."); null without any.
     * The event is saved all the same: two groups can meet at the same time.
     */
    private function overlapWarning(Event $event): ?string
    {
        $event->refresh(); // Dates as stored, cast to Carbon

        $timeFormat = __('bible-study-schedule/index.time_format');

        $overlapping = Event::whereDate('start_date', $event->start_date)
            ->whereKeyNot($event->id)
            ->orderBy('start_date')
            ->get()
            ->filter(fn (Event $other) => $event->overlapsOnSchedule($other))
            ->map(fn (Event $other) => ($other->current_title ?: $other->category->label())
                . ' (' . $other->start_date->isoFormat($timeFormat) . ' – ' . $other->scheduleEnd()->isoFormat($timeFormat) . ')');

        return $overlapping->isEmpty() ? null : __('dashboard/manage-study-schedule/index.schedule_event_overlaps', ['events' => $overlapping->implode(', ')]);
    }

    /**
     * Validation rules shared by store() and update(): the Event modal's fields.
     */
    private function rules(Request $request): array
    {
        // The end time only has to follow the start time when the event ends on the day it starts
        $endsSameDay = !$request->filled('end_day') || $request->input('end_day') === $request->input('date');

        return [
            'category' => ['required', Rule::enum(EventCategory::class)],
            'title_en' => ['nullable', 'string', 'max:80'],
            'title_fr' => ['nullable', 'string', 'max:80'],
            'date' => ['required', 'date_format:Y-m-d'],
            'end_day' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date'], // Empty = ends the day it starts
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => array_filter(['nullable', 'required_with:end_day', 'date_format:H:i', $endsSameDay ? 'after:start_time' : null]), // Empty = no end time
            'bible_study_id' => ['nullable', 'integer', 'exists:bible_studies,id'],
            'contact_name' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:1024'],
            'color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'recurring' => ['nullable', 'boolean'],
            'minimum_profile' => ['required', Rule::in(array_column(Event::MINIMUM_PROFILES, 'value'))],
        ];
    }

    /**
     * Field names shown in the validation messages, in the current language.
     */
    private function attributes(): array
    {
        return [
            'category' => __('dashboard/manage-study-schedule/index.event_type'),
            'title_en' => __('dashboard/index.title_en'),
            'title_fr' => __('dashboard/index.title_fr'),
            'date' => __('dashboard/manage-study-schedule/index.date'),
            'end_day' => __('dashboard/manage-study-schedule/index.end_day'),
            'start_time' => __('dashboard/manage-study-schedule/index.start_time'),
            'end_time' => __('dashboard/manage-study-schedule/index.end_time'),
            'bible_study_id' => __('dashboard/manage-study-schedule/index.schedule_bible_study'),
            'contact_name' => __('dashboard/manage-study-schedule/index.leader'),
            'location' => __('dashboard/manage-study-schedule/index.location'),
            'color' => __('dashboard/manage-study-schedule/index.colour'),
            'recurring' => __('dashboard/manage-study-schedule/index.recurring'),
            'minimum_profile' => __('dashboard/manage-study-schedule/index.minimum_profile'),
        ];
    }

    /**
     * Event columns from validated input: the dates and times become start_date / end_date,
     * and only group Bible studies keep a Bible study.
     */
    private function eventFields(array $validated): array
    {
        $endTime = $validated['end_time'] ?? null;
        $endDay = $validated['end_day'] ?? $validated['date'];
        $isBibleStudy = in_array(EventCategory::from($validated['category']), EventCategory::WITH_BIBLE_STUDY, true);

        return [
            'category' => $validated['category'],
            'title_en' => $validated['title_en'] ?? null,
            'title_fr' => $validated['title_fr'] ?? null,
            'start_date' => $validated['date'] . ' ' . $validated['start_time'],
            'has_end_date' => (bool) $endTime,
            'end_date' => $endTime ? $endDay . ' ' . $endTime : null,
            'bible_study_id' => $isBibleStudy ? ($validated['bible_study_id'] ?? null) : null,
            'contact_name' => $validated['contact_name'] ?? null,
            'location' => $validated['location'] ?? null,
            'color' => isset($validated['color']) ? strtoupper($validated['color']) : null,
            'recurring' => (bool) ($validated['recurring'] ?? false),
            'minimum_profile' => $validated['minimum_profile'],
        ];
    }

    /**
     * Every event starting between the first and last day shown, in start order (Manage Schedule).
     */
    private function events(Carbon $start, Carbon $end): Builder
    {
        return Event::with('bibleStudy.book')
            ->whereBetween('start_date', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->orderBy('start_date');
    }

    /**
     * Group Bible studies and Sunday worship services (or, for the "events" type, events, conferences and
     * Sunday worship services) starting between the first and last day shown, in start order.
     * Recurring ones only show on their start date for now.
     */
    private function studies(Carbon $start, Carbon $end, string $type = 'studies'): Builder
    {
        // "Bible Studies" also shows the Sunday worship services, which are listed under "Events and Conferences" too
        $studies = Event::whereIn('category', array_map(fn (EventCategory $category) => $category->value, EventCategory::WITH_BIBLE_STUDY));

        return ($type === 'events' ? Event::publicListing() : $studies)
            ->with('bibleStudy.book')
            ->whereBetween('start_date', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->orderBy('start_date');
    }

    /**
     * Period shown by both schedule pages: view, start, end, previous, next and rangeLabel.
     */
    private function period(Request $request): array
    {
        // ?view=week|list (anything else is week) and ?date=YYYY-MM-DD (anything else is today)
        $view = in_array($request->query('view'), self::VIEWS, true) ? $request->query('view') : 'week';
        $date = rescue(fn () => $request->date('date'), null, false) ?? today();

        $start = $date->copy()->startOfWeek(CarbonInterface::SUNDAY);
        $end = $start->copy()->addDays(6);
        $previous = $start->copy()->subWeek();
        $next = $start->copy()->addWeek();

        $rangeLabel = $this->weekLabel($start, $end);

        return compact('view', 'start', 'end', 'previous', 'next', 'rangeLabel');
    }

    /**
     * Week range in the current language, shortest form that stays clear, e.g.
     * "21 – 27 septembre 2026", "28 sept. – 4 oct. 2026", "29 déc. 2026 – 4 janv. 2027".
     */
    private function weekLabel(Carbon $start, Carbon $end): string
    {
        $key = match (true) {
            $start->isSameMonth($end) => 'range_same_month',
            $start->isSameYear($end) => 'range_same_year',
            default => 'range_other_year',
        };

        [$startFormat, $endFormat] = __('bible-study-schedule/index.' . $key);

        return $start->isoFormat($startFormat) . ' – ' . $end->isoFormat($endFormat);
    }
}
