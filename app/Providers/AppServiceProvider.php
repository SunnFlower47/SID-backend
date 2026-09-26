<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Spatie\Activitylog\Models\Activity;
use App\Observers\ActivityLogObserver;
use App\Listeners\LogLandlordAuthEvents;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Register Activity Log Observer
        Activity::observe(ActivityLogObserver::class);

        // Register Landlord Auth Audit Trail Listeners
        Event::listen(\Illuminate\Auth\Events\Login::class, LogLandlordAuthEvents::class);
        Event::listen(\Illuminate\Auth\Events\Logout::class, LogLandlordAuthEvents::class);
        Event::listen(\Illuminate\Auth\Events\Failed::class, LogLandlordAuthEvents::class);

        // Register KK Sync Observers
        \App\Models\Penduduk::observe(\App\Observers\PendudukObserver::class);
        \App\Models\Mutasi::observe(\App\Observers\MutasiObserver::class);
        \App\Models\KartuKeluarga::observe(\App\Observers\VillageDataObserver::class);
        \App\Models\SuratPengajuan::observe(\App\Observers\VillageDataObserver::class);
        \App\Models\Dusun::observe(\App\Observers\VillageDataObserver::class);
        \App\Models\Rw::observe(\App\Observers\VillageDataObserver::class);
        \App\Models\Rt::observe(\App\Observers\VillageDataObserver::class);
        \App\Models\WilayahChangeLog::observe(\App\Observers\VillageDataObserver::class);

        // Register SaaS Tenant User Observer (Double Insert ke Central)
        \App\Models\User::observe(\App\Observers\TenantUserObserver::class);

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
        // Customize Password Reset URL (Support both Legacy Laravel & New Next.js)
        \Illuminate\Auth\Notifications\ResetPassword::createUrlUsing(function ($user, string $token) {
            // Jika request datang dari API Next.js
            if (request()->wantsJson() || str_contains(request()->header('Referer') ?? '', 'localhost:3000')) {
                return config('app.frontend_url') . '/reset-password/' . $token . '?email=' . $user->email;
            }
            
            // Jika request dari Laravel lama (default behavior)
            return url(route('password.reset', [
                'token' => $token,
                'email' => $user->email,
            ], false));
        });

        // Define Gates for Landlord / Central (Admin Panel Central) dynamically from database
        $centralAbilities = [
            'manage-central-users',
            'manage-allocations',
            'manage-tenants',
            'broadcast-announcements',
        ];

        foreach ($centralAbilities as $ability) {
            \Illuminate\Support\Facades\Gate::define($ability, function ($user) use ($ability) {
                if (!($user instanceof \App\Models\Central\CentralUser)) {
                    return false;
                }

                // Failsafe bypass: superadmin role always has full access
                if ($user->role === 'superadmin') {
                    return true;
                }

                // Check permissions dynamically using cached array
                $permissions = \Illuminate\Support\Facades\Cache::remember("central_role_perms_{$user->role}", 3600, function () use ($user) {
                    $role = \App\Models\Central\CentralRole::where('name', $user->role)->first();
                    return $role ? ($role->permissions ?? []) : [];
                });

                return in_array($ability, $permissions);
            });
        }
    }
}
