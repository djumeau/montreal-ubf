<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bible_studies', function (Blueprint $table) {
            $table->id();

            // Relational Foreign Keys with database-level constraints
            $table->foreignId('study_series_id')
                ->nullable()
                ->constrained('study_series') // Links explicitly to study_series
                ->nullOnDelete();

            $table->foreignId('book_id')
            ->nullable()
            ->constrained('bible_books') // Links explicitly to bible_books
            ->nullOnDelete();

            // Unified, clean plain-text metadata fields
            $table->string('bible_passage')->nullable(); // "1:1-18"

            // Explicit multi-column localized titles
            $table->string('title_en')->nullable();
            $table->string('title_fr')->nullable();

            $table->json('image_links')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bible_studies');
    }
};
