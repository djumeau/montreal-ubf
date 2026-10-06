<?php

namespace App\Models;

use App\Enums\PrayerCategory;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrayerTopic extends Model
{
    protected $table = 'prayer_topics';

    protected $fillable = [
        'parent_id',
        'topic_en',
        'topic_fr',
        'category',
        'min_role',
        'url',
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
     * Relationship to the subtopics of this main topic, oldest first.
     */
    public function subtopics(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('id');
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
