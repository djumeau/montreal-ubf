<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use App\Notifications\AdminPasswordReset;
use App\Notifications\NewUserWelcome;
use App\Support\TemporaryPassword;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class UserManagementController extends Controller
{

    public function store(Request $request): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        ]);

        $newPassword = TemporaryPassword::generate();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $newPassword,
            'role' => Role::GUEST->value,
        ]);

        $user->notify(new NewUserWelcome($newPassword));

        return back()->with('status', __('dashboard/index.user_created', ['name' => $user->name]));
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        $newPassword = TemporaryPassword::generate();

        // Emailed first: if it cannot be sent, the password is left as it was, so the user is not locked out
        try {
            $user->notify(new AdminPasswordReset($newPassword));
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', __('dashboard/index.password_reset_failed', ['name' => $user->name]));
        }

        $user->update([
            'password' => $newPassword,
        ]);

        return back()->with('status', __('dashboard/index.password_reset_sent', ['name' => $user->name]));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        if ($user->role === Role::ADMIN && User::where('role', Role::ADMIN)->count() <= 1) {
            return back()->with('status', __('dashboard/index.cannot_delete_last_admin'));
        }

        if ($user->avatar_file && Storage::disk('private')->exists($user->avatar_file)) {
            Storage::disk('private')->delete($user->avatar_file);
        }

        $name = $user->name;
        $user->delete();

        return back()->with('status', __('dashboard/index.user_deleted', ['name' => $name]));
    }

}
