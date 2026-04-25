<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Spatie\Activitylog\Models\Activity;
use App\Observers\ActivityLogObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

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

        // ============================================================
        // Blade directive: @noncescript / @endnoncescript
        // Otomatis inject CSP nonce ke setiap <script> block inline
        // Pengganti untuk semua <script> di views yang tidak punya nonce.
        // Usage: @noncescript ... @endnoncescript
        // ============================================================
        Blade::directive('noncescript', function () {
            return '<?php echo "<script nonce=\"" . ($csp_nonce ?? "") . "\">"; ?>';
        });

        Blade::directive('endnoncescript', function () {
            return '<?php echo "</script>"; ?>';
        });
    }
}
