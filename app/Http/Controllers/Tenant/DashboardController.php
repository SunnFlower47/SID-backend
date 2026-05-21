<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;

use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use App\Models\Mutasi;
use App\Models\SuratType;
use App\Models\SuratPengajuan;
use App\Services\Kependudukan\VillageStatisticsService;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected $statsService;

    public function __construct(VillageStatisticsService $statsService)
    {
        $this->statsService = $statsService;
    }

    /**
     * Display the dashboard with cached statistics.
     */
    public function index()
    {
        return inertia('Tenant/Dashboard/Index', [
            'stats' => Inertia::defer(fn() => once(fn() => $this->statsService->getDashboardStats())['basic']),
            'mutasiStats' => Inertia::defer(fn() => once(fn() => $this->statsService->getDashboardStats())['mutasi']),
            'suratStats' => Inertia::defer(fn() => once(fn() => $this->statsService->getDashboardStats())['surat']),
            'recentMutasi' => Inertia::defer(fn() => Cache::remember('dashboard_recent_mutasi', 60, function () {
                return \App\Models\Mutasi::with(['penduduk' => function($q) {
                    $q->withTrashed();
                }])->latest()->take(5)->get();
            })),
            'ageGroups' => Inertia::defer(fn() => once(fn() => $this->statsService->getDashboardStats())['age_groups']),
            'mutationTrends' => Inertia::defer(fn() => once(fn() => $this->statsService->getDashboardStats())['mutation_trends']),
        ]);
    }

    /**
     * Refresh dashboard cache manually.
     */
    public function refresh()
    {
        $this->statsService->clearStats();
        return redirect()->route('dashboard')->with('success', 'Data dashboard berhasil diperbarui!');
    }
}
