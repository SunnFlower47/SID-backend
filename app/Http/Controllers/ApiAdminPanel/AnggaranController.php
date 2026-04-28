<?php

namespace App\Http\Controllers\ApiAdminPanel;

use App\Models\Apbdes;
use App\Models\ProyekDesa;
use App\Models\HistoriPengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class AnggaranController extends Controller
{
    /**
     * Get APBDes data for a specific year.
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('transparansi-desa.view');
        
        $tahun = $request->get('tahun', date('Y'));
        $apbdes = Apbdes::where('tahun', $tahun)->orderBy('kode_rekening')->get();
        
        $summary = [
            'total_pendapatan' => $apbdes->where('jenis', 'pendapatan')->sum('anggaran'),
            'total_belanja' => $apbdes->where('jenis', 'belanja')->sum('anggaran'),
            'realisasi_belanja' => $apbdes->where('jenis', 'belanja')->sum('realisasi'),
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'items' => $apbdes,
                'summary' => $summary,
                'available_years' => Apbdes::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun')
            ]
        ]);
    }

    /**
     * Store annual budget.
     */
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('transparansi-desa.view');

        $validated = $request->validate([
            'tahun' => 'required|integer|min:2020|max:2030',
            'jenis' => 'required|in:pendapatan,belanja,pembiayaan',
            'sumber_dana' => 'required|string',
            'kode_rekening' => 'required|string|max:20',
            'nama_rekening' => 'required|string|max:255',
            'anggaran' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $data = $validated;
        $data['realisasi'] = 0;
        $data['sisa_anggaran'] = $validated['anggaran'];
        $data['status'] = 'disetujui';

        $apbdes = Apbdes::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Anggaran berhasil ditambahkan',
            'data' => $apbdes
        ], 201);
    }

    /**
     * Store expenditure and update realization.
     */
    public function storePengeluaran(Request $request): JsonResponse
    {
        Gate::authorize('transparansi-desa.view');

        $validated = $request->validate([
            'apbdes_id' => 'required|exists:apbdes,id',
            'nama_pengeluaran' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tanggal_pengeluaran' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $apbdes = Apbdes::findOrFail($validated['apbdes_id']);
            $sisa = $apbdes->anggaran - $apbdes->realisasi;

            if ($validated['jumlah'] > $sisa) {
                return response()->json(['status' => 'error', 'message' => 'Jumlah melebihi sisa anggaran (Rp '.number_format($sisa).')'], 422);
            }

            $histori = HistoriPengeluaran::create([
                'nama_pengeluaran' => $validated['nama_pengeluaran'],
                'apbdes_id' => $apbdes->id,
                'jumlah' => $validated['jumlah'],
                'tanggal_pengeluaran' => $validated['tanggal_pengeluaran'],
                'keterangan' => $validated['keterangan'],
                'user_id' => auth()->id(),
            ]);

            $apbdes->realisasi += $validated['jumlah'];
            $apbdes->sisa_anggaran = $apbdes->anggaran - $apbdes->realisasi;
            $apbdes->save();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Pengeluaran berhasil dicatat', 'data' => $histori]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * List all projects.
     */
    public function listProyek(): JsonResponse
    {
        Gate::authorize('transparansi-desa.view');
        $proyek = ProyekDesa::with('apbdes')->latest()->get();
        return response()->json(['status' => 'success', 'data' => $proyek]);
    }
}
