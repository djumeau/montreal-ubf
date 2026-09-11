<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

use Illuminate\Support\Facades\Storage;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DashboardController extends Controller
{
    // @desc Show the user dashboard page
    // @route GET /user-dashboard
    public function index(): View
    {
        $user = Auth::user();
        return view('pages.dashboards.index', compact('user'));
    }

    // @desc Create a new User
    // @route Put /user-dashboard/update.password
    public function addUser(Request $request)
    {
        // $validated = $request->validate([

        // ]);

        return back()->with('status', 'created_new_user');
    }

    // @desc Update the user password
    // @route Put /user-dashboard/update.password
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

    // @desc Securely stream the user's private avatar file.
    // @route Get /user-dashboard/show-avatar
    public function showAvatar(Request $request): BinaryFileResponse|Response
    {
        $user = $request->user();

        // 1. Check if the user has an avatar path saved and that the file exists
        if (! $user->avatar || ! Storage::disk('local')->exists($user->avatar)) {
            abort(404, 'Avatar not found.');
        }

        // 2. Stream the file directly from their private directory
        return response()->file(
            Storage::disk('local')->path($user->avatar)
        );

    }

    // @desc Update the user password
    // @route Put /user-dashboard/update.password
    public function updateAvatar(Request $request)
    {
        // 1. Validate file input
        $validated = $request->validate([
            'avatar_file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = $request->user();

        // 2. Delete old avatar file
        if ($user->avatar) {
            Storage::disk('local')->delete($user->avatar);
        }

        // 3. Save the file in a private 'avatar' folder on the 'local' disk
        $path = $request->file('avatar')->store('avatar', 'local');

        $user->update([
            'avatar' => 'path',
        ]);

        return back()->with('status', 'avatar updated');

    }

}
