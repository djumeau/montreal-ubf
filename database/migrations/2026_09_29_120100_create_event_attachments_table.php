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
        Schema::create('event_attachments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')
                  ->constrained('events')
                  ->cascadeOnDelete(); // Deletes associated file records if an event is wiped

            $table->enum('type', ['document', 'media']); // document: pdf, docx | media: png, jpg, mp4
            $table->string('document_name');             // 'retreat_schedule.pdf'
            $table->string('locale', 5)->nullable()->index(); // Documents: 'en_CA', 'fr_CA' | Media: null (shown in both languages)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_attachments');
    }
};
