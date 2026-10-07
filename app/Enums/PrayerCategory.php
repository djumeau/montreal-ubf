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
     * One line under the category's name on the home page cards.
     */
    public function description(): string
    {
        return __('enums/prayer_category_description.' . $this->value);
    }

    /**
     * Font Awesome icon shown on the category's card on the home page.
     */
    public function icon(): string
    {
        return match ($this) {
            self::LOCAL => 'fa-church',
            self::INDIVIDUAL => 'fa-user-group',
            self::HEALTH => 'fa-heart-pulse',
            self::CONFERENCES => 'fa-people-group',
            self::WORLD_MISSIONS => 'fa-earth-americas',
            self::GENERAL => 'fa-hands-praying',
        };
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
