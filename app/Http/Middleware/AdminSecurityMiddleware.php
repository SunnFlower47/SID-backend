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
        // Pola berbahaya yang langsung di-BLOCK (bukan hanya di-log)
        $blockPatterns = [
            'sql' => ['union select', 'drop table', 'truncate table', 'delete from', 'insert into'],
            'xss' => ['<script', 'javascript:', 'onload=', 'onerror=', 'onclick='],
            'path' => ['../', '..\\', '/etc/passwd', '/etc/shadow'],
        ];

        // Pola yang hanya di-log (suspicious tapi bisa false positive)
        $warnPatterns = [
            'sql_warn' => ['select ', 'update ', ' where '],
        ];

        $requestData = strtolower($request->getContent() . ' ' . $request->getQueryString());

        foreach ($blockPatterns as $type => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($requestData, $pattern) !== false) {
                    Log::warning('Suspicious activity BLOCKED', [
                        'type'       => $type,
                        'pattern'    => $pattern,
                        'ip'         => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'url'        => $request->url(),
                        'user_id'    => auth()->id(),
                        'timestamp'  => now(),
                    ]);
                    abort(403, 'Suspicious activity detected.');
                }
            }
        }

        foreach ($warnPatterns as $type => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($requestData, $pattern) !== false) {
                    Log::warning('Suspicious activity detected (warning only)', [
                        'type'    => $type,
                        'ip'      => $request->ip(),
                        'url'     => $request->url(),
                        'timestamp' => now(),
                    ]);
                    break;
                }
            }
        }
    }
}
