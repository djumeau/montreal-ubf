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
            $table->string('study_series_id');
            $table->integer('book_id')->unsigned();
            $table->string('bible_passage');
            $table->string('title');
            $table->json('image_links');
            $table->json('passage_links');
            $table->json('question_sheet');
            $table->json('lecture');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bible_study');
    }
};
