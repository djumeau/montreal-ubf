<?php

namespace App\Enums;

enum Role: string
{
    case GUEST = 'guest';
    case USER = 'user';
    case MUSIC = 'music';
    case MEMBER = 'member';
    case LEADER = 'leader';
    case ELDER = 'elder';
    case ADMIN = 'admin';

    public function label():string
    {
        return __('enums/role.' . $this->value);
    }

    /**
     * Whether this role is the given one or above it, in the order the cases are declared (Guest lowest, Admin highest).
     * Usage: $user->role->atLeast(Role::MEMBER)
     */
    public function atLeast(self $minimum): bool
    {
        return array_search($this, self::cases(), true) >= array_search($minimum, self::cases(), true);
    }

    /**
     * Convenience for populating a <select> in forms (e.g. an admin "Manage Users" screen).
     */
    public static function options(): array
    {
        return array_map(
            fn (self $role) => ['value' => $role->value, 'label' => $role->label()],
            self::cases()
        );
    }

}