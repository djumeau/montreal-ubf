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
        Schema::create('study_attachments', function (Blueprint $table) {
            $table->id();

            // Foreign Key tracking back to your parent table
            $table->foreignId('bible_study_id')
                  ->constrained('bible_studies')
                  ->cascadeOnDelete(); // Deletes associated file records if a study is wiped

            $table->string('locale', 5)->index(); // 'en_CA', 'fr_CA' (indexed for rapid language filtering)
            $table->string('type');               // 'question_sheet', 'lecture'
            $table->string('filename');           // 'jn_01.1-18.q' (clean base name)
            $table->string('extension', 5);       // 'docx', 'pdf'

            // Analytics column
            $table->unsignedInteger('views')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_attachments');
    }
};
