<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
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

        return back()->with('status', __('dashboard/index.profile_updated'));
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
            // 1. Extract the file extension dynamically (e.g., jpg, png)
            $extension = $request->file('avatar')->getClientOriginalExtension();

            // 2. Build your custom file name
            $filename = 'avatar_' . $user->id . '.' . $extension;

            // 3. Remove old avatar file if present to keep storage clean
            if ($user->avatar_path && Storage::disk('private')->exists($user->avatar_path)) {
                Storage::disk('private')->delete($user->avatar_path);
            }

            // 4. Save file to storage/app/private/avatar using storeAs()
            $path = $request->file('avatar')->storeAs('avatar', $filename, 'private');

            // 5. Update user table column link
            $user->update([
                'avatar_file' => $path
            ]);

            $user->refresh();
        }

        return back()->with('status', __('dashboard/index.avatar_updated'));
    }

    /**
     * Permanently delete the authenticated user's own account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->role === Role::ADMIN && User::where('role', Role::ADMIN)->count() <= 1) {
            return back()->with('status', __('dashboard/index.cannot_delete_last_admin'));
        }

        if ($user->avatar_file && Storage::disk('private')->exists($user->avatar_file)) {
            Storage::disk('private')->delete($user->avatar_file);
        }

        $user->delete();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', __('dashboard/index.account_deleted'));
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