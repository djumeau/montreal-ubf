<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 2. Add this line right here:
        Schema::defaultStringLength(191);

        // Components kept with the Bible Study Schedule page, e.g. <x-study-schedule::week-head />
        Blade::anonymousComponentPath(resource_path('views/pages/bible-study-schedule/components'), 'study-schedule');

        // Components kept with the Events pages, e.g. <x-events::back-button />
        Blade::anonymousComponentPath(resource_path('views/pages/events/components'), 'events');
    }
}
