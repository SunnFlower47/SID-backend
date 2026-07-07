<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Central\UserTenantMap;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedException;

class InitializeTenancyByLoggedInUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        \Illuminate\Support\Facades\Log::info('[tenant.auth] START', [
            'url' => $request->fullUrl(),
            'has_tenant_id' => session()->has('tenant_id'),
            'tenant_id' => session('tenant_id'),
            'tenancy_initialized' => tenancy()->initialized,
        ]);

        // 1. Initialize tenancy dari session JIKA ADA
        // Ini harus dilakukan SEBELUM auth()->check() agar Laravel bisa 
        // baca tabel users di database tenant yang benar.
        if (session()->has('tenant_id')) {
            try {
                $tenantId = session()->get('tenant_id');
                $tenant = \App\Models\Tenant::find($tenantId);
                
                if (!$tenant) {
                    auth()->logout();
                    session()->invalidate();
                    session()->regenerateToken();
                    return redirect()->route('login')->with('error', 'Desa tidak ditemukan.');
                }
                
                if (!$tenant->is_active) {
                    auth()->logout();
                    session()->invalidate();
                    session()->regenerateToken();
                    return redirect()->route('login')->with('error', 'Desa Anda dinonaktifkan. Silakan hubungi admin Diskominfo.');
                }
                
                tenancy()->initialize($tenantId);
            } catch (\Exception $e) {
                auth()->logout();
                session()->invalidate();
                session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Gagal memuat data desa: ' . $e->getMessage());
            }
        }

        \Illuminate\Support\Facades\Log::info('[tenant.auth] AFTER INIT', [
            'tenancy_initialized' => tenancy()->initialized,
        ]);

        // 2. Pastikan user sudah login
        try {
            $isLoggedIn = auth()->check();
            \Illuminate\Support\Facades\Log::info('[tenant.auth] AUTH CHECK', [
                'logged_in' => $isLoggedIn,
                'user_id' => auth()->id(),
            ]);
            if (!$isLoggedIn) {
                \Illuminate\Support\Facades\Log::info('[tenant.auth] NOT LOGGED IN - redirecting to login');
                return redirect()->route('login');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error('[tenant.auth] QueryException during auth check: ' . $e->getMessage());
            // Jika crash karena mencari tabel users di db_central (akibat remember_me cookie lama
            // atau session tanpa tenant_id), bersihkan semua cookie auth dan paksa login ulang.
            auth()->logout();
            session()->flush();
            return redirect()->route('login')->with('error', 'Sesi kedaluwarsa. Silakan login kembali.');
        }

        // 3. Fallback: jika session tenant_id hilang tapi user login, cari dari central
        if (!tenancy()->initialized) {
            $user = auth()->user(); // Akan error jika auth table ngga ada di central, makanya session itu krusial
            $map = UserTenantMap::where('email', $user->email)->first();

            if ($map) {
                try {
                    $tenant = \App\Models\Tenant::find($map->tenant_id);
                    if (!$tenant) {
                        auth()->logout();
                        session()->invalidate();
                        session()->regenerateToken();
                        return redirect()->route('login')->with('error', 'Desa tidak ditemukan.');
                    }
                    if (!$tenant->is_active) {
                        auth()->logout();
                        session()->invalidate();
                        session()->regenerateToken();
                        return redirect()->route('login')->with('error', 'Desa Anda dinonaktifkan. Silakan hubungi admin Diskominfo.');
                    }
                    
                    tenancy()->initialize($map->tenant_id);
                    session()->put('tenant_id', $map->tenant_id);
                } catch (\Exception $e) {
                    auth()->logout();
                    session()->invalidate();
                    session()->regenerateToken();
                    return redirect()->route('login')->with('error', 'Gagal memuat data desa.');
                }
            } else {
                auth()->logout();
                session()->invalidate();
                session()->regenerateToken();
                return redirect()->route('login')->with('error', 'User tidak terdaftar di desa manapun.');
            }
        }

        \Illuminate\Support\Facades\Log::info('[tenant.auth] PASSING THROUGH', [
            'tenant' => tenant('id'),
        ]);

        return $next($request);
    }
}
