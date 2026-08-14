<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class EventController extends Controller
{
    // @desc Show events page
    // @route GET /events
    public function index(): View
    {
        return view('pages.events');
    }
}
