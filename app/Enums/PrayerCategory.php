<?php

namespace App\Enums;

enum PrayerCategory: string
{
    case LOCAL = 'local';
    case INDIVIDUAL = 'individual';
    case HEALTH = 'health';
    case CONFERENCES = 'conferences';
    case WORLD_MISSIONS = 'world_missions';
    case GENERAL = 'general';

    public function label(): string
    {
        return __('enums/prayer_category.' . $this->value);
    }

    /**
     * Convenience for populating a <select> in forms.
     */
    public static function options(): array
    {
        return array_map(
            fn (self $category) => ['value' => $category->value, 'label' => $category->label()],
            self::cases()
        );
    }

}
