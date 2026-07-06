<?php

namespace App\Http\Controllers\Landlord\Auth;

use App\Http\Controllers\Controller;
use App\Models\Central\CentralUser;
use App\Models\Central\LandlordAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LandlordLoginController extends Controller
{
    /**
     * Jumlah maksimum percobaan login sebelum akun dikunci.
     */
    const MAX_ATTEMPTS = 5;

    /**
     * Durasi kunci akun dalam menit setelah melebihi MAX_ATTEMPTS.
     */
    const LOCKOUT_MINUTES = 15;

    public function showLoginForm()
    {
        return Inertia::render('Landlord/Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Cari user di central_users
        $user = CentralUser::where('email', $credentials['email'])->first();

        // ── Account Lockout Check ────────────────────────────────────────────
        if ($user && $user->locked_until && now()->lessThan($user->locked_until)) {
            $minutesLeft = now()->diffInMinutes($user->locked_until, true);

            LandlordAuditLog::create([
                'event'       => 'login_blocked_lockout',
                'actor_email' => $user->email,
                'actor_id'    => $user->id,
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
                'description' => "Login diblokir — akun terkunci hingga {$user->locked_until->format('H:i')}.",
            ]);

            return back()->withErrors([
                'email' => "Akun terkunci karena terlalu banyak percobaan gagal. Coba lagi dalam {$minutesLeft} menit.",
            ])->onlyInput('email');
        }
        // ────────────────────────────────────────────────────────────────────

        // Coba login dengan guard 'landlord'
        if (Auth::guard('landlord')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $request->session()->forget('landlord_2fa_verified');

            // Reset counter gagal jika berhasil login
            if ($user) {
                $user->update([
                    'failed_login_attempts' => 0,
                    'locked_until'          => null,
                ]);
            }

            // Audit log ditangani otomatis oleh LogLandlordAuthEvents listener
            return redirect()->intended(route('landlord.dashboard'));
        }

        // ── Increment Failed Attempts ────────────────────────────────────────
        if ($user) {
            $newAttempts = $user->failed_login_attempts + 1;
            $lockedUntil = null;

            if ($newAttempts >= self::MAX_ATTEMPTS) {
                $lockedUntil = now()->addMinutes(self::LOCKOUT_MINUTES);
            }

            $user->update([
                'failed_login_attempts' => $newAttempts,
                'locked_until'          => $lockedUntil,
            ]);
        }
        // ────────────────────────────────────────────────────────────────────

        // Audit log untuk login gagal ditangani oleh LogLandlordAuthEvents listener
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // Audit log logout ditangani otomatis oleh LogLandlordAuthEvents listener
        Auth::guard('landlord')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
