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

        // Daftar domain yang diizinkan
        $allowedOrigins = [
            'https://pemdescibatu2001.online',
            'https://admin-dscibatu.pemdescibatu2001.online',
            'http://sistem-desa-cibatu.test',
            'http://localhost',
            'http://127.0.0.1',
            'http://192.168.0.101:8080'
        ];

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

        // Auto-add API key untuk request yang valid (origin + User-Agent benar)
        // Frontend tidak perlu mengirim API key untuk keamanan
        $expectedApiKey = config('app.api_key');
        $apiKey = $request->header('X-API-Key');

        Log::info('PrivateApiMiddleware Debug', [
            'has_api_key' => !empty($apiKey),
            'api_key_length' => $apiKey ? strlen($apiKey) : 0,
            'expected_key_length' => strlen($expectedApiKey),
            'origin' => $origin,
            'user_agent' => $userAgent,
        ]);

        if (!$apiKey) {
            // Jika tidak ada API key, otomatis tambahkan untuk request yang valid
            $request->headers->set('X-API-Key', $expectedApiKey);

            Log::info('Auto-added API key for valid request', [
                'origin' => $origin,
                'user_agent' => $userAgent,
                'ip' => $request->ip(),
                'endpoint' => $request->path(),
            ]);
        } else {
            // Jika ada API key, validasi apakah benar
            if ($apiKey !== $expectedApiKey) {
                Log::warning('Invalid API key provided', [
                    'provided_key' => $apiKey,
                    'expected_key' => $expectedApiKey,
                    'origin' => $origin,
                    'user_agent' => $userAgent,
                    'ip' => $request->ip(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'API Key tidak valid'
                ], 401);
            }
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
