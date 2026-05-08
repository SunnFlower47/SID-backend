<?php

namespace App\Http\Controllers\ApiAdminPanel;

use Illuminate\Http\Request;
use App\Models\SuratPengajuan;
use App\Models\Penduduk;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class SuratPengajuanController extends Controller
{
    /**
     * Display a listing of pengajuan (requests).
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('pelayanan_informasi');

        $query = SuratPengajuan::with(['penduduk', 'admin']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhereHas('penduduk', fn($q) => $q->where('nama', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%"));
            });
        }

        $pengajuans = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $pengajuans
        ]);
    }

    /**
     * Store a new request (usually via admin or API).
     */
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('pelayanan_informasi');

        $validated = $request->validate([
            'jenis_surat' => 'required|string',
            'penduduk_id' => 'required|exists:penduduks,id',
            'keperluan' => 'nullable|string|max:500',
            'tujuan' => 'nullable|string|max:255',
            'tanggal_surat' => 'required|date',
            'data_tambahan' => 'nullable|array',
            'penandatangan' => 'nullable|in:kepala_desa,sekretaris_desa'
        ]);

        $pengajuan = SuratPengajuan::create([
            ...$validated,
            'nomor_surat' => $this->generateNomorSurat($validated['jenis_surat']),
            'status' => 'pending',
            'data_tambahan' => $validated['data_tambahan'] ?? [],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengajuan surat berhasil dibuat',
            'data' => $pengajuan
        ], 201);
    }

    /**
     * Display the specified request details.
     */
    public function show(SuratPengajuan $suratPengajuan): JsonResponse
    {
        Gate::authorize('pelayanan_informasi');
        $suratPengajuan->load(['penduduk', 'admin']);
        
        return response()->json([
            'status' => 'success',
            'data' => $suratPengajuan
        ]);
    }

    /**
     * Approve or Reject request.
     */
    public function updateStatus(Request $request, SuratPengajuan $suratPengajuan): JsonResponse
    {
        Gate::authorize('pelayanan_informasi');

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,completed',
            'keterangan' => 'nullable|string|max:1000'
        ]);

        $updateData = [
            'status' => $validated['status'],
            'keterangan_admin' => $validated['keterangan'] ?? null,
            'admin_id' => auth()->id()
        ];

        if ($validated['status'] === 'approved' || $validated['status'] === 'completed') {
            $updateData['approved_at'] = now();
        }

        $suratPengajuan->update($updateData);

        return response()->json([
            'status' => 'success',
            'message' => "Status pengajuan berhasil diubah menjadi {$validated['status']}",
            'data' => $suratPengajuan
        ]);
    }

    /**
     * Get surat statistics.
     */
    public function statistics(): JsonResponse
    {
        Gate::authorize('pelayanan_informasi');

        $stats = [
            'total' => SuratPengajuan::count(),
            'pending' => SuratPengajuan::where('status', 'pending')->count(),
            'approved' => SuratPengajuan::where('status', 'approved')->count(),
            'completed' => SuratPengajuan::where('status', 'completed')->count(),
            'rejected' => SuratPengajuan::where('status', 'rejected')->count(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }

    /**
     * Download (generate PDF) for a surat.
     */
    public function download(SuratPengajuan $surat): JsonResponse
    {
        Gate::authorize('pelayanan_informasi');
        
        // Sementara kasih response sukses dulu, nanti logic PDF-nya kita pindahin dari Tenant
        return response()->json([
            'status' => 'success',
            'message' => 'Logic download sedang disiapkan',
            'data' => $surat
        ]);
    }

    private function generateNomorSurat($suratType)
    {
        $suratSettings = \App\Models\DesaSetting::getSuratSettings();
        $kodeSurat = $suratSettings["kode_surat_{$suratType}"] ?? 'SK';
        return \App\Models\DesaSetting::generateNomorSurat($kodeSurat);
    }
}
