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

    // @desc Show the group Bible study schedule for a week
    // @route GET /bible-study-schedule
    public function index(Request $request): View
    {
        $period = $this->period($request);

        // Visitors and members only see the studies open to their profile
        $studies = $this->studies($period['start'], $period['end'])->visibleTo($request->user())->get();

        return view('pages.bible-study-schedule.index', ['studies' => $studies] + $period);
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

        return back()->with('status', __('dashboard/index.schedule_event_created'))
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

        return back()->with('status', __('dashboard/index.schedule_event_updated'))
            ->with('warning', $this->overlapWarning($event));
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

        return $overlapping->isEmpty() ? null : __('dashboard/index.schedule_event_overlaps', ['events' => $overlapping->implode(', ')]);
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
            'category' => __('dashboard/index.event_type'),
            'title_en' => __('dashboard/index.title_en'),
            'title_fr' => __('dashboard/index.title_fr'),
            'date' => __('dashboard/index.date'),
            'end_day' => __('dashboard/index.end_day'),
            'start_time' => __('dashboard/index.start_time'),
            'end_time' => __('dashboard/index.end_time'),
            'bible_study_id' => __('dashboard/index.schedule_bible_study'),
            'contact_name' => __('dashboard/index.leader'),
            'location' => __('dashboard/index.location'),
            'color' => __('dashboard/index.colour'),
            'recurring' => __('dashboard/index.recurring'),
            'minimum_profile' => __('dashboard/index.minimum_profile'),
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
        $isBibleStudy = in_array(EventCategory::from($validated['category']), EventCategory::BIBLE_STUDIES, true);

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
     * Group Bible studies starting between the first and last day shown, in start order.
     * Recurring studies only show on their start date for now.
     */
    private function studies(Carbon $start, Carbon $end): Builder
    {
        return Event::bibleStudies()
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
