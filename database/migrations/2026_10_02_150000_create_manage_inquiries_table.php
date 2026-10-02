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
        // Follow-up of a contact form message (inquiries table) on the dashboard: one row per inquiry, created once it is handled
        Schema::create('manage_inquiries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inquiry_id')
                  ->unique() // One follow-up per inquiry
                  ->constrained('inquiries')
                  ->cascadeOnDelete(); // Deletes the follow-up if the inquiry is wiped

            $table->timestamp('read_at')->nullable();     // null = not read yet
            $table->timestamp('answered_at')->nullable(); // null = not answered yet

            // Who answered (usually an Elder or an Administrator); kept as null if that account is deleted
            $table->foreignId('answered_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manage_inquiries');
    }
};
