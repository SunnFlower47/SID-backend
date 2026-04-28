<?php

namespace App\Providers;

use App\Models\Mutasi;
use App\Models\Penduduk;
use App\Policies\MutasiPolicy;
use App\Policies\PendudukPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Penduduk::class => PendudukPolicy::class,
        Mutasi::class => MutasiPolicy::class,
        KartuKeluarga::class => \App\Policies\KartuKeluargaPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Implicitly grant "Super Admin" role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        // Testimoni permissions
        Gate::define('testimoni.view', function ($user) {
            return $user->hasPermissionTo('testimoni.view') || $user->hasRole(['Super Admin', 'Admin', 'Sekretaris']);
        });

        Gate::define('testimoni.create', function ($user) {
            return $user->hasPermissionTo('testimoni.create') || $user->hasRole(['Super Admin', 'Admin', 'Sekretaris']);
        });

        Gate::define('testimoni.update', function ($user) {
            return $user->hasPermissionTo('testimoni.update') || $user->hasRole(['Super Admin', 'Admin', 'Sekretaris']);
        });

        Gate::define('testimoni.delete', function ($user) {
            return $user->hasPermissionTo('testimoni.delete') || $user->hasRole(['Super Admin', 'Admin']);
        });
    }
}
