<?php

namespace App\Http\Controllers\Tenant\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Apbdes;
use App\Models\ProyekDesa;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TransparansiDesaController extends Controller
{
        public function __construct()
    {
        $this->middleware(['auth', 'can:keuangan']);
    }

    /**
     * Display the transparency dashboard
     */
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $tahunList = Apbdes::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        return Inertia::render('Tenant/Keuangan/Dashboard', [
            'tahun'     => $tahun,
            'tahunList' => $tahunList,

            'stats' => Inertia::defer(fn () => [
                'total_anggaran'       => (float) Apbdes::disetujui()->tahun($tahun)->sum('anggaran'),
                'total_realisasi'      => (float) Apbdes::disetujui()->tahun($tahun)->sum('realisasi'),
                'total_pendapatan'     => (float) Apbdes::disetujui()->tahun($tahun)->jenis('pendapatan')->sum('anggaran'),
                'total_belanja'        => (float) Apbdes::disetujui()->tahun($tahun)->jenis('belanja')->sum('anggaran'),
                'total_pembiayaan'     => (float) Apbdes::disetujui()->tahun($tahun)->jenis('pembiayaan')->sum('anggaran'),
                'total_proyek'         => ProyekDesa::count(),
                'proyek_aktif'         => ProyekDesa::aktif()->count(),
                'proyek_selesai'       => ProyekDesa::status('selesai')->count(),
                'total_anggaran_proyek'=> (float) ProyekDesa::sum('anggaran'),
                'total_realisasi_proyek'=> (float) ProyekDesa::sum('realisasi'),
            ]),

            'apbdesByJenis' => Inertia::defer(fn () =>
                Apbdes::disetujui()
                    ->tahun($tahun)
                    ->select('jenis', DB::raw('SUM(anggaran) as total_anggaran'), DB::raw('SUM(realisasi) as total_realisasi'))
                    ->groupBy('jenis')
                    ->get()
                    ->map(fn ($item) => [
                        'jenis'           => $item->jenis,
                        'label'           => ucfirst($item->jenis),
                        'total_anggaran'  => (float) $item->total_anggaran,
                        'total_realisasi' => (float) $item->total_realisasi,
                    ])
            ),

            'proyekByStatus' => Inertia::defer(fn () =>
                ProyekDesa::select('status', DB::raw('COUNT(*) as total'))
                    ->groupBy('status')
                    ->get()
                    ->map(fn ($item) => [
                        'status' => $item->status,
                        'label'  => ucfirst($item->status),
                        'total'  => (int) $item->total,
                    ])
            ),

            'recentApbdes' => Inertia::defer(fn () =>
                Apbdes::tahun($tahun)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
            ),

            'recentProyek' => Inertia::defer(fn () =>
                ProyekDesa::orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
            ),
        ]);
    }

    /**
     * Display APBDes data
     */
    public function apbdes(Request $request)
    {
        $tahun   = $request->get('tahun', date('Y'));
        $jenis   = $request->get('jenis', '');
        $search  = $request->get('search', '');
        $sumber  = $request->get('sumber_dana', '');

        $tahunList = Apbdes::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        return Inertia::render('Tenant/Keuangan/APBDes/Index', [
            'filters'   => $request->only(['tahun', 'jenis', 'search', 'sumber_dana']),
            'tahunList' => $tahunList,

            'apbdes' => Inertia::defer(fn () =>
                Apbdes::query()
                    ->tahun($tahun)
                    ->when($jenis,  fn ($q) => $q->jenis($jenis))
                    ->when($sumber, fn ($q) => $q->where('sumber_dana', $sumber))
                    ->when($search, fn ($q) => $q->where(function ($q2) use ($search) {
                        $q2->where('nama_rekening', 'like', "%{$search}%")
                           ->orWhere('kode_rekening', 'like', "%{$search}%");
                    }))
                    ->orderBy('kode_rekening')
                    ->paginate(15)
                    ->withQueryString()
            ),

            'stats' => Inertia::defer(fn () => [
                'total_anggaran'   => (float) Apbdes::tahun($tahun)->sum('anggaran'),
                'total_realisasi'  => (float) Apbdes::tahun($tahun)->sum('realisasi'),
                'count_pendapatan' => Apbdes::tahun($tahun)->jenis('pendapatan')->count(),
                'count_belanja'    => Apbdes::tahun($tahun)->jenis('belanja')->count(),
                'count_pembiayaan' => Apbdes::tahun($tahun)->jenis('pembiayaan')->count(),
                'count_total'      => Apbdes::tahun($tahun)->count(),
            ]),
        ]);
    }

    /**
     * Display Projects data
     */
    public function proyek(Request $request)
    {
        $status = $request->get('status', '');
        $jenis  = $request->get('jenis', '');
        $search = $request->get('search', '');

        return Inertia::render('Tenant/Keuangan/Proyek/Index', [
            'filters' => $request->only(['status', 'jenis', 'search']),

            'proyek' => Inertia::defer(fn () =>
                ProyekDesa::query()
                    ->when($status, fn ($q) => $q->status($status))
                    ->when($jenis,  fn ($q) => $q->jenis($jenis))
                    ->when($search, fn ($q) => $q->where('nama_proyek', 'like', "%{$search}%")
                                                  ->orWhere('lokasi', 'like', "%{$search}%"))
                    ->with('apbdes:id,kode_rekening,nama_rekening')
                    ->orderBy('created_at', 'desc')
                    ->paginate(12)
                    ->withQueryString()
            ),

            'stats' => Inertia::defer(fn () => [
                'total_proyek'          => ProyekDesa::count(),
                'proyek_aktif'          => ProyekDesa::aktif()->count(),
                'proyek_selesai'        => ProyekDesa::status('selesai')->count(),
                'total_anggaran_proyek' => (float) ProyekDesa::sum('anggaran'),
                'total_realisasi_proyek'=> (float) ProyekDesa::sum('realisasi'),
            ]),
        ]);
    }
}
