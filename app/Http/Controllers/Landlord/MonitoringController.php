<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Tenant;
use App\Models\Central\TenantAllocation;
use App\Models\Central\TenantActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
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

            // User count & Health Check
            $userCount = 0;
            $dbHealthy = true;
            try {
                $tenant->run(function () use (&$userCount) {
                    $userCount = \App\Models\User::count();
                });
            } catch (\Exception $e) {
                $userCount = 0;
                $dbHealthy = false;
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
                'db_healthy' => $dbHealthy,
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

        // Recent activity logs (Tenant)
        $logs = TenantActivityLog::with('tenant')
            ->latest()
            ->paginate(15, ['*'], 'tenant_page');

        // Landlord audit logs (Security & Admin actions)
        $landlordLogs = \App\Models\Central\LandlordAuditLog::latest()
            ->paginate(15, ['*'], 'landlord_page');

        // Fetch and parse Laravel Error logs
        $laravelLogs = $this->parseLaravelLog();

        return Inertia::render('Landlord/Monitoring/Index', [
            'tenants' => $tenantData,
            'systemInfo' => $systemInfo,
            'logs' => $logs,
            'landlordLogs' => $landlordLogs,
            'laravelLogs' => $laravelLogs,
        ]);
    }

    public function clearLogs()
    {
        Gate::authorize('manage-central-users'); // Superadmin privilege

        $logPath = storage_path('logs/laravel.log');
        if (file_exists($logPath)) {
            file_put_contents($logPath, '');
        }

        return redirect()->back()->with('success', 'Laravel error logs berhasil dibersihkan.');
    }

    private function parseLaravelLog()
    {
        $logPath = storage_path('logs/laravel.log');
        if (!file_exists($logPath)) {
            return [];
        }

        // Limit reading to the last 150KB of the file for performance
        $maxBytes = 150 * 1024;
        $fileSize = filesize($logPath);
        
        if ($fileSize > $maxBytes) {
            $fp = fopen($logPath, 'r');
            fseek($fp, $fileSize - $maxBytes);
            $fileContent = fread($fp, $maxBytes);
            fclose($fp);
            // Trim partial line at the beginning
            $firstNewLine = strpos($fileContent, "\n");
            if ($firstNewLine !== false) {
                $fileContent = substr($fileContent, $firstNewLine + 1);
            }
        } else {
            $fileContent = file_get_contents($logPath);
        }

        $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.*?)(?=\n\[\d{4}-\d{2}-\d{2}|\z)/s';
        preg_match_all($pattern, $fileContent, $matches, PREG_SET_ORDER);
        
        $parsedLogs = [];
        foreach ($matches as $match) {
            $parsedLogs[] = [
                'timestamp' => $match[1],
                'env' => $match[2],
                'level' => strtoupper($match[3]),
                'message' => trim($match[4]),
            ];
        }
        
        return array_slice(array_reverse($parsedLogs), 0, 100);
    }
}
