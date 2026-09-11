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
        $filename = $this->avatar_file ? basename($this->avatar_file) : 'user.jpg';

        return route('avatar.show', $filename);
    }

    public function isAdmin(): bool {
        return $this->role === Role::ADMIN;
    }

    public function hasRole(Role $role): bool
    {
        return $this->role === $role;
    }

}
