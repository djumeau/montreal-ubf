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

        // Components shared by the dashboard pages, e.g. <x-dashboards::toggle-chevron />
        Blade::anonymousComponentPath(resource_path('views/pages/dashboards/components'), 'dashboards');

        // Components kept with the Manage Inquiries dashboard page, e.g. <x-inquiry-items::inquiry-item :inquiry="$inquiry" />
        Blade::anonymousComponentPath(resource_path('views/pages/dashboards/inquiry-items'), 'inquiry-items');

        // Components kept with the Manage Users dashboard page, e.g. <x-manage-users::reset-password-button :user="$user" />
        Blade::anonymousComponentPath(resource_path('views/pages/dashboards/manage-users'), 'manage-users');

        // Components kept with the Manage Schedule dashboard page, e.g. <x-manage-schedule::files-modal />
        Blade::anonymousComponentPath(resource_path('views/pages/dashboards/manage-schedule'), 'manage-schedule');

        // Components kept with the Manage Prayer Topics dashboard page, e.g. <x-manage-prayer-topics::list.table-head />
        Blade::anonymousComponentPath(resource_path('views/pages/dashboards/manage-prayer-topics'), 'manage-prayer-topics');
    }
}
