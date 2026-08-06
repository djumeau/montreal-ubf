<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class BibleBook extends Model
{
    use Sushi;

    public $incrementing = false; // IDs are provided in the data array

    /**
     * Force Sushi to flush its internal static memory cache.
     * Call this if you change locales and query the model in the same request.
     */
    public static function flushLanguageCache(): void
    {
        // Resets Sushi's internal static indicators
        static::$sushiBoots = [];
    }

    /**
     * Dynamically change the Sushi cache key based on the session locale.
     * This forces Sushi to reload the correct dataset when the language changes.
     */
    protected function cacheKey(): string
    {
        $locale = Session::get('Locale', config('app.locale'));

        return 'sushi-bible-books-' . $locale;
    }

    /**
     * Model rows are built dynamically from the localization array.
     */

    public function getRows(): array
    {
        // 1. Get locale from session (fallback to default config)
        $locale = Session::get('Locale', config('app.locale'));

        // 2. Force Laravel to use this locale for the current request context
        App::setLocale($locale);

        // 3. Pull the translation file array
        $books = Lang::get('bible');

        if (!is_array($books)) {
            return [];
        }

        return $books;
    }

}
