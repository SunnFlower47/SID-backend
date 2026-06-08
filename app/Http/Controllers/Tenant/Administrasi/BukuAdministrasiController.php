<?php

namespace App\Http\Controllers\Tenant\Administrasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\Administrasi\BukuAdministrasiService;

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

            return Inertia::render('Tenant/Administrasi/Buku/InventarisKekayaan/Index', [
                'data'       => $data,
                'filters'    => ['tahun' => $tahun],
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

            return Inertia::render('Tenant/Administrasi/Buku/TanahKasDesa/Index', [
                'data'       => $data,
                'filters'    => $request->only(['search']),
            ]);
        }

        if ($jenis_buku === 'buku-induk-penduduk') {
            $data = $this->bukuService->getData($jenis_buku, $request->all(), false);
            
            return Inertia::render('Tenant/Administrasi/Buku/BukuIndukPenduduk/Index', [
                'data'       => $data,
                'filters'    => $request->all(),
                'rtList'     => \App\Models\Rt::all(),
                'rwList'     => \App\Models\Rw::all(),
                'dusunList'  => \App\Models\Dusun::all(),
            ]);
        }

        $filters = $request->only(['start_date', 'end_date', 'search', 'tahun']);
        
        // Default tahun ke tahun berjalan jika tidak diisi
        if (empty($filters['tahun'])) {
            $filters['tahun'] = date('Y');
        }

        $data = $this->bukuService->getData($jenis_buku, $filters);

        switch ($jenis_buku) {
            case 'peraturan-desa':
                $viewName = 'Tenant/Administrasi/Buku/PeraturanDesa/Index';
                break;
            case 'keputusan-kades':
                $viewName = 'Tenant/Administrasi/Buku/KeputusanKades/Index';
                break;
            case 'buku-agenda':
                $viewName = 'Tenant/Administrasi/Buku/BukuAgenda/Index';
                break;
            case 'aparat-pemerintah':
                $viewName = 'Tenant/Administrasi/Buku/AparatPemerintah/Index';
                break;
            case 'tanah-di-desa':
                $viewName = 'Tenant/Administrasi/Buku/TanahDiDesa/Index';
                break;
            case 'buku-mutasi-penduduk':
                $viewName = 'Tenant/Administrasi/Buku/BukuMutasiPenduduk/Index';
                break;
            case 'buku-rekapitulasi-penduduk':
                $viewName = 'Tenant/Administrasi/Buku/BukuRekapitulasiPenduduk/Index';
                break;
            case 'buku-penduduk-sementara':
                $viewName = 'Tenant/Administrasi/Buku/BukuPendudukSementara/Index';
                break;
            case 'buku-ktp-kk':
                $viewName = 'Tenant/Administrasi/Buku/BukuKtpKk/Index';
                break;
            case 'rkp-desa':
                $viewName = 'Tenant/Administrasi/Buku/RkpDesa/Index';
                break;
            case 'buku-kegiatan-pembangunan':
                $viewName = 'Tenant/Administrasi/Buku/BukuKegiatanPembangunan/Index';
                break;
            case 'buku-inventaris-pembangunan':
                $viewName = 'Tenant/Administrasi/Buku/BukuInventarisPembangunan/Index';
                break;
            case 'buku-apb-desa':
                $viewName = 'Tenant/Administrasi/Buku/BukuApbdes/Index';
                break;
            case 'buku-rab':
                $viewName = 'Tenant/Administrasi/Buku/BukuRab/Index';
                break;
            case 'buku-kas-pembantu-kegiatan':
                $viewName = 'Tenant/Administrasi/Buku/BukuKasPembantuKegiatan/Index';
                break;
            default:
                $viewName = 'Tenant/Administrasi/Buku/BukuStandard/Index';
        }

        $viewData = [
            'jenis_buku' => $jenis_buku,
            'data' => $data,
            'filters' => $filters,
        ];

        if ($jenis_buku === 'buku-kas-pembantu-kegiatan') {
            $viewData['apbdes_list'] = \App\Models\Apbdes::where('jenis', 'belanja')->orderBy('kode_rekening')->get(['id', 'kode_rekening', 'nama_rekening']);
        }

        return inertia($viewName, $viewData);
    }

    public function exportExcel($jenis_buku, Request $request)
    {
        // Inventaris Kekayaan pakai kalkulasi per-tahun
        if ($jenis_buku === 'inventaris-kekayaan') {
            $tahun = (int) $request->get('tahun', date('Y'));
            $data  = $this->bukuService->getInventarisKekayaanPdf($tahun);
            
            $viewName = 'pdf.buku-administrasi.inventaris-kekayaan';
            $viewData = [
                'data'  => $data,
                'tahun' => $tahun,
            ];
            
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\Buku\InventarisKekayaanExport($viewName, $viewData), 
                "Buku_Inventaris_Kekayaan_{$tahun}.xlsx"
            );
        }

        $filters = $request->only(['start_date', 'end_date', 'search', 'tahun', 'apbdes_id']);
        if (empty($filters['tahun'])) {
            $filters['tahun'] = date('Y');
        }

        $data = $this->bukuService->getData($jenis_buku, $filters, true);
        $viewName = 'pdf.buku-administrasi.' . $jenis_buku;
        
        if (!view()->exists($viewName)) {
            abort(404, "Template ekspor untuk buku ini belum tersedia.");
        }

        $viewData = [
            'data'       => $data,
            'filters'    => $filters,
            'jenis_buku' => $jenis_buku,
            'is_excel'   => true
        ];

        // Kependudukan khusus via Query Generator (Induk, Mutasi, Sementara, KTP KK) atau Rekapitulasi
        if (in_array($jenis_buku, ['buku-induk-penduduk', 'buku-mutasi-penduduk', 'buku-rekapitulasi-penduduk', 'buku-penduduk-sementara', 'buku-ktp-kk'])) {
            if ($jenis_buku === 'buku-rekapitulasi-penduduk') {
                $exportClass = \App\Exports\Buku\BukuRekapitulasiPendudukExport::class;
                return \Maatwebsite\Excel\Facades\Excel::download(new $exportClass($request->all()), "Rekapitulasi_Penduduk.xlsx");
            }
            $query = $this->bukuService->getQuery($jenis_buku, $request->all());
            
            if ($jenis_buku === 'buku-induk-penduduk') $exportClass = \App\Exports\Buku\BukuIndukPendudukExport::class;
            elseif ($jenis_buku === 'buku-mutasi-penduduk') $exportClass = \App\Exports\Buku\BukuMutasiPendudukExport::class;
            elseif ($jenis_buku === 'buku-penduduk-sementara') $exportClass = \App\Exports\Buku\BukuPendudukSementaraExport::class;
            else $exportClass = \App\Exports\Buku\BukuKtpKkExport::class;
            
            if ($jenis_buku === 'buku-induk-penduduk') $fileName = "Induk_Penduduk.xlsx";
            elseif ($jenis_buku === 'buku-mutasi-penduduk') $fileName = "Mutasi_Penduduk.xlsx";
            elseif ($jenis_buku === 'buku-penduduk-sementara') $fileName = "Penduduk_Sementara.xlsx";
            else $fileName = "Buku_KTP_dan_KK.xlsx";
            
            return \Maatwebsite\Excel\Facades\Excel::download(new $exportClass($query), $fileName);
        }

        // Pemetaan kelas export spesifik (Buku selain Penduduk dan Inventaris)
        $exportMap = [
            'peraturan-desa' => \App\Exports\Buku\PeraturanDesaExport::class,
            'keputusan-kades' => \App\Exports\Buku\KeputusanKadesExport::class,
            'buku-agenda' => \App\Exports\Buku\BukuAgendaExport::class,
            'aparat-pemerintah' => \App\Exports\Buku\AparatPemerintahExport::class,
            'tanah-kas-desa' => \App\Exports\Buku\TanahKasDesaExport::class,
            'tanah-di-desa' => \App\Exports\Buku\TanahDiDesaExport::class,
            'rkp-desa' => \App\Exports\Buku\RkpDesaExport::class,
            'buku-kegiatan-pembangunan' => \App\Exports\Buku\BukuKegiatanPembangunanExport::class,
            'buku-inventaris-pembangunan' => \App\Exports\Buku\BukuInventarisPembangunanExport::class,
            'buku-apb-desa' => \App\Exports\Buku\BukuApbDesaExport::class,
            'buku-rab' => \App\Exports\Buku\BukuRabExport::class,
            'buku-kas-pembantu-kegiatan' => \App\Exports\Buku\BukuKasPembantuKegiatanExport::class,
            'buku-kas-umum' => \App\Exports\Buku\BukuKasUmumExport::class,
            'buku-kas-pembantu-pajak' => \App\Exports\Buku\BukuKasPembantuPajakExport::class,
        ];

        // Default fallback
        $exportClass = $exportMap[$jenis_buku] ?? \App\Exports\Buku\BukuAgendaExport::class;

        // Potong nama agar tidak lebih dari 31 karakter (limit sheet Excel)
        $shortName = substr(str_replace('-', '_', $jenis_buku), 0, 20);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new $exportClass($viewName, $viewData), 
            $shortName . "_" . date('Ymd') . ".xlsx"
        );
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

            // Fallback proteksi RAM
            if ($data->count() > 500) {
                $data = $data->take(500);
            }

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.buku-administrasi.inventaris-kekayaan', [
                'data'   => $data,
                'tahun'  => $tahun,
            ]);
            $pdf->setPaper('A3', 'landscape');
            return $pdf->stream("buku-inventaris-kekayaan-{$tahun}.pdf");
        }

        // Buku-buku lain: Batasi kueri untuk kependudukan agar memori tidak habis
        if (in_array($jenis_buku, ['buku-induk-penduduk', 'buku-mutasi-penduduk', 'buku-penduduk-sementara', 'buku-ktp-kk'])) {
            $data = $this->bukuService->getQuery($jenis_buku, $request->all())->limit(500)->get();
            $filters = $request->only(['start_date', 'end_date', 'search']);
        } else if ($jenis_buku === 'buku-rekapitulasi-penduduk') {
            $data = $this->bukuService->getData($jenis_buku, $request->all(), true);
            $filters = $request->only(['start_date', 'end_date', 'search']);
        } else {
            $filters = $request->only(['start_date', 'end_date', 'search', 'tahun', 'apbdes_id']);
            if (empty($filters['tahun'])) {
                $filters['tahun'] = date('Y');
            }
            $data = $this->bukuService->getData($jenis_buku, $filters, true);
            if ($data->count() > 500) {
                $data = $data->take(500);
            }
        }

        $viewName = 'pdf.buku-administrasi.' . $jenis_buku;
        if (!view()->exists($viewName)) {
            abort(404, "Template PDF untuk buku ini belum tersedia.");
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($viewName, [
            'data'       => $data,
            'filters'    => $filters,
            'jenis_buku' => $jenis_buku
        ]);

        $paper = ['Legal', 'landscape'];
        if ($jenis_buku === 'tanah-kas-desa') $paper = ['A3', 'landscape'];
        if ($jenis_buku === 'buku-mutasi-penduduk') $paper = ['Legal', 'landscape'];

        $pdf->setPaper($paper[0], $paper[1]);

        return $pdf->stream("buku-{$jenis_buku}-" . date('YmdHis') . ".pdf");
    }
}
