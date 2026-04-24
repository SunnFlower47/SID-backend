<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;
use App\Observers\ActivityLogObserver;

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
        // Register Activity Log Observer
        Activity::observe(ActivityLogObserver::class);

        // Register KK Sync Observers
        \App\Models\Penduduk::observe(\App\Observers\PendudukObserver::class);
        \App\Models\Mutasi::observe(\App\Observers\MutasiObserver::class);

        // View Composer for sidebar unread count (cached)
        \Illuminate\Support\Facades\View::composer('layouts.components.sidebar', function ($view) {
            $unreadCount = \Illuminate\Support\Facades\Cache::remember('sidebar_unread_contact_count', 60, function () {
                return \App\Models\ContactMessage::unread()->count();
            });
            $view->with('unreadContactCount', $unreadCount);
        });
    }
}
