<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penduduk;
use App\Models\SuratPengajuan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Traits\ApiResponse;

class StatisticApiController extends Controller
{
    use ApiResponse;

    /**
     * Get statistics data
     */
    public function getStatistics()
    {
        // Redis cache untuk 2 menit
        return Cache::remember('api_statistics', 15, function () {
            $stats = DB::table('penduduks as p')
                ->leftJoin('kartu_keluargas as kk', 'p.kartu_keluarga_id', '=', 'kk.id')
                ->select([
                    DB::raw('COUNT(*) as total_penduduk'),
                    DB::raw('COUNT(DISTINCT p.kartu_keluarga_id) as total_kk'),
                    DB::raw('COUNT(DISTINCT kk.rt_id) as total_rt'),
                    DB::raw('COUNT(CASE WHEN p.jenis_kelamin IN ("L", "LAKI-LAKI") THEN 1 END) as laki_laki'),
                    DB::raw('COUNT(CASE WHEN p.jenis_kelamin IN ("P", "PEREMPUAN") THEN 1 END) as perempuan'),
                ])
                ->whereNull('p.deleted_at')
                ->first();

            $totalPenduduk = $stats->total_penduduk;
            $totalKK = $stats->total_kk;
            $totalRt = $stats->total_rt;
            $lakiLaki = $stats->laki_laki;
            $perempuan = $stats->perempuan;

            $totalMutasi = \App\Models\Mutasi::count();
            $totalBerita = \App\Models\Berita::published()->count();
            $totalPengajuan = SuratPengajuan::count();

            $pendidikan = DB::table('penduduks as p')
                ->leftJoin('mutasis as m', function($join) {
                    $join->on('m.penduduk_id', '=', 'p.id')
                         ->whereIn('m.jenis_mutasi', ['kematian', 'pindah_keluar']);
                })
                ->select('p.pendidikan', DB::raw('COUNT(*) as jumlah'))
                ->whereNull('m.id')
                ->whereNotNull('p.pendidikan')
                ->where('p.pendidikan', '!=', '')
                ->groupBy('p.pendidikan')
                ->orderBy('jumlah', 'desc')
                ->limit(5)
                ->pluck('jumlah', 'pendidikan')
                ->toArray();

            $pekerjaan = DB::table('penduduks as p')
                ->leftJoin('mutasis as m', function($join) {
                    $join->on('m.penduduk_id', '=', 'p.id')
                         ->whereIn('m.jenis_mutasi', ['kematian', 'pindah_keluar']);
                })
                ->select('p.pekerjaan', DB::raw('COUNT(*) as jumlah'))
                ->whereNull('m.id')
                ->whereNotNull('p.pekerjaan')
                ->where('p.pekerjaan', '!=', '')
                ->groupBy('p.pekerjaan')
                ->orderBy('jumlah', 'desc')
                ->limit(5)
                ->pluck('jumlah', 'pekerjaan')
                ->toArray();

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

            $usiaProduktif = 0;
            $usiaLansia = 0;

            foreach ($ageGroups as $group) {
                $ageRange = $group->age_group;
                $total = $group->total;

                if (in_array($ageRange, ['15-19', '20-24', '25-29', '30-34', '35-39', '40-44', '45-49', '50-54', '55-59', '60-64'])) {
                    $usiaProduktif += $total;
                } elseif ($ageRange === '65+') {
                    $usiaLansia += $total;
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total_penduduk' => $totalPenduduk,
                    'total_kk' => $totalKK,
                    'total_rt' => $totalRt,
                    'total_mutasi' => $totalMutasi,
                    'total_berita' => $totalBerita,
                    'total_pengajuan' => $totalPengajuan,
                    'laki_laki' => $lakiLaki,
                    'perempuan' => $perempuan,
                    'pendidikan' => $pendidikan,
                    'pekerjaan' => $pekerjaan,
                    'age_groups' => $ageGroups,
                    'usia_produktif' => $usiaProduktif,
                    'usia_lansia' => $usiaLansia,
                    'penduduk' => $totalPenduduk,
                    'kartu_keluarga' => $totalKK,
                    'mutasi' => $totalMutasi,
                    'berita' => $totalBerita,
                    'pengajuan_surat' => $totalPengajuan
                ]
            ])->withHeaders([
                'Cache-Control' => 'public, max-age=120',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        });
    }

    /**
     * Get penduduk statistics
     */
    public function getPendudukStats()
    {
        $stats = [
            'total' => Penduduk::whereNull('deleted_at')->count(),
            'laki_laki' => Penduduk::whereNull('deleted_at')->whereIn('jenis_kelamin', ['L', 'LAKI-LAKI'])->count(),
            'perempuan' => Penduduk::whereNull('deleted_at')->whereIn('jenis_kelamin', ['P', 'PEREMPUAN'])->count(),
            'by_age' => [
                'anak' => Penduduk::whereNull('deleted_at')->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 17')->count(),
                'remaja' => Penduduk::whereNull('deleted_at')->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 17 AND 30')->count(),
                'dewasa' => Penduduk::whereNull('deleted_at')->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 31 AND 50')->count(),
                'lansia' => Penduduk::whereNull('deleted_at')->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 51 AND 65')->count(),
                'manula' => Penduduk::whereNull('deleted_at')->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) > 65')->count(),
            ]
        ];

        return $this->successResponse($stats);
    }

