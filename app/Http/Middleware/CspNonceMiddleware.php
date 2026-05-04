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
        $hosts = "http://localhost:5173 http://127.0.0.1:5173 ws://localhost:5173 ws://127.0.0.1:5173";
        
        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}' 'unsafe-inline' 'unsafe-eval' {$hosts} https://www.google.com https://www.gstatic.com https://www.recaptcha.net https://unpkg.com https://cdn.jsdelivr.net https://cdn.tailwindcss.com",
            "script-src-attr 'unsafe-inline'",
            "style-src 'self' 'unsafe-inline' {$hosts} https://fonts.googleapis.com https://fonts.bunny.net https://cdnjs.cloudflare.com https://cdn.jsdelivr.net",
            "font-src 'self' https://fonts.gstatic.com https://fonts.bunny.net https://cdnjs.cloudflare.com",
            "img-src 'self' data: https: {$hosts}",
            "connect-src 'self' {$hosts} http://sistem-desa-cibatu.test https://admin-dscibatu.pemdescibatu2001.online https://pemdescibatu2001.online https://www.google.com https://www.gstatic.com https://www.recaptcha.net https://cdn.jsdelivr.net",
            "frame-src 'self' https://www.google.com https://www.recaptcha.net",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
            "media-src 'self' data:"
        ]);
    }
}
