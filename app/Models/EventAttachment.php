<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAttachment extends Model
{
    protected $table = 'event_attachments';

    protected $fillable = [
        'event_id',
        'type',
        'document_name',
    ];

    public const TYPES = ['document', 'media']; // document: pdf, docx | media: png, jpg, mp4

    /**
     * Get parent ownership relationship context.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

}
