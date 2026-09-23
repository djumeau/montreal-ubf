<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\BibleBook;
use App\Models\StudySeries;
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
        $users = User::orderBy('name')->paginate(10);
        $adminCount = User::where('role', Role::ADMIN)->count();
        return view('pages.dashboards.manage-users', compact('user', 'users', 'adminCount'));
    }

    // @desc Show the manage study series page
    // @route GET /manage-series
    public function studySeries(): View
    {
        $user = Auth::user();
        $seriesList = StudySeries::withCount('bibleStudies')
            ->with('book')
            ->orderBy('id')
            ->paginate(10);
        $books = BibleBook::orderBy('id')->get(); // Canonical order, for the Related Book select
        return view('pages.dashboards.manage-series', compact('user', 'seriesList', 'books'));
    }

}
