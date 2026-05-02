<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;

use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use App\Models\Mutasi;
use App\Models\SuratType;
use App\Models\SuratPengajuan;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with cached statistics.
     */
    public function index()
    {
        // Cache dashboard stats for 5 minutes (300 seconds)
        $stats = Cache::remember('dashboard_stats', 300, function () {
            return [
                'total_penduduk' => Penduduk::whereNull('deleted_at')->count(),
                'laki_laki' => Penduduk::where('jenis_kelamin', 'LAKI-LAKI')->whereNull('deleted_at')->count(),
                'perempuan' => Penduduk::where('jenis_kelamin', 'PEREMPUAN')->whereNull('deleted_at')->count(),
                'total_kk' => \App\Models\KartuKeluarga::where('anggota_aktif', '>', 0)->count(),
            ];
        });

        // Cache mutation stats for 5 minutes
        $mutasiStats = Cache::remember('dashboard_mutasi_stats', 300, function () {
            return [
                'kelahiran' => Mutasi::where('jenis_mutasi', 'kelahiran')->count(),
                'kematian' => Mutasi::where('jenis_mutasi', 'kematian')->count(),
                'pindah_masuk' => Mutasi::where('jenis_mutasi', 'pindah_masuk')->count(),
                'pindah_keluar' => Mutasi::where('jenis_mutasi', 'pindah_keluar')->count(),
            ];
        });

        // Cache surat stats for 5 minutes
        $suratStats = Cache::remember('dashboard_surat_stats', 300, function () {
            return [
                'pending' => SuratPengajuan::where('status', 'pending')->count(),
                'diproses' => SuratPengajuan::where('status', 'diproses')->count(),
                'selesai' => SuratPengajuan::where('status', 'selesai')->count(),
                'ditolak' => SuratPengajuan::where('status', 'ditolak')->count(),
            ];
        });

        // Age Groups Stats
        $ageGroups = Cache::remember('dashboard_age_groups', 300, function() {
            $now = now();
            return [
                'balita' => Penduduk::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, ?) BETWEEN 0 AND 5', [$now])->count(),
                'anak' => Penduduk::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, ?) BETWEEN 6 AND 12', [$now])->count(),
                'remaja' => Penduduk::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, ?) BETWEEN 13 AND 17', [$now])->count(),
                'dewasa' => Penduduk::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, ?) BETWEEN 18 AND 59', [$now])->count(),
                'lansia' => Penduduk::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, ?) >= 60', [$now])->count(),
            ];
        });

        // Mutation Trends (Last 6 Months)
        $mutationTrends = Cache::remember('dashboard_mutation_trends', 300, function() {
            $months = collect();
            for ($i = 5; $i >= 0; $i--) {
                $months->push(now()->subMonths($i)->format('M'));
            }

            $data = [
                'labels' => $months->toArray(),
                'masuk' => [],
                'keluar' => [],
            ];

            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
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
        });

        // Recent activity (short cache - 1 minute)
        $recentMutasi = Cache::remember('dashboard_recent_mutasi', 60, function () {
            return Mutasi::with(['penduduk' => function($q) {
                $q->withTrashed();
            }])->latest()->take(5)->get();
        });

        return inertia('Tenant/Dashboard/Index', [
            'stats' => Inertia::defer(fn() => $stats),
            'mutasiStats' => Inertia::defer(fn() => $mutasiStats),
            'suratStats' => Inertia::defer(fn() => $suratStats),
            'recentMutasi' => Inertia::defer(fn() => $recentMutasi),
            'ageGroups' => Inertia::defer(fn() => $ageGroups),
            'mutationTrends' => Inertia::defer(fn() => $mutationTrends),
        ]);
    }

    /**
     * Refresh dashboard cache manually.
     */
    public function refresh()
    {
        Cache::forget('dashboard_stats');
        Cache::forget('dashboard_mutasi_stats');
        Cache::forget('dashboard_surat_stats');
        Cache::forget('dashboard_recent_mutasi');
        Cache::forget('dashboard_age_groups');
        Cache::forget('dashboard_mutation_trends');

        return redirect()->route('dashboard')->with('success', 'Data dashboard berhasil diperbarui!');
    }
}
