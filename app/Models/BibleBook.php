<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class BibleBook extends Model
{
    // Cache key for the 66 books, which never change; cleared by BibleBookSeeder and "php artisan cache:clear" (or /clear-all)
    public const CACHE_KEY = 'bible_books.list';

    protected $table = 'bible_books';

    protected $fillable = [
        'name_en',
        'abbreviation_en',
        'name_fr',
        'abbreviation_fr',
        'testament',
        'chapters',
    ];

    /**
     * Get all Bible Studies linked to this specific book of the Bible.
     */
    public function bibleStudies(): HasMany
    {
        return $this->hasMany(BibleStudy::class, 'book_id');
    }

    /**
     * Every book in canonical order (Genesis first), read from the cache after the first request.
     * The cache holds plain arrays, rebuilt into models here: config/cache.php "serializable_classes" => false
     * blocks cached objects (they come back as __PHP_Incomplete_Class).
     * Usage: BibleBook::allCached(), BibleBook::allCached()->find($id)
     */
    public static function allCached(): Collection
    {
        $rows = Cache::rememberForever(self::CACHE_KEY, fn () => static::orderBy('id')->get()->map->getAttributes()->all());

        return static::hydrate($rows);
    }

    /**
     * Book name in the current locale: "Jean" in fr_CA, "John" otherwise.
     * Usage: $book->current_name
     */
    protected function currentName(): Attribute
    {
        return Attribute::get(fn () => app()->getLocale() === 'fr_CA' ? $this->name_fr : $this->name_en);
    }
}
