<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class GivingController extends Controller
{
    // @desc Show the giving page
    // @route GET /giving
    public function index(): View
    {
        return view('pages.giving');
    }
}
