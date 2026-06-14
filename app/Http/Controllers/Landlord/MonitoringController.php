<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Tenant;
use App\Models\Central\TenantAllocation;
use App\Models\Central\TenantActivityLog;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MonitoringController extends Controller
{
    public function index()
    {
        $tenants = Tenant::all();
        $tenantData = [];

        foreach ($tenants as $tenant) {
            // Get allocation
            $allocation = TenantAllocation::firstOrCreate([
                'tenant_id' => $tenant->id,
            ], [
                'max_users' => 10,
                'storage_limit_mb' => 1024,
                'is_active' => true,
            ]);

            // Database size
            $dbName = $tenant->tenancy_db_name;
            try {
                $dbSizeResult = DB::select("
                    SELECT SUM(data_length + index_length) AS size 
                    FROM information_schema.TABLES 
                    WHERE table_schema = ?
                ", [$dbName]);
                $dbSizeBytes = $dbSizeResult[0]->size ?? 0;
                $dbSizeMb = round($dbSizeBytes / (1024 * 1024), 2);
            } catch (\Exception $e) {
                $dbSizeMb = 0.0;
            }

            // User count
            $userCount = 0;
            try {
                $tenant->run(function () use (&$userCount) {
                    $userCount = \App\Models\User::count();
                });
            } catch (\Exception $e) {
                $userCount = 0;
            }

            // Storage size
            $storageUsedMb = 0.0;
            try {
                $storageUsedMb = $allocation->getStorageUsedMb();
            } catch (\Exception $e) {
                $storageUsedMb = 0.0;
            }

            $tenantData[] = [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'db_name' => $dbName,
                'db_size_mb' => $dbSizeMb,
                'user_count' => $userCount,
                'max_users' => $allocation->max_users,
                'storage_used_mb' => $storageUsedMb,
                'storage_limit_mb' => $allocation->storage_limit_mb,
                'is_active' => (bool) $tenant->is_active,
            ];
        }

        // System info
        $diskFree = @disk_free_space(base_path()) ?: 0;
        $diskTotal = @disk_total_space(base_path()) ?: 1;
        $diskUsed = $diskTotal - $diskFree;
        $diskUsagePercentage = round(($diskUsed / $diskTotal) * 100, 2);

        $systemInfo = [
            'disk_free_gb' => round($diskFree / (1024 * 1024 * 1024), 2),
            'disk_total_gb' => round($diskTotal / (1024 * 1024 * 1024), 2),
            'disk_used_gb' => round($diskUsed / (1024 * 1024 * 1024), 2),
            'disk_usage_percent' => $diskUsagePercentage,
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'os' => PHP_OS,
        ];

        // Recent activity logs
        $logs = TenantActivityLog::with('tenant')
            ->latest()
            ->paginate(15);

        return Inertia::render('Landlord/Monitoring/Index', [
            'tenants' => $tenantData,
            'systemInfo' => $systemInfo,
            'logs' => $logs,
        ]);
    }
}
