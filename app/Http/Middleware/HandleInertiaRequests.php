<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // [SaaS] Jangan panggil $request->user() jika tenancy belum diinisialisasi.
        // HandleInertiaRequests berjalan GLOBAL di semua request web — termasuk halaman login
        // yang belum punya session tenant. Jika kita langsung panggil $request->user() di sana,
        // Laravel akan mencoba baca tabel `users` dari db_central yang memang tidak ada.
        // Solusi: cek dulu apakah tenancy sudah aktif. Jika belum, return null.
        $authUser = null;
        $isLandlord = false;
        if (function_exists('tenancy') && tenancy()->initialized) {
            try {
                $authUser = $request->user();
            } catch (\Exception $e) {
                $authUser = null;
            }
        } else {
            // Check central landlord auth
            if (auth('landlord')->check()) {
                $authUser = auth('landlord')->user();
                $isLandlord = true;
            }
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $authUser,
                'is_landlord' => $isLandlord,
                'role' => $authUser ? ($authUser->role ?? null) : null,
                'can' => $isLandlord && $authUser ? [
                    'manage_central_users' => \Illuminate\Support\Facades\Gate::allows('manage-central-users'),
                    'manage_allocations' => \Illuminate\Support\Facades\Gate::allows('manage-allocations'),
                    'manage_tenants' => \Illuminate\Support\Facades\Gate::allows('manage-tenants'),
                    'broadcast_announcements' => \Illuminate\Support\Facades\Gate::allows('broadcast-announcements'),
                ] : [],
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'status' => $request->session()->get('status'),
                'timestamp' => now()->getTimestamp(),
            ],
            'recaptcha' => [
                'enabled' => (bool) config('services.recaptcha.enabled'),
                'v2_site_key' => config('services.recaptcha.v2_site_key'),
                'v3_site_key' => config('services.recaptcha.v3_site_key'),
            ],
            'desa_settings' => tenancy()->initialized ? \App\Models\DesaSetting::getAllAsArray() : [
                'nama_desa' => 'Portal Layanan',
                'sebutan_desa' => 'Desa',
                'sebutan_kepala_desa' => 'Kepala Desa',
                'logo_desa' => null,
            ],
        ]);
    }
}
