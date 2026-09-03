<?php

namespace App\Http\Controllers;

use App\Enums\UserPrivilege;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Enum;

class RegisterController extends Controller
{
    // @desc Show the registration form
    // @route GET /register
    public function register(): View
    {
        return view('pages.auth.register');
    }

    // @desc Store User registration data
    // @route POST /register
    public function store(Request $request): RedirectResponse
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:50|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'privileges' => ['nullable', new Enum(UserPrivilege::class)], // Optional
        ]);

        // Hash the password before storing it
        $validatedData['password'] = Hash::make($validatedData['password']);

        // Create a new user
        $user = User::create($validatedData);

        // Redirect to login pae after registration
        return redirect()->route('login')->with('success', __('auth/index.registered_successfully'));
    }

}
