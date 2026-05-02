<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class RecaptchaHelper
{
    /**
     * Verifikasi reCAPTCHA v2 (Checkbox)
     */
    public static function verifyV2($token): bool
{
    if (empty($token)) return false;

    // Cek token reuse
    $cacheKey = 'recaptcha_v2_used_' . md5($token);
    if (Cache::has($cacheKey)) {
        Log::warning('reCAPTCHA v2 Token Reuse Attempt', [
            'ip' => request()->ip()
        ]);
        return false;
    }

    try {
        $response = Http::timeout(5)
            ->asForm()
            ->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => config('services.recaptcha.v2_secret_key'),
                'response' => $token,
                'remoteip' => request()->ip(),
            ]);

        $data = $response->json();
        $success = $data['success'] ?? false;

        if ($success) {
            // Mark token sebagai sudah dipakai
            Cache::put($cacheKey, true, now()->addMinutes(2));
        } else {
            Log::warning('reCAPTCHA v2 Failed', [
                'errors' => $data['error-codes'] ?? [],
                'ip'     => request()->ip()
            ]);
        }

        return $success;

    } catch (\Exception $e) {
        Log::error('reCAPTCHA v2 Error: ' . $e->getMessage());
        return false;
    }
}

public static function verifyV3($token, $action = null): bool
{
    if (empty($token)) return false;

    try {
        $response = Http::timeout(5)
            ->asForm()
            ->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => config('services.recaptcha.v3_secret_key'),
                'response' => $token,
                'remoteip' => request()->ip(),
            ]);

        $data    = $response->json();
        $success = $data['success'] ?? false;
        
        if (!$success) {
            Log::error('reCAPTCHA v3 Verification Failed', [
                'errors' => $data['error-codes'] ?? [],
                'ip'     => request()->ip()
            ]);
            return false;
        }

        $score = $data['score'] ?? 0;
        
        // Log skor untuk analisa
        Log::info('reCAPTCHA v3 Score Log', [
            'score' => $score,
            'ip'    => request()->ip()
        ]);

        $data = $response->json();
        $success = $data['success'] ?? false;
        $score = $data['score'] ?? 0;

        Log::info('reCAPTCHA v3 Verification', [
            'success' => $success,
            'score'   => $score,
            'action'  => $data['action'] ?? '',
            'hostname' => $data['hostname'] ?? '',
            'ip'    => request()->ip()
        ]);

        $minScore = config('services.recaptcha.v3_min_score', 0.5);

        if ($score < $minScore) {
            Log::warning('reCAPTCHA v3 Score Too Low', [
                'score' => $score,
                'min_required' => $minScore
            ]);
            return false;
        }

        if ($action && ($data['action'] ?? '') !== $action) {
            Log::warning('reCAPTCHA v3 Action Mismatch', [
                'expected' => $action,
                'received' => $data['action'] ?? ''
            ]);
            return false;
        }

        return true;

    } catch (\Exception $e) {
        Log::error('reCAPTCHA v3 Error: ' . $e->getMessage());
        return false;
    }
}
}
