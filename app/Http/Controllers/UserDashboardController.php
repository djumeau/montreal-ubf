<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    // @desc Show the user dashboard page
    // @route GET /user-dashboard
    public function index(): View
    {
        $user = Auth::user();
        return view('pages.dashboards.user-dashboard', compact('user'));
    }

}
