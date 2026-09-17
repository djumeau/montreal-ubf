<?php

namespace App\Enums;

enum Role: string
{
    case GUEST = 'guest';
    case USER = 'user';
    case MUSIC = 'music';
    case MEMBER = 'member';
    case ELDER = 'elder';
    case ADMIN = 'admin';

    public function label():string
    {
        return __('enums/role.' . $this->value);
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