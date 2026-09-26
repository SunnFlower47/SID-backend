<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (tenancy()->initialized) {
            $isActive = tenant('is_active');
            // Check if explicitly inactive
            if ($isActive === false || $isActive === 0 || $isActive === '0') {
                \Illuminate\Support\Facades\Log::warning('[CheckTenantActive] Blocking access to inactive tenant: ' . tenant('id'));
                
                if (auth()->check()) {
                    auth()->logout();
                    session()->invalidate();
                    session()->regenerateToken();
                }
                
                abort(403, 'Desa dinonaktifkan.');
            }
        }

        return $next($request);
    }
}
