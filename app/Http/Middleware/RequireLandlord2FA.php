<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireLandlord2FA
{
    /**
     * Rute yang diabaikan dari pengecekan 2FA (agar tidak terjadi infinite redirect atau terjebak).
     */
    protected array $exceptRoutes = [
        'landlord.logout',
        'landlord.2fa.setup',
        'landlord.2fa.enable',
        'landlord.2fa.verify',
        'landlord.2fa.verify.post',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('landlord')->check()) {
            return $next($request);
        }

        $user = Auth::guard('landlord')->user();
        $routeName = $request->route()?->getName();

        // Abaikan jika rute saat ini ada dalam daftar pengecualian
        if ($routeName && in_array($routeName, $this->exceptRoutes)) {
            return $next($request);
        }

        // Jika 2FA sudah aktif tapi belum diverifikasi di sesi ini
        if ($user->two_factor_enabled && !session('landlord_2fa_verified')) {
            return redirect()->route('landlord.2fa.verify');
        }

        // Wajibkan setup 2FA untuk semua admin Landlord yang belum aktif
        if (!$user->two_factor_enabled) {
            return redirect()->route('landlord.2fa.setup')
                ->with('warning', 'Demi keamanan, Anda wajib mengaktifkan verifikasi Dua Langkah (2FA) sebelum mengakses panel.');
        }

        return $next($request);
    }
}
