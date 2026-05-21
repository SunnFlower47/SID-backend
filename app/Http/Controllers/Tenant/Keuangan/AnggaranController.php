<?php

namespace App\Http\Controllers\Tenant\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Apbdes;
use App\Models\ProyekDesa;
use App\Models\HistoriPengeluaran;
use App\Models\PeraturanDesa;
use App\Services\Keuangan\AnggaranService;
use App\Http\Requests\Keuangan\StoreAnggaranTahunanRequest;
use App\Http\Requests\Keuangan\StorePengeluaranRequest;
use App\Http\Requests\Keuangan\UpdatePengeluaranRequest;
use App\Http\Requests\Keuangan\StoreProyekRequest;
use App\Http\Requests\Keuangan\UpdateApbdesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class AnggaranController extends Controller
{
    protected $anggaranService;

    public function __construct(AnggaranService $anggaranService)
    {
        $this->anggaranService = $anggaranService;
        $this->middleware(['auth', 'can:keuangan']);
    }

    /**
     * Show form to create annual budget
     */
    public function createAnggaranTahunan()
    {
        $tahunList   = Apbdes::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        $currentYear = (int) date('Y');

        return Inertia::render('Tenant/Keuangan/APBDes/Create', [
            'tahunList'   => $tahunList,
            'currentYear' => $currentYear,
        ]);
    }

    /**
     * Store annual budget
     */
    public function storeAnggaranTahunan(StoreAnggaranTahunanRequest $request)
    {
        try {
            $apbdes = $this->anggaranService->storeAnggaranTahunan($request->validated());

            return redirect()->route('transparansi-desa.apbdes', ['tahun' => $apbdes->tahun])
                ->with('success', 'Anggaran tahunan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show form to add expenditure
     */
    public function createPengeluaran(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $jenis = $request->get('jenis', 'belanja');

        $apbdesList = Apbdes::tahun($tahun)
            ->jenis($jenis)
            ->where('status', 'disetujui')
            ->whereRaw('realisasi < anggaran')
            ->orderBy('nama_rekening')
            ->get(['id', 'kode_rekening', 'nama_rekening', 'anggaran', 'realisasi', 'sisa_anggaran']);

        $tahunList = Apbdes::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        return Inertia::render('Tenant/Keuangan/APBDes/AddExpenditure', [
            'apbdesList' => $apbdesList,
            'tahunList'  => $tahunList,
            'tahun'      => $tahun,
            'jenis'      => $jenis,
        ]);
    }

    /**
     * Store expenditure
     */
    public function storePengeluaran(StorePengeluaranRequest $request)
    {
        try {
            $pengeluaran = $this->anggaranService->storePengeluaran(
                $request->validated(),
                $request->file('file_bukti')
            );

            return redirect()->route('transparansi-desa.apbdes', ['tahun' => $pengeluaran->apbdes->tahun])
                ->with('success', 'Pengeluaran "' . $pengeluaran->nama_pengeluaran . '" berhasil ditambahkan. No. Bukti: ' . $pengeluaran->no_bukti);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage(), 'jumlah' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show form to create project
     */
    public function createProyek()
    {
        $tahunList   = Apbdes::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        $currentYear = (int) date('Y');

        $apbdesList = Apbdes::where('tahun', $currentYear)
            ->where('jenis', 'belanja')
            ->where('status', 'disetujui')
            ->whereRaw('realisasi < anggaran')
            ->orderBy('nama_rekening')
            ->get(['id', 'kode_rekening', 'nama_rekening', 'anggaran', 'realisasi', 'sisa_anggaran']);

        return Inertia::render('Tenant/Keuangan/Proyek/Create', [
            'tahunList'   => $tahunList,
            'currentYear' => $currentYear,
            'apbdesList'  => $apbdesList,
        ]);
    }

    /**
     * Store project and link to APBDes
     */
    public function storeProyek(StoreProyekRequest $request)
    {
        try {
            $proyek = $this->anggaranService->storeProyek($request->validated());

            $apbdes = $proyek->apbdes;
            $message = 'Proyek berhasil dibuat dan terhubung dengan rekening APBDes: ' . $apbdes->kode_rekening . ' - ' . $apbdes->nama_rekening . '.';
            $message .= ' Sisa anggaran rekening: Rp ' . number_format((float)$apbdes->sisa_anggaran, 0, ',', '.') . '.';

            return redirect()->route('transparansi-desa.proyek')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage(), 'anggaran' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Update project realization and sync with APBDes
     */
    public function updateRealisasiProyek(Request $request, ProyekDesa $proyek)
    {
        $validator = Validator::make($request->all(), [
            'realisasi' => 'required|numeric|min:0|max:' . $proyek->anggaran,
            'keterangan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->anggaranService->updateRealisasiProyek($proyek, $request->only(['realisasi']));

            return response()->json([
                'success' => true,
                'message' => 'Realisasi proyek berhasil diperbarui',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show movement history for specific APBDes (all types: pendapatan, belanja, pembiayaan)
     */
    public function historiPengeluaran($id)
    {
        $apbdes = Apbdes::with(['historiPengeluarans' => function ($q) {
            $q->orderBy('tanggal_pengeluaran', 'desc');
        }])->findOrFail($id);

        $apbdes->historiPengeluarans->each(fn ($h) => $h->append('file_bukti_url', 'has_dokumen', 'jenis_bukti_label'));

        return Inertia::render('Tenant/Keuangan/APBDes/History', [
            'apbdes'          => $apbdes,
            'jenisBuktiOptions' => HistoriPengeluaran::JENIS_BUKTI,
        ]);
    }

    /**
     * Show edit form for expenditure
     */
    public function editPengeluaran($id)
    {
        $pengeluaran = HistoriPengeluaran::with('apbdes')->findOrFail($id);
        $pengeluaran->append('file_bukti_url');

        return Inertia::render('Tenant/Keuangan/APBDes/EditExpenditure', [
            'pengeluaran'       => $pengeluaran,
            'jenisBuktiOptions' => HistoriPengeluaran::JENIS_BUKTI,
        ]);
    }

    /**
     * Update expenditure
     */
    public function updatePengeluaran(UpdatePengeluaranRequest $request, $id)
    {
        $pengeluaran = HistoriPengeluaran::with('apbdes')->findOrFail($id);

        try {
            $this->anggaranService->updatePengeluaran(
                $pengeluaran,
                $request->validated(),
                $request->file('file_bukti')
            );

            return redirect()->route('anggaran.histori-pengeluaran', $pengeluaran->apbdes_id)
                ->with('success', 'Pengeluaran "' . $request->nama_pengeluaran . '" berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage(), 'jumlah' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Delete expenditure
     */
    public function deletePengeluaran($id)
    {
        $pengeluaran = HistoriPengeluaran::with('apbdes')->findOrFail($id);

        try {
            $nama = $pengeluaran->nama_pengeluaran;
            $apbdesId = $pengeluaran->apbdes_id;

            $this->anggaranService->deletePengeluaran($pengeluaran);

            return redirect()->route('anggaran.histori-pengeluaran', $apbdesId)
                ->with('success', 'Pengeluaran "' . $nama . '" berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Show edit form for APBDes
     */
    public function editApbdes($id)
    {
        $this->authorize('anggaran.edit');
        $apbdes = Apbdes::findOrFail($id);

        return Inertia::render('Tenant/Keuangan/APBDes/Edit', [
            'apbdes' => $apbdes,
        ]);
    }

    /**
     * Update APBDes
     */
    public function updateApbdes(UpdateApbdesRequest $request, $id)
    {
        $this->authorize('anggaran.edit');
        $apbdes = Apbdes::findOrFail($id);

        try {
            $this->anggaranService->updateApbdes($apbdes, $request->validated());

            return redirect()->route('transparansi-desa.apbdes', ['tahun' => $apbdes->tahun])
                ->with('success', 'Data APBDes "' . $request->nama_rekening . '" berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage(), 'anggaran' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Delete APBDes
     */
    public function deleteApbdes($id)
    {
        $this->authorize('anggaran.delete');
        $apbdes = Apbdes::findOrFail($id);

        try {
            $tahun = $apbdes->tahun;
            $nama = $apbdes->nama_rekening;

            $this->anggaranService->deleteApbdes($apbdes);

            return redirect()->route('transparansi-desa.apbdes', ['tahun' => $tahun])
                ->with('success', 'Data APBDes "' . $nama . '" berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
