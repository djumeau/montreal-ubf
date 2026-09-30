<?php

use App\Enums\EventCategory;

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
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->string('title_en', 80)->nullable();
            $table->string('title_fr', 80)->nullable();

            // { "desktop": "...", "mobile": "...", "square": "..." } (1920x1080, 1200x800, 1080x1080)
            $table->json('images')->nullable();

            $table->enum('category', array_column(EventCategory::cases(), 'value'))
                  ->default(EventCategory::EVENT->value);

            $table->enum('minimum_profile', [
                'guest',
                'user',
                'member',
                'leader',
                'elder',
                'admin',
            ])->default('guest');

            // Group Bible studies: the study being covered (the event stays if the study is deleted)
            $table->foreignId('bible_study_id')
                  ->nullable()
                  ->constrained('bible_studies')
                  ->nullOnDelete();

            $table->dateTime('start_date');
            $table->boolean('has_end_date')->default(false);
            $table->dateTime('end_date')->nullable();
            $table->boolean('recurring')->default(false);

            $table->string('location', 1024)->nullable();

            // Person to contact about the event (e.g. the Bible study leader)
            $table->string('contact_name', 100)->nullable();
            $table->string('contact_email')->nullable();

            // Event's own website (same for both languages), e.g. https://franco2026.university-bible-fellowship.ca
            $table->string('website_url', 2048)->nullable();

            $table->boolean('featured_on_home_page')->default(false);
            $table->boolean('featured_on_events_page')->default(false);

            $table->mediumText('description_en')->nullable();
            $table->mediumText('description_fr')->nullable();

            // Recap written after the event takes place
            $table->mediumText('post_event_summary_en')->nullable();
            $table->mediumText('post_event_summary_fr')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
