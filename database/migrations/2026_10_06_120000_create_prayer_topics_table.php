<?php

use App\Enums\PrayerCategory;
use App\Enums\Role;

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
        Schema::create('prayer_topics', function (Blueprint $table) {
            $table->id();

            // Subtopics point to their main topic; null = a main topic.
            // Deleting a main topic keeps its subtopics, which become main topics
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('prayer_topics')
                  ->nullOnDelete();

            $table->string('topic_en', 2048);
            $table->string('topic_fr', 2048);

            $table->enum('category', array_column(PrayerCategory::cases(), 'value'))
                  ->default(PrayerCategory::GENERAL->value);

            // Who sees the topic: this role and the roles above it (Guest is everyone, visitors who are not logged in included)
            $table->enum('min_role', array_column(Role::cases(), 'value'))
                  ->default(Role::GUEST->value);

            // Optional link to more details (a report, a conference page...)
            $table->string('url', 2048)->nullable();

            $table->boolean('answered')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prayer_topics');
    }

};
