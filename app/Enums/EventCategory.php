<?php

namespace App\Enums;

enum EventCategory: string
{
    case EVENT = 'event';
    case CONFERENCE = 'conference';
    case GBS_IN_PERSON = 'gbs_in_person';
    case GBS_ONLINE = 'gbs_online';

    public function label(): string
    {
        return __('enums/event_category.' . $this->value);
    }

    /**
     * Group Bible study categories: managed on the admin dashboard, not listed on the events page.
     */
    public const BIBLE_STUDIES = [self::GBS_IN_PERSON, self::GBS_ONLINE];

    /**
     * Tailwind classes for the category's pill (light background, dark text, border).
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::EVENT => 'bg-pink-100 text-pink-700 border-pink-400',
            self::CONFERENCE => 'bg-purple-100 text-purple-700 border-purple-400',
            self::GBS_IN_PERSON => 'bg-sky-100 text-sky-700 border-sky-400',
            self::GBS_ONLINE => 'bg-green-100 text-green-700 border-green-500',
        };
    }

    /**
     * Font Awesome icon shown on the schedule's blocks and in the Event modal.
     */
    public function icon(): string
    {
        return match ($this) {
            self::EVENT => 'fa-calendar-day',
            self::CONFERENCE => 'fa-people-group',
            self::GBS_IN_PERSON => 'fa-users',
            self::GBS_ONLINE => 'fa-video',
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
