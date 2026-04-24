<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penduduk;
use App\Models\SuratPengajuan;
use App\Models\DesaSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class OptimizedWebDesaController extends Controller
{
    /**
     * Get optimized statistics with caching
     */
    public function getStatistics()
    {
        // Real-time data without cache
        $stats = DB::table('penduduks as p')
                ->select([
                    // Total penduduk aktif (tidak di-soft delete)
                    DB::raw('COUNT(*) as total_penduduk'),

                    // Total KK
                    DB::raw('COUNT(DISTINCT CASE WHEN p.nkk IS NOT NULL AND p.nkk != "" THEN p.nkk END) as total_kk'),

                    // Total RT
                    DB::raw('COUNT(DISTINCT p.rt) as total_rt'),

                    // Jenis kelamin
                    DB::raw('COUNT(CASE WHEN p.jenis_kelamin = "LAKI-LAKI" THEN 1 END) as laki_laki'),
                    DB::raw('COUNT(CASE WHEN p.jenis_kelamin = "PEREMPUAN" THEN 1 END) as perempuan'),
                ])
                ->whereNull('p.deleted_at')
                ->first();

            // Simple counts untuk data lain
            $totalMutasi = \App\Models\Mutasi::count();
            $totalBerita = \App\Models\Berita::published()->count();
            $totalPengajuan = SuratPengajuan::count();

            // OPTIMASI: Query terpisah untuk pendidikan dan pekerjaan dengan LEFT JOIN
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

            return [
                'total_penduduk' => $stats->total_penduduk,
                'total_kk' => $stats->total_kk,
                'total_rt' => $stats->total_rt,
                'total_mutasi' => $totalMutasi,
                'total_berita' => $totalBerita,
                'total_pengajuan' => $totalPengajuan,
                'laki_laki' => $stats->laki_laki,
                'perempuan' => $stats->perempuan,
                'pendidikan' => $pendidikan,
                'pekerjaan' => $pekerjaan,
            ];
    }

    /**
     * Get desa info with caching
     */
    public function getDesaInfo()
    {
        // Real-time data without cache
        $desaInfo = DesaSetting::getDesaInfo();
        $kepalaDesa = DesaSetting::getKepalaDesaInfo();
        $sekretaris = DesaSetting::getSekretarisInfo();
        $logos = DesaSetting::getLogos();

        return [
            'desa' => $desaInfo,
            'kepala_desa' => $kepalaDesa,
            'sekretaris' => $sekretaris,
            'logos' => $logos,
        ];
    }

    /**
     * Clear cache for statistics
     */
    public function clearStatisticsCache()
    {
                return response()->json(['success' => true, 'message' => 'Statistics cache cleared']);
    }

    /**
     * Clear cache for desa info
     */
    public function clearDesaInfoCache()
    {
                return response()->json(['success' => true, 'message' => 'Desa info cache cleared']);
    }

    /**
     * Clear all cache
     */
    public function clearAllCache()
    {
        return response()->json(['success' => true, 'message' => 'All cache cleared']);
    }
}
