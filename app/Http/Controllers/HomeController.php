<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    // @desc Show home page (index)
    // @route GET /
    public function index(): View
    {
        return view('pages.index');
    }
}
