<?php

namespace App\Http\Controllers\Tenant\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Apbdes;
use App\Models\DesaSetting;
use App\Models\HistoriPengeluaran;
use App\Models\ProyekDesa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LaporanKeuanganController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:keuangan']);
    }

    /**
     * Halaman pilih laporan keuangan
     */
    public function index(Request $request)
    {
        $tahun     = $request->get('tahun', date('Y'));
        $tahunList = Apbdes::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        // Summary per bidang untuk preview
        $summary = Apbdes::query()
            ->where('tahun', $tahun)
            ->select('bidang', 'jenis',
                DB::raw('SUM(anggaran) as total_anggaran'),
                DB::raw('SUM(realisasi) as total_realisasi'),
                DB::raw('SUM(sisa_anggaran) as total_sisa')
            )
            ->groupBy('bidang', 'jenis')
            ->orderBy('bidang')
            ->orderBy('jenis')
            ->get();

        $totalAnggaran  = Apbdes::where('tahun', $tahun)->sum('anggaran');
        $totalRealisasi = Apbdes::where('tahun', $tahun)->sum('realisasi');

        return Inertia::render('Tenant/Keuangan/Laporan/Index', [
            'tahunList'      => $tahunList,
            'tahun'          => (int) $tahun,
            'summary'        => $summary,
            'totalAnggaran'  => $totalAnggaran,
            'totalRealisasi' => $totalRealisasi,
            'persen'         => $totalAnggaran > 0 ? round(($totalRealisasi / $totalAnggaran) * 100, 1) : 0,
        ]);
    }

    /**
     * Generate PDF: Laporan Realisasi APBDes
     * Format: Permendagri No. 20 Tahun 2018 — Lampiran VII
     */
    public function pdfRealisasi(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $jenis = $request->get('jenis', ''); // '' = semua

        $apbdes = Apbdes::query()
            ->where('tahun', $tahun)
            ->when($jenis, fn ($q) => $q->where('jenis', $jenis))
            ->orderBy('bidang')
            ->orderBy('kode_rekening')
            ->get();

        // Group by Bidang → Jenis → records
        $grouped = $apbdes->groupBy('bidang');

        $desaInfo   = DesaSetting::getDesaInfo();
        $kepalaInfo = DesaSetting::getKepalaDesaInfo();
        $logos      = DesaSetting::getLogos();

        $totalAnggaran  = $apbdes->sum('anggaran');
        $totalRealisasi = $apbdes->sum('realisasi');
        $totalSisa      = $apbdes->sum('sisa_anggaran');

        $data = compact(
            'apbdes', 'grouped', 'tahun', 'jenis',
            'desaInfo', 'kepalaInfo', 'logos',
            'totalAnggaran', 'totalRealisasi', 'totalSisa'
        );

        $pdf = Pdf::loadView('pdf.keuangan.laporan-realisasi', $data)
            ->setPaper('a4', 'landscape')
            ->setOption('defaultFont', 'sans-serif')
            ->setOption('isRemoteEnabled', true)
            ->setOption('dpi', 150);

        $filename = "Laporan-Realisasi-APBDes-{$tahun}" . ($jenis ? "-{$jenis}" : '') . ".pdf";
        return $pdf->download($filename);
    }

    /**
     * Generate PDF: Laporan Rincian per Rekening (Buku Kas Umum)
     */
    public function pdfBukuKas(Request $request)
    {
        $tahun    = $request->get('tahun', date('Y'));
        $apbdesId = $request->get('apbdes_id');

        $query = HistoriPengeluaran::with('apbdes')
            ->whereHas('apbdes', fn ($q) => $q->where('tahun', $tahun))
            ->orderBy('tanggal_pengeluaran');

        if ($apbdesId) {
            $query->where('apbdes_id', $apbdesId);
        }

        $histori    = $query->get();
        $desaInfo   = DesaSetting::getDesaInfo();
        $kepalaInfo = DesaSetting::getKepalaDesaInfo();

        $apbdes = $apbdesId ? Apbdes::find($apbdesId) : null;

        $pdf = Pdf::loadView('pdf.keuangan.buku-kas', compact('histori', 'desaInfo', 'kepalaInfo', 'tahun', 'apbdes'))
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'sans-serif')
            ->setOption('dpi', 150);

        $filename = "Buku-Kas-APBDes-{$tahun}" . ($apbdesId ? "-rek{$apbdesId}" : '') . ".pdf";
        return $pdf->download($filename);
    }

    /**
     * Generate PDF: Laporan Proyek Desa
     */
    public function pdfProyek(Request $request)
    {
        $tahun  = $request->get('tahun', date('Y'));
        $status = $request->get('status', '');

        $proyek = ProyekDesa::with('apbdes')
            ->where('tahun_anggaran', $tahun)
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy('status')
            ->orderBy('nama_proyek')
            ->get();

        $desaInfo   = DesaSetting::getDesaInfo();
        $kepalaInfo = DesaSetting::getKepalaDesaInfo();

        $pdf = Pdf::loadView('pdf.keuangan.laporan-proyek', compact('proyek', 'desaInfo', 'kepalaInfo', 'tahun', 'status'))
            ->setPaper('a4', 'landscape')
            ->setOption('defaultFont', 'sans-serif')
            ->setOption('dpi', 150);

        $filename = "Laporan-Proyek-Desa-{$tahun}" . ($status ? "-{$status}" : '') . ".pdf";
        return $pdf->download($filename);
    }
}
