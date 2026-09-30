<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
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

        return view('pages.events.index', compact('upcoming', 'past', 'show', 'search'));
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
