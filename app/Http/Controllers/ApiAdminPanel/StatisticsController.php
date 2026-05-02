<?php

namespace App\Http\Controllers\ApiAdminPanel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Penduduk;
use App\Models\Mutasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\JsonResponse;

class StatisticsController extends Controller
{
    /**
     * Display statistics dashboard.
     */
    public function index(): JsonResponse
    {
        Gate::authorize('laporan_statistik');

        try {
            // Real-time basic stats
            $basicStats = DB::table('penduduks as p')
                ->select([
                    DB::raw('COUNT(*) as total_penduduk'),
                    DB::raw('COUNT(DISTINCT CASE WHEN p.nkk IS NOT NULL AND p.nkk != "" THEN p.nkk END) as total_kk'),
                ])
                ->whereNull('p.deleted_at')
                ->first();

            $kkBermasalahCount = DB::table('kartu_keluargas')
                ->whereIn('status_kk', ['bermasalah', 'bermasalah_sementara'])
                ->count();

            $suratPendingCount = 0;
            if (Schema::hasTable('surat_pengajuans')) {
                $suratPendingCount = DB::table('surat_pengajuans')
                    ->where('status', 'PENDING')
                    ->count();
            }

            $totalMutasi = Mutasi::count();

            // Group statistics with Joins
            $groupStats = DB::table('penduduks as p')
                ->leftJoin('rts', 'p.rt_id', '=', 'rts.id')
                ->leftJoin('dusuns', 'p.dusun_id', '=', 'dusuns.id')
                ->select([
                    'p.jenis_kelamin',
                    'rts.kode as rt_kode',
                    'dusuns.nama as dusun_nama',
                    'p.status_perkawinan',
                    'p.kedudukan_keluarga',
                    DB::raw('COUNT(*) as total')
                ])
                ->whereNull('p.deleted_at')
                ->groupBy('p.jenis_kelamin', 'rt_kode', 'dusun_nama', 'p.status_perkawinan', 'p.kedudukan_keluarga')
                ->get();

            // Process gender stats
            $genderStats = $groupStats->where('jenis_kelamin', '!=', null)
                ->groupBy(function($item) {
                    $val = strtoupper(trim($item->jenis_kelamin));
                    if ($val === 'LAKI-LAKI') return 'LAKI-LAKI';
                    if ($val === 'PEREMPUAN') return 'PEREMPUAN';
                    return 'LAINNYA';
                })
                ->map(fn($group) => $group->sum('total'));

            // Process RT stats (using master code)
            $rtStats = $groupStats->where('rt_kode', '!=', null)
                ->groupBy('rt_kode')
                ->map(fn($group) => ['rt' => $group->first()->rt_kode, 'total' => $group->sum('total')])
                ->sortBy('rt')
                ->values();

            // Process Dusun stats (using master name)
            $dusunStats = $groupStats->where('dusun_nama', '!=', null)
                ->groupBy('dusun_nama')
                ->map(fn($group) => ['dusun' => $group->first()->dusun_nama, 'total' => $group->sum('total')])
                ->sortBy('dusun')
                ->values();

            // Age group statistics
            $ageGroups = DB::table('penduduks as p')
                ->leftJoin('mutasis as m', function($join) {
                    $join->on('m.penduduk_id', '=', 'p.id')
                         ->whereIn('m.jenis_mutasi', ['kematian', 'pindah_keluar']);
                })
                ->select(
                    DB::raw('CASE
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) < 5 THEN "0-4"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 5 AND 9 THEN "5-9"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 10 AND 14 THEN "10-14"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 15 AND 19 THEN "15-19"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 20 AND 24 THEN "20-24"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 25 AND 29 THEN "25-29"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 30 AND 34 THEN "30-34"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 35 AND 39 THEN "35-39"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 40 AND 44 THEN "40-44"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 45 AND 49 THEN "45-49"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 50 AND 54 THEN "50-54"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 55 AND 59 THEN "55-59"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 60 AND 64 THEN "60-64"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) >= 65 THEN "65+"
                        ELSE "Tidak Diketahui"
                    END as age_group'),
                    DB::raw('COUNT(CASE WHEN m.id IS NULL THEN 1 END) as total')
                )
                ->whereNull('m.id')
                ->whereNotNull('p.tanggal_lahir')
                ->groupBy('age_group')
                ->get();

            // Religion statistics
            $religionStats = DB::table('penduduks as p')
                ->leftJoin('mutasis as m', function($join) {
                    $join->on('m.penduduk_id', '=', 'p.id')
                         ->whereIn('m.jenis_mutasi', ['kematian', 'pindah_keluar']);
                })
                ->select('p.agama', DB::raw('COUNT(CASE WHEN m.id IS NULL THEN 1 END) as total'))
                ->whereNull('m.id')
                ->groupBy('p.agama')
                ->orderBy('total', 'desc')
                ->get();

            // Recent mutations
            $recentMutations = Mutasi::with(['penduduk' => fn($q) => $q->withTrashed()])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'counts' => [
                        'total_penduduk' => $basicStats->total_penduduk,
                        'total_kk' => $basicStats->total_kk,
                        'total_mutasi' => $totalMutasi,
                        'kk_bermasalah' => $kkBermasalahCount,
                        'surat_pending' => $suratPendingCount,
                    ],
                    'gender' => $genderStats,
                    'rt' => $rtStats,
                    'dusun' => $dusunStats,
                    'age_groups' => $ageGroups,
                    'religion' => $religionStats,
                    'mutations' => [
                        'summary' => Mutasi::select('jenis_mutasi', DB::raw('count(*) as total'))->groupBy('jenis_mutasi')->get(),
                        'recent' => $recentMutations
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('API Statistics Controller Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data statistik: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Refresh statistics cache (no longer needed).
     */
    public function refreshCache()
    {
        Gate::authorize('laporan_statistik');
        return response()->json(['success' => true, 'message' => 'Data statistik sudah real-time']);
    }

    /**
     * Get statistics data for charts (AJAX).
     */
    public function getChartData(Request $request)
    {
        Gate::authorize('laporan_statistik');
        $type = $request->get('type');

        switch ($type) {
            case 'gender':
                return Penduduk::whereNull('deleted_at')->select('jenis_kelamin', DB::raw('count(*) as total'))
                    ->groupBy('jenis_kelamin')->get();

            case 'rt':
                return DB::table('penduduks as p')
                    ->leftJoin('rts', 'p.rt_id', '=', 'rts.id')
                    ->select(DB::raw('COALESCE(rts.kode, "Tidak Diketahui") as label'), DB::raw('count(*) as total'))
                    ->whereNull('p.deleted_at')
                    ->groupBy('label')
                    ->orderBy('label')
                    ->get();

            case 'dusun':
                return DB::table('penduduks as p')
                    ->leftJoin('dusuns', 'p.dusun_id', '=', 'dusuns.id')
                    ->select(DB::raw('COALESCE(dusuns.nama, "Tidak Diketahui") as label'), DB::raw('count(*) as total'))
                    ->whereNull('p.deleted_at')
                    ->groupBy('label')
                    ->orderBy('label')
                    ->get();

            case 'age':
                return Penduduk::whereNull('deleted_at')->select(
                    DB::raw('CASE
                        WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 5 THEN "0-4"
                        WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 5 AND 9 THEN "5-9"
                        WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 10 AND 14 THEN "10-14"
                        WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 15 AND 19 THEN "15-19"
                        WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 65 THEN "65+"
                        ELSE "Dewasa/Lainnya"
                    END as label'),
                    DB::raw('count(*) as total')
                )
                ->groupBy('label')->get();

            default:
                return response()->json(['error' => 'Invalid chart type'], 400);
        }
    }
}
