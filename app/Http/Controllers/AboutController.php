<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AboutController extends Controller
{
    // @desc Show the about page
    // @route GET /about
    public function index(): View
    {
        return view('pages.about');
    }
}
