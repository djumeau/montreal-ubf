<?php

namespace App\Models;

use App\Enums\PrayerCategory;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class PrayerTopic extends Model
{
    protected $table = 'prayer_topics';

    // Folder of the topics' images on the public disk (storage/app/public)
    public const IMAGE_DIRECTORY = 'images/prayer-topics';

    protected $fillable = [
        'parent_id',
        'position',
        'topic_en',
        'topic_fr',
        'category',
        'min_role',
        'url',
        'image',
        'answered',
    ];

    protected function casts(): array
    {
        return [
            'category' => PrayerCategory::class,
            'min_role' => Role::class,
            'answered' => 'boolean',
        ];
    }

    /**
     * Relationship to the main topic this one is a subtopic of; null for a main topic.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Relationship to the subtopics of this main topic, in the order chosen on the dashboard.
     */
    public function subtopics(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position')->orderBy('id');
    }

    /**
     * Topics the viewer may see: a Guest minimum role is open to everyone (visitors who are not logged in too),
     * the others need a role at that level or above.
     * Usage: PrayerTopic::visibleTo($request->user())
     */
    public function scopeVisibleTo(Builder $query, ?User $user): void
    {
        $query->visibleToRole($user?->role ?? Role::GUEST);
    }

    /**
     * Topics open to a role: those whose minimum role is that role or one below it.
     * Usage: PrayerTopic::visibleToRole(Role::MEMBER)
     */
    public function scopeVisibleToRole(Builder $query, Role $role): void
    {
        $roles = array_filter(Role::cases(), fn (Role $minimum) => $role->atLeast($minimum));

        $query->whereIn('min_role', array_map(fn (Role $minimum) => $minimum->value, $roles));
    }

    /**
     * Topics in the order chosen on the dashboard (Move up / Move down), lowest position first.
     * Usage: PrayerTopic::whereNull('parent_id')->ordered()->get()
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('position')->orderBy('id');
    }

    /**
     * Position that puts a new topic where it belongs in its list: main topics at the top, subtopics at the bottom of their main topic.
     */
    public static function newPosition(?int $parentId): int
    {
        $siblings = self::query()->where('parent_id', $parentId);

        return $parentId ? ($siblings->max('position') ?? 0) + 1 : ($siblings->min('position') ?? 1) - 1;
    }

    /**
     * URL of the topic's image; null without one.
     * Usage: $prayerTopic->image_url
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->image ? asset('storage/' . self::IMAGE_DIRECTORY . '/' . $this->image) : null);
    }

    /**
     * Delete the topic's image file, if it has one (the column is left to the caller).
     */
    public function deleteImage(): void
    {
        if ($this->image) {
            Storage::disk('public')->delete(self::IMAGE_DIRECTORY . '/' . $this->image);
        }
    }

    /**
     * Topic text in the current locale: topic_fr in fr_CA, topic_en otherwise.
     * Usage: $prayerTopic->current_topic
     */
    protected function currentTopic(): Attribute
    {
        return Attribute::get(fn () => app()->getLocale() === 'fr_CA' ? $this->topic_fr : $this->topic_en);
    }

}
