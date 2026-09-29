<?php

namespace App\Models;

use App\Enums\EventCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $table = 'events';

    protected $fillable = [
        'title',
        'images',
        'category',
        'minimum_profile',
        'start_date',
        'has_end_date',
        'end_date',
        'recurring',
        'location',
        'featured_on_home_page',
        'featured_on_events_page',
        'description',
        'post_event',
    ];

    protected $casts = [
        'images' => 'array', // Keys: desktop (1920x1080), mobile (1200x800), square (1080x1080), in images/events/
        'category' => EventCategory::class,
        'start_date' => 'datetime',
        'has_end_date' => 'boolean',
        'end_date' => 'datetime',
        'recurring' => 'boolean',
        'featured_on_home_page' => 'boolean',
        'featured_on_events_page' => 'boolean',
    ];

    /**
     * Relationship to the event's documents and media files.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(EventAttachment::class, 'event_id');
    }

}
