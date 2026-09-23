<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('study_series', function (Blueprint $table) {
            // Related Bible book; null means the series covers multiple books
            $table->foreignId('book_id')
                ->nullable()
                ->after('name_fr')
                ->constrained('bible_books') // Links explicitly to bible_books
                ->nullOnDelete();
        });

        // Backfill: series whose studies all use the same book get that book
        $singleBookSeries = DB::table('bible_studies')
            ->whereNotNull('study_series_id')
            ->whereNotNull('book_id')
            ->groupBy('study_series_id')
            ->havingRaw('COUNT(DISTINCT book_id) = 1')
            ->selectRaw('study_series_id, MIN(book_id) as book_id')
            ->get();

        foreach ($singleBookSeries as $row) {
            DB::table('study_series')->where('id', $row->study_series_id)->update(['book_id' => $row->book_id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_series', function (Blueprint $table) {
            $table->dropConstrainedForeignId('book_id');
        });
    }
};
