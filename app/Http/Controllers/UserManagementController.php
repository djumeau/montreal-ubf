<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use App\Notifications\AdminPasswordReset;
use App\Notifications\NewUserWelcome;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

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

        $newPassword = Str::password(8);

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

        $newPassword = Str::password(16);

        $user->update([
            'password' => $newPassword,
        ]);

        $user->notify(new AdminPasswordReset($newPassword));

        return back()->with('status', __('dashboard/index.password_reset_sent', ['name' => $user->name]));
    }

}
