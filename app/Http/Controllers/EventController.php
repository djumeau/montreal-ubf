<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    // @desc Show events page: upcoming events (soonest first) and past events (latest first) the viewer's role allows
    // @route GET /events
    public function index(Request $request): View
    {
        $events = Event::visibleTo($request->user())->with('attachments');

        $upcoming = (clone $events)->upcoming()->orderBy('start_date')->get();
        $past = (clone $events)->whereNotIn('id', $upcoming->modelKeys())->orderByDesc('start_date')->get();

        return view('pages.events', compact('upcoming', 'past'));
    }
}
