<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class PrivateApiMiddleware
{
    /**
     * Handle an incoming request.
     * Middleware untuk memastikan hanya frontend internal yang bisa akses
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Debug: Log bahwa middleware dijalankan
        Log::info('PrivateApiMiddleware EXECUTED', [
            'url' => $request->url(),
            'method' => $request->method(),
        ]);

        // Cek origin request
        $origin = $request->header('Origin');
        $referer = $request->header('Referer');
        $userAgent = $request->header('User-Agent');

        // Daftar domain yang diizinkan (Konfigurasi dari cors.php)
        $allowedOrigins = config('cors.allowed_origins', []);


        // Cek apakah request dari domain yang diizinkan (dengan caching)
        $isAllowedOrigin = false;
        $cacheKey = 'allowed_origin_' . md5($origin . $referer);

        $isAllowedOrigin = cache()->remember($cacheKey, 3600, function() use ($origin, $referer, $allowedOrigins) {
            if ($origin) {
                return in_array($origin, $allowedOrigins);
            } elseif ($referer) {
                foreach ($allowedOrigins as $allowedOrigin) {
                    if (strpos($referer, $allowedOrigin) === 0) {
                        return true;
                    }
                }
            }
            return false;
        });

        // Debug: Log kondisi origin (hanya untuk request gagal)
        if (!$isAllowedOrigin) {
            Log::warning('Origin Check Failed', [
                'origin' => $origin,
                'referer' => $referer,
                'is_allowed_origin' => $isAllowedOrigin,
                'ip' => $request->ip(),
            ]);
        }

        $secretKey = config('app.api_key'); // Kita gunakan API_KEY sebagai Secret
        $signature = $request->header('X-Signature');
        $timestamp = $request->header('X-Timestamp');

        // 1. Cek apakah header lengkap
        if (!$signature || !$timestamp) {
            return response()->json([
                'success' => false,
                'message' => 'Security Headers Missing',
                'error' => 'SIGNATURE_REQUIRED'
            ], 403);
        }

        // 2. Cek Kadaluwarsa (Toleransi 30 detik)
        $currentTime = time();
        if (abs($currentTime - $timestamp) > 30) {
            return response()->json([
                'success' => false,
                'message' => 'Request Expired (Time Sync Error)',
                'error' => 'REQUEST_TIMEOUT'
            ], 403);
        }

        // 3. Validasi Tanda Tangan (HMAC)
        // Rumus: hash_hmac('sha256', timestamp + method + path, secret)
        $path = $request->path();
        $message = $timestamp . strtoupper($request->method()) . $path;
        $expectedSignature = hash_hmac('sha256', $message, $secretKey);

        if (!hash_equals($expectedSignature, $signature)) {
            Log::warning('Digital Signature Mismatch', [
                'ip' => $request->ip(),
                'provided' => $signature,
                // Zero Trust: Jangan pernah catat 'expected signature' di log!
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid Digital Signature',
                'error' => 'INVALID_SIGNATURE'
            ], 403);
        }

        // User-Agent WAJIB ada dan harus dari browser
        if (!$userAgent) {
            Log::warning('Missing User-Agent', [
                'ip' => $request->ip(),
                'origin' => $origin,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'User-Agent header diperlukan'
            ], 403);
        }

        // Optimasi User-Agent validation dengan caching
        $userAgentCacheKey = 'user_agent_' . md5($userAgent);
        $isBrowserRequest = cache()->remember($userAgentCacheKey, 3600, function() use ($userAgent) {
            return (
                strpos($userAgent, 'Mozilla') !== false ||
                strpos($userAgent, 'Chrome') !== false ||
                strpos($userAgent, 'Safari') !== false ||
                strpos($userAgent, 'Firefox') !== false ||
                strpos($userAgent, 'Edge') !== false
            );
        });

        // Debug: Log kondisi (hanya untuk request gagal)
        if (!$isBrowserRequest) {
            Log::warning('Non-browser request detected', [
                'user_agent' => $userAgent,
                'is_browser_request' => $isBrowserRequest,
                'ip' => $request->ip(),
            ]);
        }

        // Jika bukan dari domain yang diizinkan ATAU bukan browser request
        if (!$isAllowedOrigin || !$isBrowserRequest) {
            Log::warning('Private API Access Denied', [
                'ip' => $request->ip(),
                'origin' => $origin,
                'referer' => $referer,
                'user_agent' => $userAgent,
                'endpoint' => $request->path(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Access denied: Api Key Required , Origin Invalid',
                'error' => 'PRIVATE_API_ACCESS_DENIED'
            ], 403);
        }

        return $next($request);
    }
}
