<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // @desc Show the login form
    // @route GET /login
    public function login(): View
    {
        return view('pages.auth.login');
    }

    // @desc Logout User
    // @route POST / Logout
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('home'));
    }

    // @desc Authenticate User
    // @route POST /login
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|string|email|max:50',
            'password' => 'required|string|min:8',
        ]);

        // Attempt to authenticate user
        if (Auth::attempt($credentials)) {
            //Regenerate the session to prevent fixation attacks.
            $request->session()->regenerate();
            return redirect()->intended(route('home'))->with('success', 'You are now logged in.');
        }

        //If auth fails, redirect with errors.
        return back()->withErrors([
            'email' => __('auth/index.validation.not_matched'),
        ])->onlyInput('email');
    }
}
