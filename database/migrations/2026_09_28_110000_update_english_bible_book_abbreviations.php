<?php

use App\Models\BibleBook;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * English abbreviations switch to short Logos ones (John -> Jn, 1 Corinthians -> 1Co), as in seeders/data/bible_en.php.
     * Runs before the file-move migrations, which name the study folders after them (jn_01.01-18).
     */
    public function up(): void
    {
        foreach (require database_path('seeders/data/bible_en.php') as $id => $book) {
            DB::table('bible_books')->where('id', $id)->update(['abbreviation_en' => $book['abbreviation']]);
        }

        Cache::forget(BibleBook::CACHE_KEY); // So BibleBook::allCached() picks up the new abbreviations
    }

    /**
     * No way back: the previous abbreviations aren't kept.
     */
    public function down(): void
    {
    }
};
