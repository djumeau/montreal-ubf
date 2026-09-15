<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // @desc Show the user dashboard page
    // @route GET /dashboard
    public function index(): View
    {
        $user = Auth::user();
        return view('pages.dashboards.profile', compact('user'));
    }

    // @desc Show the manage users page
    // @route GET /manage-users
    public function manageUsers(): View
    {
        $user = Auth::user();
        return view('pages.dashboards.manage-users', compact('user'));
    }

}
