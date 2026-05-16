<?php

namespace App\Services;

use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use App\Models\Mutasi;
use App\Models\SuratPengajuan;
use App\Models\PendudukDomisili;
use App\Models\Berita;
use App\Models\Dusun;
use App\Models\Rw;
use App\Models\Rt;
use App\Models\WilayahChangeLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class VillageStatisticsService
{
    /**
     * Cache Key Prefixes
     */
    const KEY_VERSION = 'village_stats_version';
    const KEY_WILAYAH_VERSION = 'village_wilayah_version';

    /**
     * Get a versioned cache key
     */
    protected function getVersionedKey($key, $type = 'stats')
    {
        $versionKey = $type === 'wilayah' ? self::KEY_WILAYAH_VERSION : self::KEY_VERSION;
        $version = Cache::get($versionKey, 1);
        return "v{$version}_{$key}";
    }

    /**
     * Increment version to invalidate cache (Works on all drivers)
     */
    public function clearStats()
    {
        Cache::increment(self::KEY_VERSION);
    }

    public function clearWilayah()
    {
        Cache::increment(self::KEY_WILAYAH_VERSION);
    }

    /**
     * Get Main Dashboard Statistics
     */
    public function getDashboardStats()
    {
        $cacheKey = $this->getVersionedKey('dashboard_main_stats');
        
        return Cache::remember($cacheKey, 3600, function () {
            $today = today();
            
            return [
                'basic' => [
                    'total' => Penduduk::whereNull('deleted_at')->count(),
                    'total_penduduk' => Penduduk::whereNull('deleted_at')->count(),
                    'laki_laki' => Penduduk::where('jenis_kelamin', 'LAKI-LAKI')->whereNull('deleted_at')->count(),
                    'perempuan' => Penduduk::where('jenis_kelamin', 'PEREMPUAN')->whereNull('deleted_at')->count(),
                    'total_kk' => KartuKeluarga::where('anggota_aktif', '>', 0)->count(),
                ],
                'mutasi' => [
                    'kelahiran' => Mutasi::where('jenis_mutasi', 'kelahiran')->count(),
                    'kematian' => Mutasi::where('jenis_mutasi', 'kematian')->count(),
                    'pindahan' => Mutasi::whereIn('jenis_mutasi', ['pindah_masuk', 'pindah_keluar', 'pindah_rt_rw'])->count(),
                    'pisah_kk' => Mutasi::where('jenis_mutasi', 'pisah_kk')->count(),
                ],
                'surat' => [
                    'pending' => SuratPengajuan::where('status', 'pending')->count(),
                    'diproses' => SuratPengajuan::where('status', 'diproses')->count(),
                    'selesai' => SuratPengajuan::where('status', 'selesai')->count(),
                    'ditolak' => SuratPengajuan::where('status', 'ditolak')->count(),
                ],
                'age_groups' => $this->calculateAgeGroups(),
                'mutation_trends' => $this->calculateMutationTrends(),
            ];
        });
    }

    /**
     * Get Detailed Statistics for Reports
     */
    public function getDetailedStats()
    {
        $cacheKey = $this->getVersionedKey('detailed_village_stats');

        return Cache::remember($cacheKey, 3600, function () {
            $groupStats = DB::table('penduduks as p')
                ->join('kartu_keluargas as kk', 'p.kartu_keluarga_id', '=', 'kk.id')
                ->leftJoin('rts as rt', 'kk.rt_id', '=', 'rt.id')
                ->leftJoin('rws as rw', 'kk.rw_id', '=', 'rw.id')
                ->leftJoin('dusuns as d', 'kk.dusun_id', '=', 'd.id')
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

            return [
                'gender' => $this->processGroupedStats($groupStats, 'jenis_kelamin'),
                'marital' => $this->processGroupedStats($groupStats, 'status_perkawinan'),
                'family_position' => $this->processGroupedStats($groupStats, 'kedudukan_keluarga'),
                'rt_distribution' => $this->processGroupedStats($groupStats, 'rt_kode'),
                'rw_distribution' => $this->processGroupedStats($groupStats, 'rw_kode'),
                'dusun_distribution' => $this->processGroupedStats($groupStats, 'dusun_nama'),
            ];
        });
    }

    /**
     * Get Wilayah Master Data and Summary
     */
    public function getWilayahData()
    {
        $cacheKey = $this->getVersionedKey('master_wilayah_data_v2', 'wilayah');
        
        return Cache::remember($cacheKey, 86400, function () {
            $dusuns = Dusun::orderBy('nama')->get();
            $rws = Rw::orderBy('kode')->get();
            $rts = Rt::with(['rw', 'dusun'])->orderBy('kode')->get();

            // Simpan sebagai array murni biar enteng di Redis & PHP
            return [
                'dusuns' => $dusuns->toArray(),
                'rws' => $rws->toArray(),
                'rts' => $rts->toArray(),
                'summary' => $this->calculateWilayahSummary($dusuns, $rws, $rts),
            ];
        });
    }

    /**
     * Get Recent Wilayah Logs
     */
    public function getRecentWilayahLogs()
    {
        $cacheKey = $this->getVersionedKey('wilayah_recent_logs_list_v2', 'wilayah');
        
        return Cache::remember($cacheKey, 3600, function () {
            return WilayahChangeLog::query()
                ->select(['id', 'entity_type', 'entity_id', 'action', 'user_id', 'before_payload', 'after_payload', 'affected_count', 'status', 'applied_at', 'created_at'])
                ->where('entity_type', 'rt')
                ->with('user:id,name')
                ->latest('id')
                ->limit(15)
                ->get();
        });
    }

    /**
     * Internal: Calculate age groups
     */
    protected function calculateAgeGroups()
    {
        $now = now();
        return [
            'balita' => Penduduk::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, ?) BETWEEN 0 AND 5', [$now])->whereNull('deleted_at')->count(),
            'anak' => Penduduk::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, ?) BETWEEN 6 AND 12', [$now])->whereNull('deleted_at')->count(),
            'remaja' => Penduduk::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, ?) BETWEEN 13 AND 17', [$now])->whereNull('deleted_at')->count(),
            'dewasa' => Penduduk::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, ?) BETWEEN 18 AND 59', [$now])->whereNull('deleted_at')->count(),
            'lansia' => Penduduk::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, ?) >= 60', [$now])->whereNull('deleted_at')->count(),
        ];
    }

    /**
     * Internal: Calculate mutation trends
     */
    protected function calculateMutationTrends()
    {
        $months = collect();
        $data = ['labels' => [], 'masuk' => [], 'keluar' => []];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $data['labels'][] = $date->format('M');
            
            $data['masuk'][] = Mutasi::whereIn('jenis_mutasi', ['kelahiran', 'pindah_masuk'])
                ->whereYear('tanggal_mutasi', $date->year)
                ->whereMonth('tanggal_mutasi', $date->month)
                ->count();
                
            $data['keluar'][] = Mutasi::whereIn('jenis_mutasi', ['kematian', 'pindah_keluar'])
                ->whereYear('tanggal_mutasi', $date->year)
                ->whereMonth('tanggal_mutasi', $date->month)
                ->count();
        }

        return $data;
    }

    /**
     * Internal: Calculate summary for Master Wilayah
     */
    protected function calculateWilayahSummary($dusuns, $rws, $rts)
    {
        $pendudukCounts = Penduduk::query()
            ->join('kartu_keluargas', 'penduduks.kartu_keluarga_id', '=', 'kartu_keluargas.id')
            ->selectRaw('kartu_keluargas.rt_id, COUNT(*) as total')
            ->whereNull('penduduks.deleted_at')
            ->groupBy('kartu_keluargas.rt_id')
            ->pluck('total', 'rt_id');

        $domisiliCounts = PendudukDomisili::query()
            ->selectRaw('rt_id, COUNT(*) as total')
            ->where('status', 'aktif')
            ->groupBy('rt_id')
            ->pluck('total', 'rt_id');

        $penduduk_terpetakan = 0;
        $domisili_terpetakan = 0;

        foreach ($rts as $rt) {
            $penduduk_terpetakan += (int) ($pendudukCounts[$rt->id] ?? 0);
            $domisili_terpetakan += (int) ($domisiliCounts[$rt->id] ?? 0);
        }

        return [
            'dusun' => $dusuns->count(),
            'rw' => $rws->count(),
            'rt' => $rts->count(),
            'penduduk_terpetakan' => $penduduk_terpetakan,
            'domisili_terpetakan' => $domisili_terpetakan,
        ];
    }

    /**
     * Internal: Process grouped DB results
     */
    protected function processGroupedStats($stats, $key)
    {
        return $stats->where($key, '!=', null)
            ->groupBy($key)
            ->map(function($group) use ($key) {
                return (object)[
                    $key => $group->first()->$key,
                    'label' => $group->first()->$key,
                    'total' => $group->sum('total')
                ];
            })
            ->values();
    }
}
