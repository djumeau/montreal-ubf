<?php

namespace App\Models;

use App\Enums\Role;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'email', 'password', 'role', 'avatar_file'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar_file',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class, // Cast to Enum
        ];
    }

    public function getAvatarUrlAttribute(): string
    {

        // 1. If the user has uploaded their own unique avatar, stream it
        if ($this->avatar_file && Storage::disk('private')->exists($this->avatar_file)) {
            return route('private.avatar', [
                'filename' => basename($this->avatar_file)
            ]);
        }

        // 2. Check if a default user.jpg exists in storage/app/private/avatar/
        if (Storage::disk('private')->exists('avatar/user.jpg')) {
            return route('private.avatar', [
                'filename' => 'user.jpg'
            ]);
        }

        // 3. Absolute fallback to the public asset folder
        return asset('images/user.jpg');

    }

    public function isAdmin(): bool {
        return $this->role === Role::ADMIN;
    }

    public function hasRole(Role $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Determine if the user can manage church member platform roles.
     */
    public function canManageRoles(): bool
    {
        // Check against your explicit Enum values
        return in_array($this->role->value, ['admin', 'elder']);
    }

}
