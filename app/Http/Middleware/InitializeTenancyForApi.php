<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class InitializeTenancyForApi
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek header X-Tenant (dikirim oleh Next.js proxy atau client)
        $tenantId = $request->header('X-Tenant');
        
        // 2. Jika tidak ada header, coba deteksi dari subdomain host
        if (!$tenantId) {
            $host = $request->getHost();
            $centralDomains = config('tenancy.central_domains', []);
            
            // Cek apakah host saat ini bukan central domain
            if (!in_array($host, $centralDomains)) {
                $parts = explode('.', $host);
                if (count($parts) >= 3) {
                    $tenantId = $parts[0];
                }
            }
        }
        
        // 3. Inisialisasi Tenancy jika tenantId ditemukan
        if ($tenantId) {
            try {
                if (!tenancy()->initialized || tenant('id') !== $tenantId) {
                    tenancy()->initialize($tenantId);
                }

                // Check if the tenant is active
                $isActive = tenant('is_active');
                if ($isActive === false || $isActive === 0 || $isActive === '0') {
                    Log::warning('[InitializeTenancyForApi] Blocking API access to inactive tenant: ' . $tenantId);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Desa dinonaktifkan.',
                        'error' => 'TENANT_INACTIVE',
                        'diskominfo_hotline' => \App\Models\Central\CentralSetting::get('diskominfo_hotline', '081234567890'),
                        'diskominfo_email' => \App\Models\Central\CentralSetting::get('diskominfo_email', 'admin@central.go.id'),
                    ], 403);
                }
            } catch (\Exception $e) {
                Log::error('API Tenancy Initialization Failed', [
                    'tenant_id' => $tenantId,
                    'error' => $e->getMessage()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Desa tidak ditemukan atau tidak aktif.',
                    'error' => 'TENANT_NOT_FOUND'
                ], 404);
            }
        } else {
            Log::warning('API Tenancy Not Initialized: No tenant identified', [
                'url' => $request->fullUrl(),
                'host' => $request->getHost()
            ]);
        }
        
        return $next($request);
    }
}
