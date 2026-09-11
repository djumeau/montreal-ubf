<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    /**
     * Handle updating standard text profile options.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return back()->with('status', 'profile-updated');
    }

    /**
     * Handle asynchronous or modal incoming file upload specifically to private folder.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required',
                        'image',
                        'mimes:jpeg,png,jpg,webp',
                        'max:2048'],
        ]);

        $user = $request->user();

        if ($request->hasFile('avatar')) {
            // Remove old avatar file if present to save private storage clean space
            if ($user->avatar_path) {
                Storage::disk('private')->delete($user->avatar_path);
            }

            // Saves directly to storage/app/private/avatar via explicit custom local private disk allocation
            $path = $request->file('avatar')->store('avatar', 'private');

            $user->update([
                'avatar_path' => $path
            ]);
        }

        return back()->with('status', 'avatar-updated');
    }

    /**
     * Securely stream avatars from private storage.
     */
    public function streamAvatar(string $filename): Response
    {
        $path = 'avatar/' . $filename;

        $fullPath = Storage::disk('private')->path($path);

        // 1. Verify file existence on the private disk
        if (!Storage::disk('private')->exists($path)) {
            abort(404, 'Avatar not found.');
        }

        // This method dynamically constructs the correct Symfony Response object
        return response()->file($fullPath, [
            'Cache-Control' => 'private, max-age=86400, no-transform',
        ]);

    }

}