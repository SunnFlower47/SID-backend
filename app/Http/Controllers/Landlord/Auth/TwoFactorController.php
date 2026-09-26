<?php

namespace App\Http\Controllers\Landlord\Auth;

use App\Http\Controllers\Controller;
use App\Models\Central\LandlordAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use PragmaRX\Google2FA\Google2FA;
use chillerlan\QRCode\QRCode;

class TwoFactorController extends Controller
{
    /**
     * Tampilkan halaman setup 2FA (QR Code & Secret Key).
     */
    public function showSetup()
    {
        $user = Auth::guard('landlord')->user();

        if ($user->two_factor_enabled) {
            return redirect()->route('landlord.dashboard');
        }

        // Generate secret baru jika belum ada
        if (empty($user->two_factor_secret)) {
            $secret = (new Google2FA())->generateSecretKey();
            $user->update(['two_factor_secret' => $secret]);
        }

        // Generate URL untuk TOTP
        $qrUrl = (new Google2FA())->getQRCodeUrl(
            'Sistem Desa (Admin Panel Central)',
            $user->email,
            $user->two_factor_secret
        );

        // Render QR Code ke format SVG Base64 Data URI
        $qrCodeImage = (new QRCode())->render($qrUrl);

        return Inertia::render('Landlord/Auth/TwoFactorSetup', [
            'qrCodeImage' => $qrCodeImage,
            'secretKey'   => $user->two_factor_secret,
        ]);
    }

    /**
     * Verifikasi kode OTP awal dan aktifkan 2FA.
     */
    public function enable(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'min:6'],
        ]);

        $user  = Auth::guard('landlord')->user();
        $code  = trim($request->code);
        $valid = (new Google2FA())->verifyKey($user->two_factor_secret, $code);

        if (!$valid) {
            return back()->withErrors([
                'code' => 'Kode OTP yang Anda masukkan tidak valid atau telah kadaluarsa.',
            ])->onlyInput('code');
        }

        // Generate 8 kode pemulihan darurat
        $recoveryCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $recoveryCodes[] = strtoupper(Str::random(5) . '-' . Str::random(5));
        }

        $user->update([
            'two_factor_enabled'        => true,
            'two_factor_recovery_codes' => $recoveryCodes,
            'two_factor_confirmed_at'   => now(),
        ]);

        session(['landlord_2fa_verified' => true]);

        LandlordAuditLog::record(
            event: '2fa_enabled',
            description: 'Mengaktifkan verifikasi Dua Langkah (2FA)'
        );

        return redirect()->route('landlord.dashboard')
            ->with('success', 'Verifikasi Dua Langkah berhasil diaktifkan!')
            ->with('recoveryCodes', $recoveryCodes);
    }

    /**
     * Tampilkan halaman verifikasi OTP saat login.
     */
    public function showVerify()
    {
        $user = Auth::guard('landlord')->user();

        if (!$user->two_factor_enabled) {
            return redirect()->route('landlord.2fa.setup');
        }

        if (session('landlord_2fa_verified')) {
            return redirect()->route('landlord.dashboard');
        }

        return Inertia::render('Landlord/Auth/TwoFactorVerify');
    }

    /**
     * Verifikasi kode OTP atau kode pemulihan saat login.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = Auth::guard('landlord')->user();
        $code = trim($request->code);

        // Cek apakah kode OTP 6 digit valid
        $valid = (new Google2FA())->verifyKey($user->two_factor_secret, $code);

        // Jika tidak valid, cek apakah merupakan recovery code
        if (!$valid && !empty($user->two_factor_recovery_codes)) {
            $recoveryCodes = $user->two_factor_recovery_codes;
            $index         = array_search(strtoupper($code), $recoveryCodes);

            if ($index !== false) {
                $valid = true;
                unset($recoveryCodes[$index]); // Hapus kode pemulihan yang sudah dipakai
                
                $user->update([
                    'two_factor_recovery_codes' => array_values($recoveryCodes),
                ]);

                LandlordAuditLog::record(
                    event: '2fa_recovery_used',
                    description: "Login 2FA menggunakan kode pemulihan: {$code}"
                );
            }
        }

        if (!$valid) {
            return back()->withErrors([
                'code' => 'Kode verifikasi tidak valid.',
            ])->onlyInput('code');
        }

        session(['landlord_2fa_verified' => true]);

        LandlordAuditLog::record(
            event: '2fa_verified',
            description: 'Verifikasi 2FA berhasil saat login'
        );

        return redirect()->intended(route('landlord.dashboard'));
    }

    /**
     * Nonaktifkan 2FA (memerlukan verifikasi password).
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = Auth::guard('landlord')->user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Password yang Anda masukkan salah.',
            ]);
        }

        $user->update([
            'two_factor_enabled'        => false,
            'two_factor_secret'         => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at'   => null,
        ]);

        session()->forget('landlord_2fa_verified');

        LandlordAuditLog::record(
            event: '2fa_disabled',
            description: 'Menonaktifkan verifikasi Dua Langkah (2FA)'
        );

        return back()->with('success', 'Verifikasi Dua Langkah berhasil dinonaktifkan.');
    }
}
