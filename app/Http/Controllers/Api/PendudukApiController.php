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
                $query = Penduduk::withWilayah()
                    ->filter($request->all())
                    ->orderByFamilyRole();

                // Pagination
                $perPage = $request->get('per_page', 50);
                $penduduks = $query->paginate($perPage);

                // Add age calculation to each penduduk
                $penduduks->getCollection()->transform(function ($penduduk) {
                    $penduduk->umur = $penduduk->usia;
                    $penduduk->rt_label = optional($penduduk->rtMaster)->kode;
                    $penduduk->rw_label = optional($penduduk->rwMaster)->kode;
                    $penduduk->dusun_label = optional($penduduk->dusunMaster)->nama;

                    // Hash data sensitif untuk keamanan
                    $penduduk->nik_masked = DataSanitizer::hashSensitiveData($penduduk->nik);
                    $penduduk->nkk_masked = DataSanitizer::hashSensitiveData($penduduk->nkk);

                    return $penduduk;
                });

                return response()->json([
                    'success' => true,
                    'data' => $penduduks,
                    'filters_applied' => [
                        'search' => $request->search,
                        'jenis_kelamin' => $request->jenis_kelamin,
                        'rt_id' => $request->rt_id,
                        'rw_id' => $request->rw_id,
                        'dusun_id' => $request->dusun_id,
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
        return Cache::remember('api_penduduk_age_statistics', 300, function () {
            try {
                $today = Carbon::now();
                $stats = [
                    'bayi' => Penduduk::whereNull('deleted_at')->where('tanggal_lahir', '>=', $today->copy()->subYears(2))->count(),
                    'balita' => Penduduk::whereNull('deleted_at')->whereBetween('tanggal_lahir', [$today->copy()->subYears(5), $today->copy()->subYears(2)])->count(),
                    'anak' => Penduduk::whereNull('deleted_at')->whereBetween('tanggal_lahir', [$today->copy()->subYears(12), $today->copy()->subYears(5)])->count(),
                    'remaja' => Penduduk::whereNull('deleted_at')->whereBetween('tanggal_lahir', [$today->copy()->subYears(18), $today->copy()->subYears(12)])->count(),
                    'dewasa_muda' => Penduduk::whereNull('deleted_at')->whereBetween('tanggal_lahir', [$today->copy()->subYears(30), $today->copy()->subYears(18)])->count(),
                    'dewasa' => Penduduk::whereNull('deleted_at')->whereBetween('tanggal_lahir', [$today->copy()->subYears(60), $today->copy()->subYears(30)])->count(),
                    'lansia' => Penduduk::whereNull('deleted_at')->where('tanggal_lahir', '<=', $today->copy()->subYears(60))->count(),
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
                    ]
                ]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        });
    }

    /**
     * Get available filter options
     */
    public function filterOptions()
    {
        return Cache::remember('api_penduduk_filter_options_v2', 3600, function () {
            try {
                $options = [
                    'rt_list' => \App\Models\Rt::orderBy('kode')->get(['id', 'kode']),
                    'rw_list' => \App\Models\Rw::orderBy('kode')->get(['id', 'kode']),
                    'dusun_list' => \App\Models\Dusun::orderBy('nama')->get(['id', 'nama']),
                    'jenis_kelamin_list' => [
                        ['id' => 'L', 'label' => 'Laki-laki'],
                        ['id' => 'P', 'label' => 'Perempuan']
                    ],
                    'age_filters' => [
                        'bayi' => 'Bayi (0-2 tahun)',
                        'balita' => 'Balita (2-5 tahun)',
                        'anak' => 'Anak (5-12 tahun)',
                        'remaja' => 'Remaja (12-18 tahun)',
                        'dewasa_muda' => 'Dewasa Muda (18-30 tahun)',
                        'dewasa' => 'Dewasa (30-60 tahun)',
                        'lansia' => 'Lansia (≥60 tahun)',
                    ]
                ];

                return response()->json([
                    'success' => true,
                    'data' => $options
                ]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        });
    }
}
