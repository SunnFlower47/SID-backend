<?php

namespace App\Http\Controllers\ApiAdminPanel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Penduduk;
use App\Models\DesaSetting;
use App\Models\Surat;
use App\Models\SuratPengajuan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class SuratController extends Controller
{
    /**
     * Display a listing of available letter types
     */
    public function index(): JsonResponse
    {
        Gate::authorize('pelayanan_informasi');

        // Fetch from database for consistency with frontend
        $suratTypes = \App\Models\SuratType::where('is_active', true)
            ->orderBy('has_template', 'desc')
            ->orderBy('nama')
            ->get()
            ->map(function ($type) {
                return [
                    'id' => $type->template_code ?? $type->id,
                    'name' => $type->nama,
                    'description' => $type->deskripsi,
                    'icon' => $type->icon ?? 'fas fa-file-alt',
                    'color' => $type->color ?? 'blue',
                    'has_template' => (bool)$type->has_template,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'types' => $suratTypes,
                'stats' => $this->getSuratStatistics()
            ]
        ]);
    }

    /**
     * Store surat and save to history
     */
    public function store(Request $request, $type): JsonResponse
    {
        Gate::authorize('pelayanan_informasi');

        $validated = $request->validate([
            'penduduk_id' => 'required|exists:penduduks,id',
            'keperluan' => 'nullable|string|max:500',
            'tujuan' => 'nullable|string|max:255',
            'tanggal_surat' => 'nullable|date',
            'keterangan_tambahan' => 'nullable|string|max:1000',
            'data_tambahan' => 'nullable|array',
            'penandatangan' => 'nullable|in:kepala_desa,sekretaris_desa'
        ]);

        $penduduk = Penduduk::findOrFail($validated['penduduk_id']);

        $surat = Surat::create([
            'penduduk_id' => $penduduk->id,
            'jenis_surat' => $type,
            'keperluan' => $validated['keperluan'] ?? '',
            'tujuan' => $validated['tujuan'] ?? '',
            'tanggal_surat' => $validated['tanggal_surat'] ? Carbon::parse($validated['tanggal_surat']) : Carbon::now(),
            'keterangan_tambahan' => $validated['keterangan_tambahan'] ?? '',
            'data_tambahan' => $validated['data_tambahan'] ?? [],
            'status' => 'selesai',
            'nomor_surat' => $this->generateNomorSurat($type),
            'created_by' => Auth::id(),
            'penandatangan' => $validated['penandatangan'] ?? 'kepala_desa',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Surat berhasil diterbitkan',
            'data' => $surat
        ], 201);
    }

    /**
     * Get surat history (Combined with approved requests)
     */
    public function history(Request $request): JsonResponse
    {
        Gate::authorize('pelayanan_informasi');

        $query = Surat::with(['penduduk', 'creator']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhereHas('penduduk', fn($q) => $q->where('nama', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('jenis_surat')) {
            $query->where('jenis_surat', $request->jenis_surat);
        }

        $history = $query->latest()->paginate($request->get('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $history
        ]);
    }

    /**
     * Generate PDF (Return stream)
     */
    public function download(Surat $surat)
    {
        Gate::authorize('pelayanan_informasi');

        $data = $this->prepareSuratData($surat->penduduk, [
            'keperluan' => $surat->keperluan,
            'tujuan' => $surat->tujuan,
            'tanggal_surat' => $surat->tanggal_surat,
            'keterangan_tambahan' => $surat->keterangan_tambahan,
            'penandatangan' => $surat->penandatangan ?? 'kepala_desa'
        ], $surat->jenis_surat, $surat->data_tambahan);

        $data['surat'] = $surat;
        $view = "surat.templates.{$surat->jenis_surat}";
        $pdf = Pdf::loadView($view, $data);

        if ($surat->jenis_surat === 'kematian') {
            $pdf->setPaper(array(0, 0, 609.4488, 935.433), 'landscape');
        } else {
            $pdf->setPaper('A4', 'portrait');
        }

        return $pdf->stream("Surat_{$surat->nomor_surat}.pdf");
    }

    private function generateNomorSurat($type)
    {
        $suratSettings = DesaSetting::getSuratSettings();
        $kodeSurat = $suratSettings["kode_surat_{$type}"] ?? 'SK';
        return DesaSetting::generateNomorSurat($kodeSurat);
    }

    private function getSuratStatistics()
    {
        $today = Carbon::today();
        return [
            'total' => Surat::count(),
            'hari_ini' => Surat::whereDate('created_at', $today)->count(),
            'minggu_ini' => Surat::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'bulan_ini' => Surat::whereMonth('created_at', Carbon::now()->month)->count(),
        ];
    }

    private function prepareSuratData($penduduk, $validated, $type, $dataTambahan = [])
    {
        $penandatangan = $validated['penandatangan'] ?? 'kepala_desa';
        $signerData = ($penandatangan === 'sekretaris_desa') ? DesaSetting::getSekretarisInfo() : DesaSetting::getKepalaDesaInfo();

        return [
            'penduduk' => $penduduk,
            'keperluan' => $validated['keperluan'] ?? '',
            'tujuan' => $validated['tujuan'] ?? '',
            'tanggal_surat' => $validated['tanggal_surat'] ? Carbon::parse($validated['tanggal_surat']) : Carbon::now(),
            'keterangan_tambahan' => $validated['keterangan_tambahan'] ?? '',
            'desa' => DesaSetting::getDesaInfo(),
            'kepala_desa' => $signerData,
            'penandatangan_raw' => $signerData,
            'is_sekdes' => ($penandatangan === 'sekretaris_desa'),
            'sekretaris' => DesaSetting::getSekretarisInfo(),
            'logos' => DesaSetting::getLogos(),
            'template_settings' => [
                'header' => DesaSetting::getValue('template_header', "PEMERINTAH KABUPATEN GARUT\nKECAMATAN CIBATU\nDESA CIBATU"),
                'footer' => DesaSetting::getValue('template_footer', "Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.")
            ]
        ] + (is_array($dataTambahan) ? $dataTambahan : []);
    }
}
