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
