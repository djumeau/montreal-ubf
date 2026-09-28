<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    // @desc Show home page (index)
    // @route GET /
    public function index(): View
    {
        // The featured study under the hero is loaded by the study component (see layout.blade.php)
        return view('pages.index');
    }
}
