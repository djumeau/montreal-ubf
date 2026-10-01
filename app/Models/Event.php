<?php

namespace App\Models;

use App\Enums\EventCategory;
use App\Enums\Role;
use App\Support\SafeHtml;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $table = 'events';

    // Roles an event can be reserved for, lowest first: the values the minimum_profile column accepts
    // (see the create_events_table migration; it has no Music role)
    public const MINIMUM_PROFILES = [Role::GUEST, Role::USER, Role::MEMBER, Role::LEADER, Role::ELDER, Role::ADMIN];

    protected $fillable = [
        'title_en',
        'title_fr',
        'images',
        'category',
        'color',
        'minimum_profile',
        'bible_study_id',
        'start_date',
        'has_end_date',
        'end_date',
        'recurring',
        'location',
        'contact_name',
        'contact_email',
        'website_url',
        'featured_on_home_page',
        'featured_on_events_page',
        'description_en',
        'description_fr',
        'post_event_summary_en',
        'post_event_summary_fr',
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
     * Group Bible studies: the Bible study being covered (null for other events).
     */
    public function bibleStudy(): BelongsTo
    {
        return $this->belongsTo(BibleStudy::class, 'bible_study_id');
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
     * Events shown on the public events page: every category except the group Bible studies.
     * Usage: Event::publicListing()
     */
    public function scopePublicListing(Builder $query): void
    {
        $query->whereNotIn('category', array_map(fn (EventCategory $category) => $category->value, EventCategory::BIBLE_STUDIES));
    }

    /**
     * Group Bible studies only (the Bible Study Schedule), the opposite of scopePublicListing().
     * Usage: Event::bibleStudies()
     */
    public function scopeBibleStudies(Builder $query): void
    {
        $query->whereIn('category', array_map(fn (EventCategory $category) => $category->value, EventCategory::BIBLE_STUDIES));
    }

    /**
     * Whether the event is a group Bible study (managed on the admin dashboard, not on the events page).
     */
    public function isBibleStudy(): bool
    {
        return in_array($this->category, EventCategory::BIBLE_STUDIES, true);
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
     * Past events: not recurring, and whose end date (or start date without one) is past (the opposite of scopeUpcoming()).
     */
    public function scopePast(Builder $query): void
    {
        $query->where('recurring', false)
            ->whereRaw('COALESCE(end_date, start_date) < ?', [now()]);
    }

    /**
     * Events matching every word of the search text in their title, location or description (either language).
     * Usage: Event::search('camp kinkora')
     */
    public function scopeSearch(Builder $query, string $search = ''): void
    {
        $terms = $search === '' ? [] : preg_split('/\s+/', $search);

        foreach ($terms as $term) {
            $query->where(fn (Builder $q) => $q
                ->whereLike('title_en', "%{$term}%")
                ->orWhereLike('title_fr', "%{$term}%")
                ->orWhereLike('location', "%{$term}%")
                ->orWhereLike('description_en', "%{$term}%")
                ->orWhereLike('description_fr', "%{$term}%"));
        }
    }

    /**
     * Text colour for the event's colour: white or dark slate, whichever has the higher contrast
     * (WCAG relative luminance); null when the event has no valid "#RRGGBB" colour.
     * Usage: $event->color_text
     */
    protected function colorText(): Attribute
    {
        return Attribute::get(function () {
            if (! preg_match('/^#[0-9a-f]{6}$/i', (string) $this->color)) {
                return null;
            }

            $luminance = fn (string $hex) => collect(str_split(ltrim($hex, '#'), 2))
                ->map(fn ($pair) => hexdec($pair) / 255)
                ->map(fn ($c) => $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4)
                ->pipe(fn ($rgb) => 0.2126 * $rgb[0] + 0.7152 * $rgb[1] + 0.0722 * $rgb[2]);

            $background = $luminance($this->color);
            $dark = '#0F172A'; // slate-900
            $contrast = fn (float $a, float $b) => (max($a, $b) + 0.05) / (min($a, $b) + 0.05);

            return $contrast($background, 1.0) >= $contrast($background, $luminance($dark)) ? '#FFFFFF' : $dark;
        });
    }

    /**
     * Title in the current language, else the other one.
     * Usage: $event->current_title
     */
    protected function currentTitle(): Attribute
    {
        return Attribute::get(fn () => $this->translated('title'));
    }

    /**
     * Description in the current language, else the other one.
     * Usage: $event->current_description
     */
    protected function currentDescription(): Attribute
    {
        return Attribute::get(fn () => $this->translated('description'));
    }

    /**
     * Description in the current language as safe HTML: formatting tags kept (<p>, <ul>, <li>, <strong>, <a>...),
     * anything else removed; plain text keeps its line breaks. Print with {!! !!}.
     * Usage: {!! $event->description_html !!}
     */
    protected function descriptionHtml(): Attribute
    {
        return Attribute::get(fn () => SafeHtml::clean($this->current_description));
    }

    /**
     * Post-event summary in the current language as safe HTML (same rules as description_html). Print with {!! !!}.
     * Usage: {!! $event->post_event_summary_html !!}
     */
    protected function postEventSummaryHtml(): Attribute
    {
        return Attribute::get(fn () => SafeHtml::clean($this->current_post_event_summary));
    }

    /**
     * Whether every word of the search text is in the title, location or description (either language),
     * ignoring HTML tags, so "li" or "strong" don't match the markup. Follows scopeSearch().
     */
    public function matchesSearch(string $search): bool
    {
        $terms = $search === '' ? [] : preg_split('/\s+/', $search);

        $text = implode(' ', [
            $this->title_en,
            $this->title_fr,
            $this->location,
            html_entity_decode(strip_tags((string) $this->description_en)),
            html_entity_decode(strip_tags((string) $this->description_fr)),
        ]);

        foreach ($terms as $term) {
            if (mb_stripos($text, $term) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Post-event summary in the current language, else the other one.
     * Usage: $event->current_post_event_summary
     */
    protected function currentPostEventSummary(): Attribute
    {
        return Attribute::get(fn () => $this->translated('post_event_summary'));
    }

    /**
     * "{field}_fr" or "{field}_en" for the current language, falling back to the other language when empty.
     */
    private function translated(string $field): ?string
    {
        [$current, $other] = app()->getLocale() === 'fr_CA' ? ['fr', 'en'] : ['en', 'fr'];

        return $this->{"{$field}_{$current}"} ?: $this->{"{$field}_{$other}"};
    }

    /**
     * Location name: the part of "location" before the first comma, e.g. "Montreal UBF" from
     * "Montreal UBF, 2627 rue Ryde, Montréal, QC, H3K 1R7".
     * Usage: $event->location_name
     */
    protected function locationName(): Attribute
    {
        return Attribute::get(fn () => $this->location === null ? null : trim(explode(',', $this->location)[0]));
    }

    /**
     * Address: the part of "location" after the first comma, e.g. "2627 rue Ryde, Montréal, QC, H3K 1R7"; null without one.
     * Usage: $event->location_address
     */
    protected function locationAddress(): Attribute
    {
        return Attribute::get(fn () => str_contains((string) $this->location, ',')
            ? trim(explode(',', $this->location, 2)[1])
            : null);
    }

    /**
     * Whether the location can be shown on Google Maps: it has an address and the event is not online.
     */
    public function hasMap(): bool
    {
        return $this->category !== EventCategory::GBS_ONLINE && $this->location_address !== null;
    }

    /**
     * Google Maps search for the full location; null when hasMap() is false.
     * Usage: $event->maps_url
     */
    protected function mapsUrl(): Attribute
    {
        return Attribute::get(fn () => $this->hasMap()
            ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($this->location)
            : null);
    }

    /**
     * Embedded Google Map of the full location (no API key needed); null when hasMap() is false.
     * Usage: <iframe src="{{ $event->maps_embed_url }}">
     */
    protected function mapsEmbedUrl(): Attribute
    {
        return Attribute::get(fn () => $this->hasMap()
            ? 'https://maps.google.com/maps?q=' . urlencode($this->location) . '&output=embed'
            : null);
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
