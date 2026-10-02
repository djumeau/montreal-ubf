<?php

namespace App\Models;

use App\Enums\InquiryType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Inquiry extends Model
{
    protected $table = 'inquiries';

    protected $fillable = [
        'name',
        'email',
        'user',
        'inquiry',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'user' => 'boolean',
            'inquiry' => InquiryType::class,
        ];
    }

    /**
     * Relationship to the inquiry's follow-up on the dashboard (read, answered, by whom, note); null until it is handled.
     */
    public function management(): HasOne
    {
        return $this->hasOne(ManageInquiry::class);
    }

}
