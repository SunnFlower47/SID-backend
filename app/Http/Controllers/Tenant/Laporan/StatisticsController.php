<?php

namespace App\Http\Controllers\Tenant\Laporan;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Penduduk;
use App\Models\Mutasi;
use App\Models\PendudukDomisili;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\Kependudukan\VillageStatisticsService;

class StatisticsController extends Controller
{
    protected $statsService;

    public function __construct(VillageStatisticsService $statsService)
    {
        $this->middleware(['auth', 'can:laporan.view']);
        $this->statsService = $statsService;
    }

    /**
     * Display statistics dashboard.
     */
    public function index()
    {
        Gate::authorize('laporan.view');

        return Inertia::render('Tenant/Laporan/Statistik/Index', [
            'basicStats' => Inertia::defer(fn() => [
                'total_penduduk' => once(fn() => $this->statsService->getDashboardStats())['basic']['total_penduduk'],
                'laki_laki' => once(fn() => $this->statsService->getDashboardStats())['basic']['laki_laki'],
                'perempuan' => once(fn() => $this->statsService->getDashboardStats())['basic']['perempuan'],
                'total_kk' => once(fn() => $this->statsService->getDashboardStats())['basic']['total_kk'],
                'total_domisili' => \App\Models\PendudukDomisili::where('status', 'aktif')->count(),
                'total_mutasi' => \App\Models\Mutasi::count(),
            ]),
            'genderStats' => Inertia::defer(fn() => once(fn() => $this->statsService->getDetailedStats())['gender']),
            'ageGroups' => Inertia::defer(fn() => once(fn() => $this->statsService->getDashboardStats())['age_groups']),
            'religionStats' => Inertia::defer(fn() => Cache::remember('agama_stats_v2', 3600, function() {
                return DB::table('penduduks')->select('agama', DB::raw('count(*) as total'))->whereNull('deleted_at')->groupBy('agama')->orderByDesc('total')->get();
            })),
            'educationStats' => Inertia::defer(fn() => Cache::remember('pendidikan_stats_v2', 3600, function() {
                return DB::table('penduduks')->select('pendidikan', DB::raw('count(*) as total'))->whereNull('deleted_at')->groupBy('pendidikan')->orderByDesc('total')->get();
            })),
            'jobStats' => Inertia::defer(fn() => Cache::remember('pekerjaan_stats_v2', 3600, function() {
                return DB::table('penduduks')->select('pekerjaan', DB::raw('count(*) as total'))->whereNull('deleted_at')->groupBy('pekerjaan')->orderByDesc('total')->limit(10)->get();
            })),
            'rtStats' => Inertia::defer(fn() => once(fn() => $this->statsService->getDetailedStats())['rt_distribution']),
            'rwStats' => Inertia::defer(fn() => once(fn() => $this->statsService->getDetailedStats())['rw_distribution']),
            'mutationStats' => Inertia::defer(fn() => once(fn() => $this->statsService->getDashboardStats())['mutasi']),
            'recentMutations' => Inertia::defer(fn() => Cache::remember('recent_mutasi_detailed_v2', 600, function() {
                return Mutasi::with(['penduduk' => fn($q) => $q->withTrashed()])->orderBy('created_at', 'desc')->limit(10)->get();
            })),
        ]);
    }
}
