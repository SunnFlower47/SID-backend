<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\RecaptchaHelper;
use Symfony\Component\HttpFoundation\Response;

class VerifyRecaptcha
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null $version (v2 atau v3)
     */
    public function handle(Request $request, Closure $next, $version = null): Response
    {
        if (!config('services.recaptcha.enabled')) {
            return $next($request);
        }

        // 1. Cek reCAPTCHA v2 (Checkbox) - Biasanya dikirim via Header X-Recaptcha-Token
        $v2Token = $request->header('X-Recaptcha-Token') ?? $request->input('g-recaptcha-response');
        
        // 2. Cek reCAPTCHA v3 (Invisible) - Bisa via Header atau Input (form blade)
        $v3Token = $request->header('X-Recaptcha-V3-Token') ?? $request->input('recaptcha_token');
        $minScore = config('services.recaptcha.v3_min_score', 0.5);

        // Jika rute mewajibkan V2 secara spesifik
        if ($version === 'v2') {
            if (!$v2Token || !RecaptchaHelper::verifyV2($v2Token)) {
                if ($request->header('X-Inertia')) {
                    return back()->withInput()->withErrors([
                        'g-recaptcha-response' => 'Verifikasi reCAPTCHA v2 gagal atau token kadaluwarsa.',
                        'recaptcha_token' => 'Verifikasi reCAPTCHA v2 gagal atau token kadaluwarsa.'
                    ]);
                }
                return response()->json([
                    'success' => false,
                    'message' => 'Verifikasi reCAPTCHA v2 gagal atau token kadaluwarsa.',
                    'error' => 'RECAPTCHA_V2_FAILED'
                ], 403);
            }
            return $next($request);
        }

        // Jika rute mewajibkan V3 secara spesifik
        if ($version === 'v3') {
            if (!$v3Token || !RecaptchaHelper::verifyV3($v3Token)) {
                if ($request->header('X-Inertia')) {
                    return back()->withInput()->withErrors([
                        'recaptcha_token' => 'Verifikasi keamanan (v3) gagal.',
                        'g-recaptcha-response' => 'Verifikasi keamanan (v3) gagal.'
                    ]);
                }
                return response()->json([
                    'success' => false,
                    'message' => 'Verifikasi keamanan (v3) gagal.',
                    'error' => 'RECAPTCHA_V3_FAILED'
                ], 403);
            }
            return $next($request);
        }

        // Default: Cek salah satu (V2 diprioritaskan untuk Form Submit)
        $errorMsg = null;
        $errorCode = null;

        if ($v2Token) {
            if (!RecaptchaHelper::verifyV2($v2Token)) {
                $errorMsg = 'Verifikasi reCAPTCHA gagal.';
                $errorCode = 'RECAPTCHA_FAILED';
            }
        } elseif ($v3Token) {
            if (!RecaptchaHelper::verifyV3($v3Token)) {
                $errorMsg = 'Verifikasi keamanan gagal.';
                $errorCode = 'RECAPTCHA_FAILED';
            }
        } else {
            // Jika tidak ada token sama sekali
            $errorMsg = 'Keamanan tambahan (reCAPTCHA) diperlukan.';
            $errorCode = 'RECAPTCHA_REQUIRED';
        }

        if ($errorMsg) {
            // Jika request adalah Inertia
            if ($request->header('X-Inertia')) {
                return back()->withInput()->withErrors([
                    'recaptcha_token' => $errorMsg,
                    'g-recaptcha-response' => $errorMsg,
                ]);
            }

            // Jika request meminta JSON (API/AJAX)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMsg,
                    'error' => $errorCode
                ], 403);
            }

            // Jika rute Web biasa (Blade)
            return back()->withInput()->with('error', $errorMsg);
        }

        return $next($request);
    }
}
