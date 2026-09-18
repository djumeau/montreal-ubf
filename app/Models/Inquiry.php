<?php

namespace App\Models;

use App\Enums\InquiryType;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
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

}
