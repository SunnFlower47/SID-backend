<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class RecaptchaResetPasswordMiddleware
{
    /**
     * Handle an incoming request.
     * Middleware untuk validasi reCAPTCHA v2 di reset password
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya validasi untuk reset password
        if (!$request->is('reset-password') && !$request->is('auth/reset-password')) {
            return $next($request);
        }

        // Skip reCAPTCHA validation untuk development/local environment
        if (app()->environment('local', 'testing') || config('app.debug')) {
            return $next($request);
        }

        $recaptchaResponse = $request->input('g-recaptcha-response');

        if (!$recaptchaResponse) {
            return back()->withErrors([
                'g-recaptcha-response' => 'Mohon verifikasi bahwa Anda bukan robot.'
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        // Verify reCAPTCHA v2
        $secretKey = config('services.recaptcha.v2_secret_key');

        if (!$secretKey) {
            return back()->withErrors([
                'g-recaptcha-response' => 'Konfigurasi reCAPTCHA tidak valid.'
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secretKey,
            'response' => $recaptchaResponse,
            'remoteip' => $request->ip()
        ]);

        $recaptchaData = $response->json();

        if (!$recaptchaData['success']) {
            return back()->withErrors([
                'g-recaptcha-response' => 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.'
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        return $next($request);
    }
}
