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

            $table->string('title', 80);

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

            $table->dateTime('start_date');
            $table->boolean('has_end_date')->default(false);
            $table->dateTime('end_date')->nullable();
            $table->boolean('recurring')->default(false);

            $table->string('location', 1024)->nullable();

            $table->boolean('featured_on_home_page')->default(false);
            $table->boolean('featured_on_events_page')->default(false);

            $table->mediumText('description')->nullable();
            $table->binary('post_event')->nullable();

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
