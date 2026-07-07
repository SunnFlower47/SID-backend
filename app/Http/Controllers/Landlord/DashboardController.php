<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Tenant;
use App\Models\Central\TenantAllocation;
use App\Models\Central\TenantActivityLog;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $tenants = Tenant::all();

        // 1. Basic stats
        $stats = [
            'total_tenants' => $tenants->count(),
            'active_tenants' => $tenants->filter(function($t) {
                return (bool) ($t->is_active ?? true);
            })->count(),
            'total_users_limit' => (int) TenantAllocation::sum('max_users'),
            'total_storage_limit' => (int) TenantAllocation::sum('storage_limit_mb'),
        ];

        // 2. Registration Trend (last 6 months, database-agnostic)
        $registrationTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $key = $date->format('Y-m');
            $registrationTrend[$key] = [
                'month' => $date->translatedFormat('F'),
                'count' => 0
            ];
        }

        foreach ($tenants as $tenant) {
            if ($tenant->created_at) {
                $key = $tenant->created_at->format('Y-m');
                if (isset($registrationTrend[$key])) {
                    $registrationTrend[$key]['count']++;
                }
            }
        }
        $registrationTrend = array_values($registrationTrend);

        // 3. Storage & User Distribution per Tenant
        $storageDistribution = [];
        $userDistribution = [];

        foreach ($tenants as $tenant) {
            $allocation = TenantAllocation::firstOrCreate([
                'tenant_id' => $tenant->id,
            ], [
                'max_users' => 10,
                'storage_limit_mb' => 1024,
                'is_active' => true,
            ]);

            // Storage
            $storageUsed = 0.0;
            try {
                $storageUsed = $allocation->getStorageUsedMb();
            } catch (\Exception $e) {
                // ignore
            }

            $storageDistribution[] = [
                'name' => $tenant->name,
                'used' => $storageUsed,
                'limit' => $allocation->storage_limit_mb,
            ];

            // Users
            $userCount = 0;
            try {
                $tenant->run(function () use (&$userCount) {
                    $userCount = \App\Models\User::count();
                });
            } catch (\Exception $e) {
                // ignore
            }

            $userDistribution[] = [
                'name' => $tenant->name,
                'active_users' => $userCount,
                'max_users' => $allocation->max_users,
            ];
        }

        // Sort and slice storage (top 5)
        usort($storageDistribution, function($a, $b) {
            return $b['used'] <=> $a['used'];
        });
        $storageDistribution = array_slice($storageDistribution, 0, 5);

        // Sort and slice users (top 5)
        usort($userDistribution, function($a, $b) {
            return $b['active_users'] <=> $a['active_users'];
        });
        $userDistribution = array_slice($userDistribution, 0, 5);

        // 4. Recent registered tenants (last 5)
        $recentTenants = Tenant::latest()
            ->take(5)
            ->get()
            ->map(function($tenant) {
                return [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'created_at' => $tenant->created_at ? $tenant->created_at->toISOString() : null,
                    'is_active' => (bool) ($tenant->is_active ?? true),
                ];
            });

        // 5. Recent activity logs (last 5)
        $recentLogs = TenantActivityLog::with('tenant')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($log) {
                return [
                    'id' => $log->id,
                    'tenant_name' => $log->tenant?->name ?? 'Sistem Pusat',
                    'action' => $log->action,
                    'description' => $log->description,
                    'created_at' => $log->created_at->toISOString(),
                ];
            });

        return Inertia::render('Landlord/Dashboard/Index', [
            'stats' => $stats,
            'charts' => [
                'registration_trend' => $registrationTrend,
                'storage_distribution' => $storageDistribution,
                'user_distribution' => $userDistribution,
            ],
            'recentTenants' => $recentTenants,
            'recentLogs' => $recentLogs,
        ]);
    }
}

