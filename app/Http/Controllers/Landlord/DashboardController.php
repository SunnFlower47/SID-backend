<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Tenant;
use App\Models\Central\TenantAllocation;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('data->is_active', true)->count(),
            'total_users_limit' => TenantAllocation::sum('max_users'),
            'total_storage_limit' => TenantAllocation::sum('storage_limit_mb'),
        ];

        return Inertia::render('Landlord/Dashboard/Index', [
            'stats' => $stats
        ]);
    }
}

