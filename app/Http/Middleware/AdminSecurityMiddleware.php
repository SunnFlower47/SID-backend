<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminSecurityMiddleware
{
    /**
     * Handle an incoming request.
     * Enhanced security middleware untuk admin panel
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log admin access attempts
        Log::info('Admin panel access', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->url(),
            'method' => $request->method(),
            'user_id' => auth()->id(),
            'timestamp' => now(),
        ]);

        // Check for suspicious patterns
        $this->checkSuspiciousActivity($request);

        // Add security headers
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }

    /**
     * Check for suspicious activity patterns
     */
    private function checkSuspiciousActivity(Request $request): void
    {
        $suspiciousPatterns = [
            'sql' => ['union', 'select', 'insert', 'update', 'delete', 'drop'],
            'xss' => ['<script', 'javascript:', 'onload=', 'onerror='],
            'path' => ['../', '..\\', '/etc/', '/var/', 'C:\\'],
        ];

        $requestData = strtolower($request->getContent() . ' ' . $request->getQueryString());

        foreach ($suspiciousPatterns as $type => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($requestData, $pattern) !== false) {
                    Log::warning('Suspicious activity detected', [
                        'type' => $type,
                        'pattern' => $pattern,
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'url' => $request->url(),
                        'data' => substr($requestData, 0, 500), // Limit log size
                        'timestamp' => now(),
                    ]);
                    break;
                }
            }
        }
    }
}
