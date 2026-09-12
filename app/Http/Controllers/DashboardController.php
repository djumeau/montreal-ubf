<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // @desc Show the user dashboard page
    // @route GET /user-dashboard
    public function index(): View
    {
        $user = Auth::user();
        return view('pages.dashboards.index', compact('user'));
    }

}
