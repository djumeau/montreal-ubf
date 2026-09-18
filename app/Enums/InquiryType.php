<?php

namespace App\Enums;

enum InquiryType: string
{
    case MINISTRY = 'ministry';
    case GROUP_STUDY = 'group_study';
    case ONE_TO_ONE = 'one_to_one';
    case WORSHIP = 'worship';
    case OTHER = 'other';
    case PASTORAL = 'pastoral';

    public function label(): string
    {
        return __('enums/inquiry_type.' . $this->value);
    }

    /**
     * Convenience for populating a <select> in forms.
     */
    public static function options(): array
    {
        return array_map(
            fn (self $type) => ['value' => $type->value, 'label' => $type->label()],
            self::cases()
        );
    }

}
