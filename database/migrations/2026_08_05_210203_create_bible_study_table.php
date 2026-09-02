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
        Schema::create('bible_studies_en', function (Blueprint $table) {
            $table->id();
            $table->string('study_series_id')->nullable();
            $table->unsignedInteger('book_id')->nullable();
            $table->string('bible_passage')->nullable();
            $table->json('title')->nullable();
            $table->json('image_links')->nullable();
            $table->json('passage_links')->nullable();
            $table->json('question_sheet')->nullable();
            $table->json('lecture')->nullable();
            $table->timestamps();
        });

        Schema::create('bible_studies_fr', function (Blueprint $table) {
            $table->id();
            $table->string('study_series_id')->nullable();
            $table->unsignedInteger('book_id')->nullable();
            $table->string('bible_passage')->nullable();
            $table->json('title')->nullable();
            $table->json('image_links')->nullable();
            $table->json('passage_links')->nullable();
            $table->json('question_sheet')->nullable();
            $table->json('lecture')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bible_studies_en');
        Schema::dropIfExists('bible_studies_fr');
    }
};
