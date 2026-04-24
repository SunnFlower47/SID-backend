<?php

namespace App\Http\Controllers;

use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use App\Models\Mutasi;
use App\Models\SuratPengajuan;
use Illuminate\Support\Facades\Cache;

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
                // Count unique NKK from Penduduk table (more accurate than kartu_keluargas table)
                'total_kk' => Penduduk::whereNull('deleted_at')->whereNotNull('nkk')->distinct('nkk')->count('nkk'),
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
                'approved' => SuratPengajuan::where('status', 'approved')->count(),
                'rejected' => SuratPengajuan::where('status', 'rejected')->count(),
            ];
        });

        // Recent activity (short cache - 1 minute)
        $recentMutasi = Cache::remember('dashboard_recent_mutasi', 60, function () {
            return Mutasi::with(['penduduk' => function($q) {
                $q->withTrashed();
            }])->latest()->take(5)->get();
        });

        return view('dashboard', compact('stats', 'mutasiStats', 'suratStats', 'recentMutasi'));
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

        return redirect()->route('dashboard')->with('success', 'Data dashboard berhasil diperbarui!');
    }
}
