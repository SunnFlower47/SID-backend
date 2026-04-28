<?php

namespace App\Http\Controllers\ApiAdminPanel;

use Illuminate\Http\Request;
use App\Models\Apbdes;
use App\Models\ProyekDesa;
use Illuminate\Support\Facades\DB;

class TransparansiDesaController extends Controller
{
        public function __construct()
    {
        $this->middleware(['auth', 'can:transparansi-desa.view']);
    }

    /**
     * Display the transparency dashboard
     */
    public function index()
    {
        $currentYear = date('Y');

        // APBDes Statistics
        $apbdesStats = [
            'total_anggaran' => Apbdes::disetujui()->tahun($currentYear)->sum('anggaran'),
            'total_realisasi' => Apbdes::disetujui()->tahun($currentYear)->sum('realisasi'),
            'total_pendapatan' => Apbdes::disetujui()->tahun($currentYear)->jenis('pendapatan')->sum('anggaran'),
            'total_belanja' => Apbdes::disetujui()->tahun($currentYear)->jenis('belanja')->sum('anggaran'),
            'total_pembiayaan' => Apbdes::disetujui()->tahun($currentYear)->jenis('pembiayaan')->sum('anggaran'),
        ];

        // Proyek Statistics
        $proyekStats = [
            'total_proyek' => ProyekDesa::count(),
            'proyek_aktif' => ProyekDesa::aktif()->count(),
            'proyek_selesai' => ProyekDesa::status('selesai')->count(),
            'total_anggaran_proyek' => ProyekDesa::sum('anggaran'),
            'total_realisasi_proyek' => ProyekDesa::sum('realisasi'),
        ];

        // Recent APBDes entries
        $recentApbdes = Apbdes::tahun($currentYear)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent Projects
        $recentProyek = ProyekDesa::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // APBDes by jenis for chart
        $apbdesByJenis = Apbdes::disetujui()
            ->tahun($currentYear)
            ->select('jenis', DB::raw('SUM(anggaran) as total_anggaran'), DB::raw('SUM(realisasi) as total_realisasi'))
            ->groupBy('jenis')
            ->get();

        // Projects by status
        $proyekByStatus = ProyekDesa::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        return view('transparansi-desa.index', compact(
            'apbdesStats',
            'proyekStats',
            'recentApbdes',
            'recentProyek',
            'apbdesByJenis',
            'proyekByStatus',
            'currentYear'
        ));
    }

    /**
     * Display APBDes data
     */
    public function apbdes(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $jenis = $request->get('jenis', '');

        $query = Apbdes::tahun($tahun);

        if ($jenis) {
            $query->jenis($jenis);
        }

        $apbdes = $query->orderBy('kode_rekening')->paginate(20);

        $stats = [
            'total_anggaran' => $query->sum('anggaran'),
            'total_realisasi' => $query->sum('realisasi'),
            'persentase_realisasi' => $query->sum('anggaran') > 0 ?
                round(($query->sum('realisasi') / $query->sum('anggaran')) * 100, 2) : 0,
        ];

        $tahunList = Apbdes::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        return view('transparansi-desa.apbdes', compact('apbdes', 'stats', 'tahunList', 'tahun', 'jenis'));
    }

    /**
     * Display Projects data
     */
    public function proyek(Request $request)
    {
        $status = $request->get('status', '');
        $jenis = $request->get('jenis', '');

        $query = ProyekDesa::query();

        if ($status) {
            $query->status($status);
        }

        if ($jenis) {
            $query->jenis($jenis);
        }

        $proyek = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total_proyek' => ProyekDesa::count(),
            'total_anggaran' => ProyekDesa::sum('anggaran'),
            'total_realisasi' => ProyekDesa::sum('realisasi'),
            'persentase_realisasi' => ProyekDesa::sum('anggaran') > 0 ?
                round((ProyekDesa::sum('realisasi') / ProyekDesa::sum('anggaran')) * 100, 2) : 0,
        ];

        return view('transparansi-desa.proyek', compact('proyek', 'stats', 'status', 'jenis'));
    }
}
