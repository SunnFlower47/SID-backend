<?php

namespace App\Http\Controllers\Tenant\Administrasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\BukuAdministrasiService;

class BukuAdministrasiController extends Controller
{
    protected $bukuService;

    public function __construct(BukuAdministrasiService $bukuService)
    {
        $this->bukuService = $bukuService;
    }

    /**
     * Menampilkan dashboard utama Buku Administrasi Desa.
     */
    public function index()
    {
        return Inertia::render('Tenant/Administrasi/Buku/Index');
    }

    /**
     * Menampilkan preview data untuk satu jenis buku.
     */
    public function show($jenis_buku, Request $request)
    {
        // Inventaris kekayaan: gunakan data per-tahun yang sudah dikalkulasi
        if ($jenis_buku === 'inventaris-kekayaan') {
            $tahun = (int) $request->get('tahun', date('Y'));
            $data  = $this->bukuService->getInventarisKekayaanPdf($tahun)->values();

            return Inertia::render('Tenant/Administrasi/Buku/Show', [
                'jenis_buku' => $jenis_buku,
                'filters'    => ['tahun' => $tahun],
                'data'       => $data,
            ]);
        }

        // Tanah kas desa: pakai paginated data tapi items-nya di-map agar accessor saldo_kwantitas terkirim
        if ($jenis_buku === 'tanah-kas-desa') {
            $data = $this->bukuService->getData($jenis_buku, $request->all(), false);
            $data->through(fn($item) => [
                'id'                  => $item->id,
                'nama_barang_override'=> $item->nama_barang_override,
                'barang'              => $item->barang ? ['nama_barang' => $item->barang->nama_barang, 'kode_barang' => $item->barang->kode_barang] : null,
                'lokasi'              => $item->lokasi,
                'no_sertifikat'       => $item->no_sertifikat,
                'asal_usul'           => $item->asal_usul,
                'kondisi'             => $item->kondisi,
                'saldo_kwantitas'     => $item->saldo_kwantitas,  // computed accessor
                'keterangan'          => $item->keterangan,
            ]);

            return Inertia::render('Tenant/Administrasi/Buku/Show', [
                'jenis_buku' => $jenis_buku,
                'filters'    => $request->only(['search']),
                'data'       => $data,
            ]);
        }

        $data = $this->bukuService->getData($jenis_buku, $request->all());

        return Inertia::render('Tenant/Administrasi/Buku/Show', [
            'jenis_buku' => $jenis_buku,
            'filters'    => $request->only(['start_date', 'end_date', 'search', 'tahun']),
            'data'       => $data
        ]);
    }

    /**
     * Export data ke Excel
     */
    public function exportExcel($jenis_buku, Request $request)
    {
        // TODO: Integrasi dengan Maatwebsite Excel
    }

    /**
     * Export data ke PDF (Landscape)
     */
    public function exportPdf($jenis_buku, Request $request)
    {
        // Inventaris Kekayaan pakai kalkulasi per-tahun (Permendagri format lengkap)
        if ($jenis_buku === 'inventaris-kekayaan') {
            $tahun = (int) $request->get('tahun', date('Y'));
            $data  = $this->bukuService->getInventarisKekayaanPdf($tahun);

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.buku-administrasi.inventaris-kekayaan', [
                'data'   => $data,
                'tahun'  => $tahun,
            ]);
            $pdf->setPaper('A3', 'landscape');
            return $pdf->stream("buku-inventaris-kekayaan-{$tahun}.pdf");
        }

        // Buku-buku lain: gunakan query standar
        $data = $this->bukuService->getData($jenis_buku, $request->all(), true);

        $viewName = 'pdf.buku-administrasi.' . $jenis_buku;
        if (!view()->exists($viewName)) {
            abort(404, "Template PDF untuk buku ini belum tersedia.");
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($viewName, [
            'data'       => $data,
            'filters'    => $request->only(['start_date', 'end_date', 'search']),
            'jenis_buku' => $jenis_buku
        ]);

        // Tanah kas desa butuh A3 landscape karena banyak kolom
        $paper = $jenis_buku === 'tanah-kas-desa' ? ['A3', 'landscape'] : ['Legal', 'landscape'];
        $pdf->setPaper($paper[0], $paper[1]);

        return $pdf->stream("buku-{$jenis_buku}-" . date('YmdHis') . ".pdf");
    }
}
