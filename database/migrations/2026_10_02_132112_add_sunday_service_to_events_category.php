<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Values the events.category column accepts after / before this migration
    private const WITH_SUNDAY_SERVICE = ['event', 'conference', 'gbs_in_person', 'gbs_online', 'sunday_service'];
    private const WITHOUT_SUNDAY_SERVICE = ['event', 'conference', 'gbs_in_person', 'gbs_online'];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->allow(self::WITH_SUNDAY_SERVICE);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Sunday services become plain events, so the narrower list can be put back
        DB::table('events')->where('category', 'sunday_service')->update(['category' => 'event']);

        $this->allow(self::WITHOUT_SUNDAY_SERVICE);
    }

    /**
     * Set the list of values the category column accepts.
     * PostgreSQL keeps the list in a check constraint; MySQL / MariaDB keep it in the column type.
     */
    private function allow(array $values): void
    {
        if (DB::getDriverName() === 'pgsql') {
            $list = implode(', ', array_map(fn (string $value) => "'{$value}'", $values));

            DB::statement('ALTER TABLE events DROP CONSTRAINT IF EXISTS events_category_check');
            DB::statement("ALTER TABLE events ADD CONSTRAINT events_category_check CHECK (category IN ({$list}))");

            return;
        }

        Schema::table('events', function (Blueprint $table) use ($values) {
            $table->enum('category', $values)->default('event')->change();
        });
    }
};
