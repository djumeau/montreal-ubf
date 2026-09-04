<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', Password::defaults(), 'same:confirm_password'],
        ]);

        $user = $request->user();

        $user->password = $validated['new_password'];

        $user->save();

        return back()->with('status', 'password-updated');
    }

}
