<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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

    /**
     * Path on the private "local" disk: documents/events/{category}/{start date}/{document_name},
     * e.g. documents/events/conference/2026-11-20/fall_conference_schedule.pdf
     * Usage: Storage::disk('local')->response($attachment->storage_path)
     */
    protected function storagePath(): Attribute
    {
        return Attribute::get(fn () => $this->event->documentDirectory() . '/' . $this->document_name);
    }

}
