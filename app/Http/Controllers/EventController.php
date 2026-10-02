<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class EventController extends Controller
{
    // Events per page in each list (table rows from md up, cards below)
    private const PER_PAGE = 5;

    // @desc Show events page: upcoming events (soonest first) and/or past events (latest first) the viewer's role allows
    // @route GET /events
    public function index(Request $request): View
    {
        // ?show=all|upcoming|past (anything else is all) and optional ?q= search (see Event::scopeSearch)
        $show = in_array($request->query('show'), ['upcoming', 'past'], true) ? $request->query('show') : 'all';
        $search = trim($request->string('q'));

        // Attachments column: documents (not media) in the current language only
        $locale = app()->getLocale() === 'fr_CA' ? 'fr_CA' : 'en_CA';

        // Group Bible studies are left out (managed on the admin dashboard)
        $events = Event::visibleTo($request->user())
            ->publicListing()
            ->with('bibleStudy.book')
            ->withCount(['attachments as documents_count' => fn ($query) => $query->where('locale', $locale)->where('type', 'document')])
            ->search($search);


        // null = list not shown. The database search also matches HTML tags in descriptions (e.g. "li"),
        // so its results are checked again against the text without tags
        $upcoming = $show !== 'past' ? (clone $events)->upcoming()->orderBy('start_date')->get() : null;
        $past = $show !== 'upcoming' ? (clone $events)->past()->orderByDesc('start_date')->get() : null;

        if ($search !== '') {
            $upcoming = $upcoming?->filter(fn (Event $event) => $event->matchesSearch($search))->values();
            $past = $past?->filter(fn (Event $event) => $event->matchesSearch($search))->values();
        }

        // Each list has its own page number (?upcoming_page=, ?past_page=), so one can be paged without moving the other
        $upcoming = $upcoming ? $this->paginate($upcoming, 'upcoming') : null;
        $past = $past ? $this->paginate($past, 'past') : null;

        return view('pages.events.index', compact('upcoming', 'past', 'show', 'search'));
    }

    // Paginates an already loaded list (the search is finished in PHP, so the database cannot page it).
    // Links keep the query string (?show=, ?q=, the other list's page) and jump back to the list (#upcoming, #past)
    private function paginate(Collection $events, string $list): LengthAwarePaginator
    {
        $pageName = $list . '_page';
        $lastPage = max(1, (int) ceil($events->count() / self::PER_PAGE));
        $page = min(LengthAwarePaginator::resolveCurrentPage($pageName), $lastPage); // Past the end: show the last page

        return (new LengthAwarePaginator(
            $events->forPage($page, self::PER_PAGE)->values(),
            $events->count(),
            self::PER_PAGE,
            $page,
            ['path' => request()->url(), 'pageName' => $pageName],
        ))->withQueryString()->fragment($list);
    }

    // @desc Show one event with all its media and its documents in the current language
    // @route GET /events/{event}
    public function show(Request $request, Event $event): View
    {
        // Group Bible studies have no public page (managed on the admin dashboard)
        abort_if($event->isBibleStudy(), 404);

        // Same rule as the events list: below its minimum profile, visitors log in first (then come back here), accounts are refused
        if (! $event->isVisibleTo($request->user())) {
            $request->user() ? abort(403, __('home/index.unauthorized')) : throw new AuthenticationException();
        }

        $locale = app()->getLocale() === 'fr_CA' ? 'fr_CA' : 'en_CA';

        // Media show in both languages; documents only in the current one
        $event->load(['attachments' => fn ($query) => $query
            ->where(fn ($query) => $query->where('type', 'media')->orWhere('locale', $locale))
            ->orderBy('document_name')]);
        $event->attachments->each->setRelation('event', $event); // storage_path needs the event; avoids a query per file

        [$media, $documents] = $event->attachments->partition(fn ($attachment) => $attachment->type === 'media');

        return view('pages.events.event-details', compact('event', 'media', 'documents'));
    }
}
