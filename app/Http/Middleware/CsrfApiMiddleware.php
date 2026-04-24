<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CsrfApiMiddleware
{
    /**
     * Handle an incoming request.
     * CSRF Protection untuk API endpoints yang menerima form submissions
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip CSRF untuk GET requests dan OPTIONS (preflight)
        if (in_array($request->method(), ['GET', 'OPTIONS'])) {
            return $next($request);
        }

        // Skip CSRF untuk development environment
        if (app()->environment('local', 'testing')) {
            Log::info('CSRF skipped for development environment', [
                'method' => $request->method(),
                'endpoint' => $request->path(),
            ]);
            return $next($request);
        }

        // Validasi CSRF token
        $csrfToken = $request->header('X-CSRF-Token');
        $sessionToken = $request->session()->token();

        if (!$csrfToken || !hash_equals($sessionToken, $csrfToken)) {
            Log::warning('CSRF token validation failed', [
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'endpoint' => $request->path(),
                'method' => $request->method(),
                'provided_token' => $csrfToken ? 'present' : 'missing',
                'session_token' => $sessionToken ? 'present' : 'missing',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'CSRF token mismatch. Please refresh the page and try again.',
                'error' => 'CSRF_TOKEN_MISMATCH'
            ], 419); // 419 = CSRF token mismatch
        }

        // Log successful CSRF validation (optional, bisa dihapus di production)
        Log::info('CSRF token validated successfully', [
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'ip' => $request->ip(),
        ]);

        return $next($request);
    }
}

