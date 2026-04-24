<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Helpers\DataSanitizer;
class PendudukApiController extends Controller
{
    /**
     * Get penduduk data with age filters
     */
    public function index(Request $request)
    {
        // Redis cache untuk 2 menit (penduduk data sering berubah)
        $cacheKey = 'api_penduduk_' . md5(serialize($request->all()));

        return Cache::remember($cacheKey, 120, function () use ($request) {
            try {
            $query = Penduduk::whereNull('deleted_at')
                ->orderBy('rt')
                ->orderBy('rw')
                ->orderBy('nkk')
                ->orderByRaw("CASE
                    WHEN kedudukan_keluarga = 'Kepala Keluarga' THEN 1
                    WHEN kedudukan_keluarga = 'Istri' THEN 2
                    WHEN kedudukan_keluarga = 'Anak' THEN 3
                    WHEN kedudukan_keluarga = 'Menantu' THEN 4
                    WHEN kedudukan_keluarga = 'Cucu' THEN 5
                    WHEN kedudukan_keluarga = 'Orang Tua' THEN 6
                    WHEN kedudukan_keluarga = 'Mertua' THEN 7
                    WHEN kedudukan_keluarga = 'Saudara' THEN 8
                    ELSE 9
                END")
                ->orderBy('tanggal_lahir', 'asc');

            // Search functionality
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nik', 'like', "%{$search}%")
                      ->orWhere('nkk', 'like', "%{$search}%")
                      ->orWhere('alamat', 'like', "%{$search}%");
                });
            }

            // Filter by gender
            if ($request->has('jenis_kelamin') && $request->jenis_kelamin) {
                $jenisKelamin = $request->jenis_kelamin;
                if (in_array($jenisKelamin, ['L', 'Laki-laki', 'LAKI-LAKI'])) {
                    $query->where('jenis_kelamin', 'LAKI-LAKI');
                } elseif (in_array($jenisKelamin, ['P', 'Perempuan', 'PEREMPUAN'])) {
                    $query->where('jenis_kelamin', 'PEREMPUAN');
                } else {
                    $query->where('jenis_kelamin', $jenisKelamin);
                }
            }

            // Filter by RT
            if ($request->has('rt') && $request->rt) {
                $query->where('rt', $request->rt);
            }

            // Filter by RW
            if ($request->has('rw') && $request->rw) {
                $query->where('rw', $request->rw);
            }

            // Filter by dusun
            if ($request->has('dusun') && $request->dusun) {
                $query->where('dusun', $request->dusun);
            }

            // Filter by age range
            if ($request->has('filter_umur') && $request->filter_umur) {
                $filterUmur = $request->filter_umur;
                $today = Carbon::now();

                switch ($filterUmur) {
                    case 'bayi':
                        // 0-2 tahun
                        $query->where('tanggal_lahir', '>=', $today->copy()->subYears(2));
                        break;
                    case 'balita':
                        // 2-5 tahun
                        $query->where('tanggal_lahir', '>=', $today->copy()->subYears(5))
                              ->where('tanggal_lahir', '<', $today->copy()->subYears(2));
                        break;
                    case 'anak':
                        // 5-12 tahun
                        $query->where('tanggal_lahir', '>=', $today->copy()->subYears(12))
                              ->where('tanggal_lahir', '<', $today->copy()->subYears(5));
                        break;
                    case 'remaja':
                        // 12-18 tahun
                        $query->where('tanggal_lahir', '>=', $today->copy()->subYears(18))
                              ->where('tanggal_lahir', '<', $today->copy()->subYears(12));
                        break;
                    case 'dewasa_muda':
                        // 18-30 tahun
                        $query->where('tanggal_lahir', '>=', $today->copy()->subYears(30))
                              ->where('tanggal_lahir', '<', $today->copy()->subYears(18));
                        break;
                    case 'dewasa':
                        // 30-60 tahun
                        $query->where('tanggal_lahir', '>=', $today->copy()->subYears(60))
                              ->where('tanggal_lahir', '<', $today->copy()->subYears(30));
                        break;
                    case 'lansia':
                        // >=60 tahun
                        $query->where('tanggal_lahir', '<=', $today->copy()->subYears(60));
                        break;
                case 'umur_20_keatas':
                        // >=20 tahun
                        $query->where('tanggal_lahir', '<=', $today->copy()->subYears(20));
                        break;
                    case 'umur_20_kebawah':
                        // <20 tahun
                        $query->where('tanggal_lahir', '>', $today->copy()->subYears(20));
                        break;
                case 'umur_40_keatas':
                        // >=40 tahun
                        $query->where('tanggal_lahir', '<=', $today->copy()->subYears(40));
                        break;
                    case 'umur_60_keatas':
                        // >=60 tahun
                        $query->where('tanggal_lahir', '<=', $today->copy()->subYears(60));
                        break;
                    case 'umur_60_kebawah':
                        // <60 tahun
                        $query->where('tanggal_lahir', '>', $today->copy()->subYears(60));
                        break;
                }
            }

            // Pagination
            $perPage = $request->get('per_page', 50);
            $penduduks = $query->paginate($perPage);

            // Add age calculation to each penduduk
            $penduduks->getCollection()->transform(function ($penduduk) {
                $penduduk->umur = Carbon::parse($penduduk->tanggal_lahir)->age;

                // Hash data sensitif untuk keamanan
                $penduduk->nik = DataSanitizer::hashSensitiveData($penduduk->nik);
                $penduduk->nkk = DataSanitizer::hashSensitiveData($penduduk->nkk);

                return $penduduk;
            });

                return response()->json([
                    'success' => true,
                    'data' => $penduduks,
                    'filters_applied' => [
                        'search' => $request->search,
                        'jenis_kelamin' => $request->jenis_kelamin,
                        'rt' => $request->rt,
                        'rw' => $request->rw,
                        'dusun' => $request->dusun,
                        'filter_umur' => $request->filter_umur
                    ]
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error retrieving penduduk data: ' . $e->getMessage()
                ], 500);
            }
        });
    }

    /**
     * Get age statistics
     */
    public function ageStatistics()
    {
        // Redis cache untuk 5 menit (statistics tidak terlalu sering berubah)
        return Cache::remember('api_penduduk_age_statistics', 300, function () {
            try {
            $today = Carbon::now();

            $stats = [
                'bayi' => Penduduk::whereNull('deleted_at')
                    ->where('tanggal_lahir', '>=', $today->copy()->subYears(2))
                    ->count(),
                'balita' => Penduduk::whereNull('deleted_at')
                    ->where('tanggal_lahir', '>=', $today->copy()->subYears(5))
                    ->where('tanggal_lahir', '<', $today->copy()->subYears(2))
                    ->count(),
                'anak' => Penduduk::whereNull('deleted_at')
                    ->where('tanggal_lahir', '>=', $today->copy()->subYears(12))
                    ->where('tanggal_lahir', '<', $today->copy()->subYears(5))
                    ->count(),
                'remaja' => Penduduk::whereNull('deleted_at')
                    ->where('tanggal_lahir', '>=', $today->copy()->subYears(18))
                    ->where('tanggal_lahir', '<', $today->copy()->subYears(12))
                    ->count(),
                'dewasa_muda' => Penduduk::whereNull('deleted_at')
                    ->where('tanggal_lahir', '>=', $today->copy()->subYears(30))
                    ->where('tanggal_lahir', '<', $today->copy()->subYears(18))
                    ->count(),
                'dewasa' => Penduduk::whereNull('deleted_at')
                    ->where('tanggal_lahir', '>=', $today->copy()->subYears(60))
                    ->where('tanggal_lahir', '<', $today->copy()->subYears(30))
                    ->count(),
                'lansia' => Penduduk::whereNull('deleted_at')
                    ->where('tanggal_lahir', '<=', $today->copy()->subYears(60))
                    ->count(),
                'umur_20_keatas' => Penduduk::whereNull('deleted_at')
                    ->where('tanggal_lahir', '<=', $today->copy()->subYears(20))
                    ->count(),
                'umur_20_kebawah' => Penduduk::whereNull('deleted_at')
                    ->where('tanggal_lahir', '>', $today->copy()->subYears(20))
                    ->count(),
                'umur_40_keatas' => Penduduk::whereNull('deleted_at')
                    ->where('tanggal_lahir', '<=', $today->copy()->subYears(40))
                    ->count(),
                'umur_40_kebawah' => Penduduk::whereNull('deleted_at')
                    ->where('tanggal_lahir', '>', $today->copy()->subYears(40))
                    ->count(),
                'umur_60_keatas' => Penduduk::whereNull('deleted_at')
                    ->where('tanggal_lahir', '<=', $today->copy()->subYears(60))
                    ->count(),
                'umur_60_kebawah' => Penduduk::whereNull('deleted_at')
                    ->where('tanggal_lahir', '>', $today->copy()->subYears(60))
                    ->count(),
            ];

                return response()->json([
                    'success' => true,
                    'data' => $stats,
                    'age_ranges' => [
                        'bayi' => '0-2 tahun',
                        'balita' => '2-5 tahun',
                        'anak' => '5-12 tahun',
                        'remaja' => '12-18 tahun',
                        'dewasa_muda' => '18-30 tahun',
                        'dewasa' => '30-60 tahun',
                        'lansia' => '≥60 tahun',
                        'umur_20_keatas' => '≥20 tahun',
                        'umur_20_kebawah' => '<20 tahun',
                        'umur_40_keatas' => '≥40 tahun',
                        'umur_40_kebawah' => '<40 tahun',
                        'umur_60_keatas' => '≥60 tahun',
                        'umur_60_kebawah' => '<60 tahun'
                    ]
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error retrieving age statistics: ' . $e->getMessage()
                ], 500);
            }
        });
    }

    /**
     * Get available filter options
     */
    public function filterOptions()
    {
        // Redis cache untuk 1 jam (filter options jarang berubah)
        return Cache::remember('api_penduduk_filter_options', 3600, function () {
            try {
            $options = [
                'rt_list' => Penduduk::select('rt')
                    ->distinct()
                    ->whereNotNull('rt')
                    ->orderBy('rt')
                    ->pluck('rt'),
                'rw_list' => Penduduk::select('rw')
                    ->distinct()
                    ->whereNotNull('rw')
                    ->orderBy('rw')
                    ->pluck('rw'),
                'dusun_list' => Penduduk::select('dusun')
                    ->distinct()
                    ->whereNotNull('dusun')
                    ->orderBy('dusun')
                    ->pluck('dusun'),
                'jenis_kelamin_list' => ['LAKI-LAKI', 'PEREMPUAN'],
                'age_filters' => [
                    'bayi' => 'Bayi (0-2 tahun)',
                    'balita' => 'Balita (2-5 tahun)',
                    'anak' => 'Anak (5-12 tahun)',
                    'remaja' => 'Remaja (12-18 tahun)',
                    'dewasa_muda' => 'Dewasa Muda (18-30 tahun)',
                    'dewasa' => 'Dewasa (30-60 tahun)',
                    'lansia' => 'Lansia (≥60 tahun)',
                    'umur_20_keatas' => 'Umur ≥20 tahun',
                    'umur_20_kebawah' => 'Umur <20 tahun',
                    'umur_40_keatas' => 'Umur ≥40 tahun',
                    'umur_40_kebawah' => 'Umur <40 tahun',
                    'umur_60_keatas' => 'Umur ≥60 tahun',
                    'umur_60_kebawah' => 'Umur <60 tahun'
                ]
            ];

                return response()->json([
                    'success' => true,
                    'data' => $options
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error retrieving filter options: ' . $e->getMessage()
                ], 500);
            }
        });
    }
}
