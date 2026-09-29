<?php

namespace App\Models;

use App\Enums\EventCategory;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Builder;
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
        'images' => 'array', // Keys: desktop (1920x1080), mobile (1200x800), square (1080x1080), in imageDirectory()
        'category' => EventCategory::class,
        'minimum_profile' => Role::class,
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

    /**
     * Events the viewer may see: a Guest minimum profile is open to everyone (visitors too),
     * the others need a role at that level or above.
     * Usage: Event::visibleTo($request->user())
     */
    public function scopeVisibleTo(Builder $query, ?User $user): void
    {
        $roles = array_filter(Role::cases(), fn (Role $role) => $role === Role::GUEST || $user?->role->atLeast($role));

        $query->whereIn('minimum_profile', array_map(fn (Role $role) => $role->value, $roles));
    }

    /**
     * Whether the viewer may see this event and open its attachments (same rule as scopeVisibleTo()).
     */
    public function isVisibleTo(?User $user): bool
    {
        return $this->minimum_profile === Role::GUEST || (bool) $user?->role->atLeast($this->minimum_profile);
    }

    /**
     * Upcoming events: recurring ones, and those whose end date (or start date without one) is not past yet.
     */
    public function scopeUpcoming(Builder $query): void
    {
        $query->where(fn (Builder $q) => $q
            ->where('recurring', true)
            ->orWhereRaw('COALESCE(end_date, start_date) >= ?', [now()]));
    }

    /**
     * Folder on the "public" disk holding this event's images,
     * storage/app/public/images/events/{category}/{start date}, e.g. images/events/conference/2026-11-20.
     */
    public function imageDirectory(): string
    {
        return 'images/events/' . $this->folderPath();
    }

    /**
     * Folder on the private "local" disk holding this event's attachments,
     * storage/app/private/documents/events/{category}/{start date}, e.g. documents/events/conference/2026-11-20.
     */
    public function documentDirectory(): string
    {
        return 'documents/events/' . $this->folderPath();
    }

    /**
     * "{category}/{start date}" part shared by imageDirectory() and documentDirectory(), e.g. "gbs_online/2026-10-07".
     */
    private function folderPath(): string
    {
        return $this->category->value . '/' . $this->start_date->format('Y-m-d');
    }

    /**
     * Public URL of one of the event's images, falling back to the default events image.
     * Usage: $event->imageUrl('square' | 'desktop' | 'mobile')
     */
    public function imageUrl(string $type): string
    {
        if ($file = $this->images[$type] ?? null) {
            return asset('storage/' . $this->imageDirectory() . '/' . $file);
        }

        return asset("storage/images/events/events-{$type}.jpg");
    }

}
