<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\Role;

use Illuminate\Validation\Rules\Enum;

use Illuminate\View\View;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class RoleController extends Controller
{

    public function index(Request $request): View
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

    }

    public function update(Request $request, User $user)
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        $validated = $request->validate([
            'role' => ['required', new Enum(Role::class)],
        ]);

        $user->update([
            'role' => $validated['role']
        ]);

        return back()->with('status', __('dashboard/index.role_updated') );
    }

}
