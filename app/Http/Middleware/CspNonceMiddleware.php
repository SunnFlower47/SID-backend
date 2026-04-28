<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CspNonceMiddleware
{
    /**
     * Handle an incoming request.
     * Generate CSP nonce untuk script dan style tags
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Generate nonce untuk setiap request
        $nonce = base64_encode(random_bytes(16));

        // Store nonce di request untuk digunakan di views
        $request->attributes->set('csp_nonce', $nonce);

        // Store nonce di view untuk digunakan di templates
        view()->share('csp_nonce', $nonce);

        $response = $next($request);

        // Add CSP header dengan nonce
        $cspHeader = $this->buildCspHeader($nonce);
        $response->headers->set('Content-Security-Policy', $cspHeader);

        return $response;
    }

    /**
     * Build CSP header dengan nonce
     */
    private function buildCspHeader(string $nonce): string
    {
        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}' 'unsafe-inline' 'unsafe-eval' https://www.google.com https://www.gstatic.com https://www.recaptcha.net https://unpkg.com https://cdn.jsdelivr.net https://cdn.tailwindcss.com",
            "script-src-attr 'unsafe-inline'",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net https://cdnjs.cloudflare.com https://cdn.jsdelivr.net",
            "font-src 'self' https://fonts.gstatic.com https://fonts.bunny.net https://cdnjs.cloudflare.com",
            "img-src 'self' data: https:",
            "connect-src 'self' http://sistem-desa-cibatu.test/ https://admin-dscibatu.pemdescibatu2001.online https://pemdescibatu2001.online https://www.google.com https://www.gstatic.com https://www.recaptcha.net",
            "frame-src 'self' https://www.google.com https://www.recaptcha.net",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'"
        ]);
    }
}

