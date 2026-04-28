<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Penduduk;
use App\Models\Mutasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class StatisticsController extends Controller
{
    /**
     * Display statistics dashboard.
     */
    public function index()
    {
        Gate::authorize('statistics.view');

        try {
            // Real-time statistics without cache
            $basicStats = DB::table('penduduks as p')
                ->select([
                    DB::raw('COUNT(*) as total_penduduk'),
                    DB::raw('COUNT(DISTINCT CASE WHEN p.nkk IS NOT NULL AND p.nkk != "" THEN p.nkk END) as total_kk'),
                    DB::raw('COUNT(CASE WHEN p.jenis_kelamin = "LAKI-LAKI" THEN 1 END) as laki_laki'),
                    DB::raw('COUNT(CASE WHEN p.jenis_kelamin = "PEREMPUAN" THEN 1 END) as perempuan'),
                ])
                ->whereNull('p.deleted_at')
                ->first();

            $totalMutasi = Mutasi::count();

            // Single query untuk semua statistik grup - hanya data aktif (tidak di-soft delete)
            $groupStats = DB::table('penduduks as p')
                ->leftJoin('rts as rt', 'p.rt_id', '=', 'rt.id')
                ->leftJoin('rws as rw', 'p.rw_id', '=', 'rw.id')
                ->leftJoin('dusuns as d', 'p.dusun_id', '=', 'd.id')
                ->select([
                    'p.jenis_kelamin',
                    'rt.kode as rt_kode',
                    'rw.kode as rw_kode',
                    'd.nama as dusun_nama',
                    'p.status_perkawinan',
                    'p.kedudukan_keluarga',
                    DB::raw('COUNT(*) as total')
                ])
                ->whereNull('p.deleted_at')
                ->groupBy('p.jenis_kelamin', 'rt.kode', 'rw.kode', 'd.nama', 'p.status_perkawinan', 'p.kedudukan_keluarga')
                ->get();

            // Process group statistics
            $genderStats = $groupStats->where('jenis_kelamin', '!=', null)
                ->groupBy(function($item) {
                    $val = strtoupper(trim($item->jenis_kelamin));
                    if (in_array($val, ['L', 'LAKI-LAKI', 'LAKI LAKI'])) return 'LAKI-LAKI';
                    if (in_array($val, ['P', 'PEREMPUAN'])) return 'PEREMPUAN';
                    return 'LAINNYA';
                })
                ->map(function($group) {
                    return $group->sum('total');
                });

            $rtStats = $groupStats->where('rt_kode', '!=', null)
                ->groupBy('rt_kode')
                ->map(function($group) {
                    return (object)['rt_label' => $group->first()->rt_kode, 'total' => $group->sum('total')];
                })
                ->sortBy('rt_label')
                ->values();

            $dusunStats = $groupStats->where('dusun_nama', '!=', null)
                ->groupBy('dusun_nama')
                ->map(function($group) {
                    return (object)['dusun_label' => $group->first()->dusun_nama, 'total' => $group->sum('total')];
                })
                ->sortBy('dusun_label')
                ->values();

            $maritalStats = $groupStats->where('status_perkawinan', '!=', null)
                ->groupBy('status_perkawinan')
                ->map(function($group) {
                    return (object)['status_perkawinan' => $group->first()->status_perkawinan, 'total' => $group->sum('total')];
                })
                ->sortByDesc('total')
                ->values();

            $familyPositionStats = $groupStats->where('kedudukan_keluarga', '!=', null)
                ->groupBy('kedudukan_keluarga')
                ->map(function($group) {
                    return (object)['kedudukan_keluarga' => $group->first()->kedudukan_keluarga, 'total' => $group->sum('total')];
                })
                ->sortByDesc('total')
                ->values();

            $stats = [
                'basic' => $basicStats,
                'total_mutasi' => $totalMutasi,
                'gender' => $genderStats,
                'rt' => $rtStats,
                'dusun' => $dusunStats,
                'marital' => $maritalStats,
                'family_position' => $familyPositionStats
            ];


            // Extract cached data
            $totalPenduduk = $stats['basic']->total_penduduk;
            $totalKK = $stats['basic']->total_kk;
            $totalMutasi = $stats['total_mutasi'];
            $genderStats = $stats['gender'];
            $rtStats = $stats['rt'];
            $dusunStats = $stats['dusun'];
            $maritalStats = $stats['marital'];
            $familyPositionStats = $stats['family_position'];

            // Age group statistics - hanya data aktif
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
                ->orderByRaw('CASE
                    WHEN age_group = "0-4" THEN 1
                    WHEN age_group = "5-9" THEN 2
                    WHEN age_group = "10-14" THEN 3
                    WHEN age_group = "15-19" THEN 4
                    WHEN age_group = "20-24" THEN 5
                    WHEN age_group = "25-29" THEN 6
                    WHEN age_group = "30-34" THEN 7
                    WHEN age_group = "35-39" THEN 8
                    WHEN age_group = "40-44" THEN 9
                    WHEN age_group = "45-49" THEN 10
                    WHEN age_group = "50-54" THEN 11
                    WHEN age_group = "55-59" THEN 12
                    WHEN age_group = "60-64" THEN 13
                    WHEN age_group = "65+" THEN 14
                    ELSE 15
                END')
                ->get();

            // Religion statistics - hanya data aktif
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

            // Education statistics - hanya data aktif
            $educationStats = DB::table('penduduks as p')
                ->leftJoin('mutasis as m', function($join) {
                    $join->on('m.penduduk_id', '=', 'p.id')
                         ->whereIn('m.jenis_mutasi', ['kematian', 'pindah_keluar']);
                })
                ->select('p.pendidikan', DB::raw('COUNT(CASE WHEN m.id IS NULL THEN 1 END) as total'))
                ->whereNull('m.id')
                ->groupBy('p.pendidikan')
                ->orderBy('total', 'desc')
                ->get();

            // Job statistics - hanya data aktif
            $jobStats = DB::table('penduduks as p')
                ->leftJoin('mutasis as m', function($join) {
                    $join->on('m.penduduk_id', '=', 'p.id')
                         ->whereIn('m.jenis_mutasi', ['kematian', 'pindah_keluar']);
                })
                ->select('p.pekerjaan', DB::raw('COUNT(CASE WHEN m.id IS NULL THEN 1 END) as total'))
                ->whereNull('m.id')
                ->groupBy('p.pekerjaan')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get();

            // RW statistics - hanya data aktif
            $rwStats = DB::table('penduduks as p')
                ->leftJoin('rws as rw', 'p.rw_id', '=', 'rw.id')
                ->leftJoin('mutasis as m', function($join) {
                    $join->on('m.penduduk_id', '=', 'p.id')
                         ->whereIn('m.jenis_mutasi', ['kematian', 'pindah_keluar']);
                })
                ->select('rw.kode as rw_kode', DB::raw('COUNT(CASE WHEN m.id IS NULL THEN 1 END) as total'))
                ->whereNull('m.id')
                ->groupBy('rw.kode')
                ->orderBy('rw.kode')
                ->get()
                ->map(function($item) {
                    return (object)['rw_label' => $item->rw_kode, 'total' => $item->total];
                });

            // Mutation statistics
            $mutationStats = Mutasi::select('jenis_mutasi', DB::raw('count(*) as total'))
                ->groupBy('jenis_mutasi')
                ->get();

            // Recent mutations
            $recentMutations = Mutasi::with(['penduduk' => function($query) {
                $query->withTrashed();
            }])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

            // Family size statistics
            $familySizeStats = Penduduk::select('nkk', DB::raw('count(*) as family_size'))
                ->groupBy('nkk')
                ->get()
                ->groupBy('family_size')
                ->map(function($group) {
                    return (object) [
                        'family_size' => $group->first()->family_size,
                        'total' => $group->count()
                    ];
                })
                ->sortBy('family_size')
                ->values();

            return view('statistics.index', compact(
                'totalPenduduk',
                'totalKK',
                'totalMutasi',
                'genderStats',
                'ageGroups',
                'religionStats',
                'educationStats',
                'jobStats',
                'rtStats',
                'rwStats',
                'dusunStats',
                'maritalStats',
                'familyPositionStats',
                'mutationStats',
                'recentMutations',
                'familySizeStats'
            ));

        } catch (\Exception $e) {
            Log::error('Statistics Controller Error: ' . $e->getMessage());

            // Return basic data if there's an error
            return view('statistics.simple', [
                'totalPenduduk' => 0,
                'totalKK' => 0,
                'totalMutasi' => 0,
                'genderStats' => collect(),
                'ageGroups' => collect(),
                'religionStats' => collect(),
                'educationStats' => collect(),
                'jobStats' => collect(),
                'rtStats' => collect(),
                'rwStats' => collect(),
                'mutationStats' => collect(),
                'recentMutations' => collect(),
                'familySizeStats' => collect(),
            ]);
        }
    }

    /**
     * Refresh statistics cache (no longer needed - data is real-time).
     */
    public function refreshCache()
    {
        Gate::authorize('statistics.view');

        return response()->json([
            'success' => true,
            'message' => 'Data statistik sudah real-time, tidak perlu refresh cache'
        ]);
    }

    /**
     * Get statistics data for charts (AJAX).
     */
    public function getChartData(Request $request)
    {
        Gate::authorize('statistics.view');

        $type = $request->get('type');

        switch ($type) {
            case 'gender':
                return Penduduk::whereDoesntHave('mutasis', function($query) {
                    $query->whereIn('jenis_mutasi', ['kematian', 'pindah_keluar']);
                })->select('jenis_kelamin', DB::raw('count(*) as total'))
                    ->groupBy('jenis_kelamin')
                    ->get();

            case 'age':
                return Penduduk::whereDoesntHave('mutasis', function($query) {
                    $query->whereIn('jenis_mutasi', ['kematian', 'pindah_keluar']);
                })->select(
                    DB::raw('CASE
                        WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 5 THEN "0-4"
                        WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 5 AND 9 THEN "5-9"
                        WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 10 AND 14 THEN "10-14"
                        WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 15 AND 19 THEN "15-19"
                        WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 20 AND 24 THEN "20-24"
                        WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 25 AND 29 THEN "25-29"
                        WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 30 AND 34 THEN "30-34"
                        WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 35 AND 39 THEN "35-39"
                        WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 40 AND 44 THEN "40-44"
                        WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 45 AND 49 THEN "45-49"
                        WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 50 AND 54 THEN "50-54"
                        WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 55 AND 59 THEN "55-59"
                        WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 60 AND 64 THEN "60-64"
                        WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 65 THEN "65+"
                        ELSE "Tidak Diketahui"
                    END as age_group'),
                    DB::raw('count(*) as total')
                )
                ->groupBy('age_group')
                ->orderByRaw('CASE
                    WHEN age_group = "0-4" THEN 1
                    WHEN age_group = "5-9" THEN 2
                    WHEN age_group = "10-14" THEN 3
                    WHEN age_group = "15-19" THEN 4
                    WHEN age_group = "20-24" THEN 5
                    WHEN age_group = "25-29" THEN 6
                    WHEN age_group = "30-34" THEN 7
                    WHEN age_group = "35-39" THEN 8
                    WHEN age_group = "40-44" THEN 9
                    WHEN age_group = "45-49" THEN 10
                    WHEN age_group = "50-54" THEN 11
                    WHEN age_group = "55-59" THEN 12
                    WHEN age_group = "60-64" THEN 13
                    WHEN age_group = "65+" THEN 14
                    ELSE 15
                END')
                ->get();

            case 'religion':
                return Penduduk::whereDoesntHave('mutasis', function($query) {
                    $query->whereIn('jenis_mutasi', ['kematian', 'pindah_keluar']);
                })->select('agama', DB::raw('count(*) as total'))
                    ->groupBy('agama')
                    ->orderBy('total', 'desc')
                    ->get();

            case 'rt':
                return DB::table('penduduks as p')
                    ->join('rts as rt', 'p.rt_id', '=', 'rt.id')
                    ->leftJoin('mutasis as m', function($join) {
                        $join->on('m.penduduk_id', '=', 'p.id')
                             ->whereIn('m.jenis_mutasi', ['kematian', 'pindah_keluar']);
                    })
                    ->select('rt.kode as rt_kode', DB::raw('COUNT(CASE WHEN m.id IS NULL THEN 1 END) as total'))
                    ->whereNull('m.id')
                    ->groupBy('rt.kode')
                    ->orderBy('rt.kode')
                    ->get()
                    ->map(function($item) {
                        return (object)['rt_label' => $item->rt_kode, 'total' => $item->total];
                    });


            default:
                return response()->json(['error' => 'Invalid chart type'], 400);
        }
    }
}