    /**
     * Get KK statistics
     */
    public function getKKStats()
    {
        $stats = [
            'total' => DB::table('penduduks')->whereNull('deleted_at')->distinct('kartu_keluarga_id')->count(),
            'by_dusun' => DB::table('penduduks as p')
                ->join('kartu_keluargas as kk', 'p.kartu_keluarga_id', '=', 'kk.id')
                ->join('dusuns as d', 'kk.dusun_id', '=', 'd.id')
                ->selectRaw('d.nama as dusun, COUNT(DISTINCT p.kartu_keluarga_id) as total')
                ->whereNull('p.deleted_at')
                ->groupBy('d.nama')
                ->get()
                ->pluck('total', 'dusun')
        ];

        return $this->successResponse($stats);
    }

    /**
     * Get mutasi statistics
     */
    public function getMutasiStats()
    {
        $stats = [
            'total' => \App\Models\Mutasi::count(),
            'masuk' => \App\Models\Mutasi::where('jenis_mutasi', 'masuk')->count(),
            'keluar' => \App\Models\Mutasi::where('jenis_mutasi', 'keluar')->count(),
            'this_month' => \App\Models\Mutasi::whereMonth('created_at', now()->month)->count()
        ];

        return $this->successResponse($stats);
    }

    /**
     * Get public statistics for welcome page
     */
    public function getPublicStatistics()
    {
        return Cache::remember('api_public_statistics', 15, function () {
            try {
                $stats = [
                    'total_penduduk' => DB::table('penduduks')->whereNull('deleted_at')->count(),
                    'total_kk' => DB::table('penduduks')->whereNull('deleted_at')->distinct('kartu_keluarga_id')->count(),
                    'total_rt' => DB::table('kartu_keluargas')->distinct('rt_id')->count(),
                    'surat_selesai' => SuratPengajuan::whereIn('status', ['completed', 'selesai'])->count(),
                    'pengaduan_total' => \App\Models\Pengaduan::count(),
                    'pengaduan_selesai' => \App\Models\Pengaduan::where('status', 'selesai')->count(),
                ];

                return $this->successResponse($stats);
            } catch (\Exception $e) {
                return $this->errorResponse('Gagal mengambil statistik', $e->getMessage(), 500);
            }
        });
    }

    /**
     * Get public penduduk statistics for welcome page
     */
    public function getPublicPendudukStats()
    {
        return Cache::remember('api_public_penduduk_stats', 15, function () {
            try {
                $stats = [
                    'total' => Penduduk::whereNull('deleted_at')->count(),
                    'laki_laki' => Penduduk::whereNull('deleted_at')->whereIn('jenis_kelamin', ['L', 'LAKI-LAKI'])->count(),
                    'perempuan' => Penduduk::whereNull('deleted_at')->whereIn('jenis_kelamin', ['P', 'PEREMPUAN'])->count(),
                ];

                return response()->json([
                    'success' => true,
                    'data' => $stats
                ])->withHeaders([
                    'Cache-Control' => 'public, max-age=300',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
            } catch (\Exception $e) {
                return $this->errorResponse('Gagal mengambil statistik penduduk', null, 500);
            }
        });
    }
}
