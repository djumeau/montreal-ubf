<?php

namespace App\Http\Controllers;

use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class StudyScheduleController extends Controller
{
    // Schedule views: week and list show Sunday to Saturday, month the whole month
    private const VIEWS = ['week', 'month', 'list'];

    // @desc Show the group Bible study schedule for a week or a month
    // @route GET /bible-study-schedule
    public function index(Request $request): View
    {
        // ?view=week|month|list (anything else is week) and ?date=YYYY-MM-DD (anything else is today)
        $view = in_array($request->query('view'), self::VIEWS, true) ? $request->query('view') : 'week';
        $date = rescue(fn () => $request->date('date'), null, false) ?? today();

        if ($view === 'month') {
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();
            $previous = $start->copy()->subMonth();
            $next = $start->copy()->addMonth();
        } else {
            $start = $date->copy()->startOfWeek(CarbonInterface::SUNDAY);
            $end = $start->copy()->addDays(6);
            $previous = $start->copy()->subWeek();
            $next = $start->copy()->addWeek();
        }

        $rangeLabel = $view === 'month'
            ? $start->isoFormat(__('bible-study-schedule/index.month_format'))
            : $this->weekLabel($start, $end);

        return view('pages.bible-study-schedule.index', compact('view', 'start', 'end', 'previous', 'next', 'rangeLabel'));
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
