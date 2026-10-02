<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManageInquiry extends Model
{
    protected $table = 'manage_inquiries';

    protected $fillable = [
        'inquiry_id',
        'read_at',
        'answered_at',
        'answered_by',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'answered_at' => 'datetime',
        ];
    }

    /**
     * Relationship to the contact form message this follow-up is about.
     */
    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }

    /**
     * Relationship to the user who answered (usually an Elder or an Administrator).
     */
    public function answeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'answered_by');
    }

}
