<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('prayer_topics', function (Blueprint $table) {
            // Order chosen on the dashboard (Move up / Move down), lowest first: among the main topics, or among the subtopics of one main topic
            $table->integer('position')->default(0)->after('parent_id');

            // Optional image: its file name in storage/app/public/images/prayer-topics; null = no image
            $table->string('image')->nullable()->after('url');
        });

        // Existing topics keep the order they were listed in: main topics newest first, subtopics oldest first
        $position = 0;
        foreach (DB::table('prayer_topics')->whereNull('parent_id')->orderByDesc('created_at')->orderByDesc('id')->pluck('id') as $id) {
            DB::table('prayer_topics')->where('id', $id)->update(['position' => ++$position]);
        }

        $positions = [];
        foreach (DB::table('prayer_topics')->whereNotNull('parent_id')->orderBy('id')->get(['id', 'parent_id']) as $subtopic) {
            $positions[$subtopic->parent_id] = ($positions[$subtopic->parent_id] ?? 0) + 1;
            DB::table('prayer_topics')->where('id', $subtopic->id)->update(['position' => $positions[$subtopic->parent_id]]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prayer_topics', function (Blueprint $table) {
            $table->dropColumn(['position', 'image']);
        });
    }

};
