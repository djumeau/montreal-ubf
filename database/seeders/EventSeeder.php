<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use Illuminate\Support\Arr;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load Init Events data
        $events = require database_path('seeders/data/events.php');

        foreach ($events as $id => $eventData) {

            // Pull out the attachments so they are not inserted as an events column
            $attachments = Arr::pull($eventData, 'attachments', []);

            // forceCreate keeps the data file's id (id is not fillable), so attachment folders match
            $event = Event::forceCreate(array_merge(['id' => $id], $eventData));

            foreach ($attachments as $attachmentData) {
                $event->attachments()->create($attachmentData);
            }
        }

    }

}
