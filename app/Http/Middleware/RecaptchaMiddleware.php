<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class RecaptchaMiddleware
{
    /**
     * Handle an incoming request.
     * Middleware untuk validasi reCAPTCHA v3 di backend login
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya validasi untuk login
        if (!$request->is('login') && !$request->is('auth/login')) {
            return $next($request);
        }

        // Skip reCAPTCHA validation untuk development/local environment
        if (!config('services.recaptcha.enabled')) {
            return $next($request);
        }

        $recaptchaToken = $request->header('X-Recaptcha-Token') ?? $request->input('recaptcha_token');

        if (!$recaptchaToken) {
            return back()->withErrors([
                'recaptcha_token' => 'Mohon verifikasi bahwa Anda bukan robot.'
            ])->withInput($request->except('password'));
        }

        // Verify reCAPTCHA v3
        $secretKey = config('services.recaptcha.v3_secret_key');

        if (!$secretKey) {
            return back()->withErrors([
                'recaptcha_token' => 'Konfigurasi reCAPTCHA tidak valid.'
            ])->withInput($request->except('password'));
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secretKey,
            'response' => $recaptchaToken,
            'remoteip' => $request->ip()
        ]);

        $recaptchaData = $response->json();

        if (!$recaptchaData['success']) {
            return back()->withErrors([
                'recaptcha_token' => 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.'
            ])->withInput($request->except('password'));
        }

        // Check score for v3 (0.0 to 1.0, higher is better)
        $score = $recaptchaData['score'] ?? 0;
        $minScore = config('services.recaptcha.v3_min_score', 0.5);

        if ($score < $minScore) {
            return back()->withErrors([
                'recaptcha_token' => 'Akses ditolak. Silakan coba lagi.'
            ])->withInput($request->except('password'));
        }

        return $next($request);
    }
}
