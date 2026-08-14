<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RegisterController extends Controller
{
    // @desc Show the registration form
    // @route GET /register
    public function register(): View
    {
        return view('pages.auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create a new user (assuming you have a User model)
        $user = \App\Models\User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

        // Log the user in (optional)
        auth()->login($user);

        // Redirect to a desired page after registration
        return redirect()->route('home')->with('success', 'Registration successful!');
    }
}
