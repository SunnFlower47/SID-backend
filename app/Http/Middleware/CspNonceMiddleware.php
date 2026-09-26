<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Vite;

class CspNonceMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = base64_encode(random_bytes(16));
        $request->attributes->set('csp_nonce', $nonce);
        view()->share('csp_nonce', $nonce);
        
        if (class_exists(Vite::class)) {
            Vite::useCspNonce($nonce);
        }

        $response = $next($request);

        // Security headers hardening
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        $cspHeader = $this->buildCspHeader($nonce);
        $response->headers->set('Content-Security-Policy', $cspHeader);

        return $response;
    }

    /**
     * Build CSP header dengan nonce
     */
    private function buildCspHeader(string $nonce): string
    {
        // Whitelist yang sangat longgar untuk development agar tidak merusak admin panel
        $hosts = "http://localhost:5173 http://127.0.0.1:5173 ws://localhost:5173 ws://127.0.0.1:5173 http://localhost:5174 http://127.0.0.1:5174 ws://localhost:5174 ws://127.0.0.1:5174 http://localhost:5175 http://127.0.0.1:5175 ws://localhost:5175 ws://127.0.0.1:5175 http://localhost:9000 http://127.0.0.1:9000 https://pub-0a581c22a5f04777b366071e7b30d2ed.r2.dev";
        
        return implode('; ', [
            "default-src 'self' https://www.google.com https://www.gstatic.com https://www.recaptcha.net",
            "script-src 'self' 'nonce-{$nonce}' {$hosts} https://api-vilage.sunnflower.site https://www.google.com https://www.gstatic.com https://www.recaptcha.net https://unpkg.com https://cdn.jsdelivr.net https://cdn.tailwindcss.com https://static.cloudflareinsights.com",
            "style-src 'self' 'unsafe-inline' {$hosts} https://api-vilage.sunnflower.site https://fonts.googleapis.com https://fonts.bunny.net https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://cdn.tailwindcss.com",
            "font-src 'self' https://fonts.gstatic.com https://fonts.bunny.net https://cdnjs.cloudflare.com",
            "img-src 'self' data: blob: https: {$hosts} https://api-vilage.sunnflower.site",
            "connect-src 'self' {$hosts} https://api-vilage.sunnflower.site http://sistem-desa-cibatu.test https://admin-dscibatu.pemdescibatu2001.online https://pemdescibatu2001.online https://www.google.com https://www.gstatic.com https://www.recaptcha.net https://cdn.jsdelivr.net",
            "frame-src 'self' https://www.google.com https://www.gstatic.com https://www.recaptcha.net https://recaptcha.net",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self' https://www.google.com https://www.recaptcha.net",
            "media-src 'self' data:"
        ]);
    }
}
