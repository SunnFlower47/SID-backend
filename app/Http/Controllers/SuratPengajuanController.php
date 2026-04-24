<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratPengajuan;
use App\Models\Penduduk;
use App\Models\DesaSetting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class SuratPengajuanController extends Controller
{
        public function __construct()
    {
        $this->middleware(['auth', 'can:surat.view']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SuratPengajuan::with(['penduduk', 'admin']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by surat type
        if ($request->has('jenis_surat') && $request->jenis_surat) {
            $query->where('jenis_surat', $request->jenis_surat);
        }

        // Search by nomor surat or penduduk name
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nomor_surat', 'like', "%{$request->search}%")
                  ->orWhereHas('penduduk', function($pendudukQuery) use ($request) {
                      $pendudukQuery->where('nama', 'like', "%{$request->search}%")
                                   ->orWhere('nik', 'like', "%{$request->search}%");
                  });
            });
        }

        $pengajuans = $query->orderBy('created_at', 'desc')->paginate(20);

        $statusList = [
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'completed' => 'Selesai'
        ];

        $suratTypes = [
            'sku' => 'Surat Keterangan Usaha',
            'sktm_dewasa' => 'Surat Keterangan Tidak Mampu (Dewasa)',
            'sktm_anak' => 'Surat Keterangan Tidak Mampu (Anak)',
            'domisili' => 'Surat Keterangan Domisili',
            'kelahiran' => 'Surat Keterangan Kelahiran',
            'kematian' => 'Surat Keterangan Kematian',
            'pindah' => 'Surat Keterangan Pindah'
        ];

        return view('admin.surat-pengajuan.index', compact(
            'pengajuans',
            'statusList',
            'suratTypes'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $penduduks = Penduduk::whereNull('deleted_at')->orderBy('nama')->get();
        $suratTypes = [
            'sku' => 'Surat Keterangan Usaha',
            'sktm_dewasa' => 'Surat Keterangan Tidak Mampu (Dewasa)',
            'sktm_anak' => 'Surat Keterangan Tidak Mampu (Anak)',
            'domisili' => 'Surat Keterangan Domisili',
            'kelahiran' => 'Surat Keterangan Kelahiran',
            'kematian' => 'Surat Keterangan Kematian',
            'pindah' => 'Surat Keterangan Pindah'
        ];

        return view('admin.surat-pengajuan.create', compact('penduduks', 'suratTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('surat.create');

        $validated = $request->validate([
            'jenis_surat' => 'required|string',
            'penduduk_id' => 'required|exists:penduduks,id',
            'keperluan' => 'nullable|string|max:500',
            'tujuan' => 'nullable|string|max:255',
            'tanggal_surat' => 'required|date',
            'keterangan_tambahan' => 'nullable|string|max:1000',
            'data_tambahan' => 'nullable|array',
            'penandatangan' => 'nullable|in:kepala_desa,sekretaris_desa'
        ]);

        try {
            DB::beginTransaction();

            // Auto-approve if created by admin (via this controller)
            // Assuming this controller is guarded by admin or verified auth
            $status = 'completed'; // Direct completion for admin created letters
            $adminId = auth()->id();

            $pengajuan = SuratPengajuan::create([
                ...$validated,
                'nomor_surat' => $this->generateNomorSurat($validated['jenis_surat']),
                'status' => $status,
                'data_tambahan' => $validated['data_tambahan'] ?? [],
                'admin_id' => $adminId, // Track who created it
                'approved_at' => now(), // Auto approve timestamp
            ]);

            DB::commit();

            return redirect()->route('admin.surat-pengajuan.index')
                           ->with('success', 'Surat berhasil dibuat dan disetujui otomatis.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SuratPengajuan $suratPengajuan)
    {
        $suratPengajuan->load(['penduduk.kartuKeluarga', 'admin']);

        return view('admin.surat-pengajuan.show', compact('suratPengajuan'));
    }

    /**
     * Update status of surat pengajuan
     */
    public function updateStatus(Request $request, SuratPengajuan $suratPengajuan)
    {
        Gate::authorize('surat.edit');

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,completed',
            'keterangan' => 'nullable|string|max:1000'
        ]);

        $suratPengajuan->update($validated);

        // Clear relevant caches after updating        return redirect()->back()->with('success', 'Status surat berhasil diperbarui');
    }

    /**
     * Generate PDF for surat
     */
    public function generatePdf(SuratPengajuan $suratPengajuan)
    {
        Gate::authorize('surat.view');

        $suratPengajuan->load(['penduduk.kartuKeluarga']);

        // Get desa settings
        $desaSettings = DesaSetting::getByGroup('desa_info');

        // Prepare signer data
        $penandatangan = $suratPengajuan->penandatangan ?? 'kepala_desa';
        $signerData = ($penandatangan === 'sekretaris_desa')
            ? DesaSetting::getSekretarisInfo()
            : DesaSetting::getKepalaDesaInfo();

        // Prepare data for template
        $data = [
            'penduduk' => $suratPengajuan->penduduk,
            'pengajuan' => $suratPengajuan,
            'desa_info' => $desaSettings,
            'kepala_desa' => $signerData,
            'is_sekdes' => ($penandatangan === 'sekretaris_desa'),
            'tanggal_surat' => $suratPengajuan->tanggal_surat,
            'keperluan' => $suratPengajuan->keperluan,
            'tujuan' => $suratPengajuan->tujuan,
            'keterangan_tambahan' => $suratPengajuan->keterangan_tambahan,
            'data_tambahan' => $suratPengajuan->data_tambahan ?? []
        ];

        // Merge additional data for specific surat types
        if ($suratPengajuan->data_tambahan) {
            // Handle both string and array cases
            $dataTambahan = $suratPengajuan->data_tambahan;

            // If it's a string, try to decode it
            if (is_string($dataTambahan)) {
                $dataTambahan = json_decode($dataTambahan, true);
            }

            // Only proceed if we have a valid array
            if (is_array($dataTambahan) && !empty($dataTambahan)) {
                foreach ($dataTambahan as $key => $value) {
                    $data[$key] = $value;
                }
            }
        }

        try {
            $pdf = Pdf::loadView("surat.templates.{$this->getTemplateName($suratPengajuan->jenis_surat)}", $data);
            $pdf->setPaper(array(0, 0, 609.4488, 935.433), 'landscape'); // F4 Landscape

            // Replace invalid characters in filename
            $filename = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $suratPengajuan->nomor_surat) . '.pdf';

            return $pdf->stream($filename);

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    /**
     * Preview surat
     */
    public function preview(SuratPengajuan $suratPengajuan)
    {
        Gate::authorize('surat.view');

        $suratPengajuan->load(['penduduk.kartuKeluarga']);

        // Get desa settings
        $desaSettings = DesaSetting::getByGroup('desa_info');

        // Prepare data for template
        $penandatangan = $suratPengajuan->penandatangan ?? 'kepala_desa';
        $signerData = ($penandatangan === 'sekretaris_desa')
            ? DesaSetting::getSekretarisInfo()
            : DesaSetting::getKepalaDesaInfo();

        $data = [
            'penduduk' => $suratPengajuan->penduduk,
            'pengajuan' => $suratPengajuan,
            'desa_info' => $desaSettings,
            'kepala_desa' => $signerData,
            'is_sekdes' => ($penandatangan === 'sekretaris_desa'),
            'tanggal_surat' => $suratPengajuan->tanggal_surat,
            'keperluan' => $suratPengajuan->keperluan,
            'tujuan' => $suratPengajuan->tujuan,
            'keterangan_tambahan' => $suratPengajuan->keterangan_tambahan,
            'data_tambahan' => $suratPengajuan->data_tambahan ?? []
        ];

        // Merge additional data for specific surat types
        if ($suratPengajuan->data_tambahan) {
            // Handle both string and array cases
            $dataTambahan = $suratPengajuan->data_tambahan;

            // If it's a string, try to decode it
            if (is_string($dataTambahan)) {
                $dataTambahan = json_decode($dataTambahan, true);
            }

            // Only proceed if we have a valid array
            if (is_array($dataTambahan) && !empty($dataTambahan)) {
                foreach ($dataTambahan as $key => $value) {
                    $data[$key] = $value;
                }
            }
        }

        try {
            return view("surat.templates.{$this->getTemplateName($suratPengajuan->jenis_surat)}", $data);
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal memuat preview: ' . $e->getMessage());
        }
    }

    /**
     * Map jenis surat to template name
     */
    private function getTemplateName($jenisSurat)
    {
        $mapping = [
            'sku' => 'sku',
            'sktm_dewasa' => 'tidak-mampu-dewasa',
            'sktm_anak' => 'tidak-mampu-anak',
            'domisili' => 'keterangan-domisili',
            'kelahiran' => 'kelahiran',
            'kematian' => 'kematian',
            'pindah' => 'pindah'
        ];

        return $mapping[$jenisSurat] ?? $jenisSurat;
    }

    /**
     * Generate nomor surat
     */
    private function generateNomorSurat($suratType)
    {
        $suratSettings = \App\Models\DesaSetting::getSuratSettings();
        $kodeSurat = $suratSettings["kode_surat_{$suratType}"] ?? 'SK';

        return \App\Models\DesaSetting::generateNomorSurat($kodeSurat);
    }

    /**
     * Mark surat pengajuan as read
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            $suratPengajuan = SuratPengajuan::findOrFail($id);

            // Update status to read if it's still unread
            if ($suratPengajuan->status === 'pending') {
                $suratPengajuan->update(['status' => 'diproses']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Surat pengajuan telah ditandai sebagai dibaca'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in markAsRead: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai surat pengajuan sebagai dibaca: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SuratPengajuan $suratPengajuan)
    {
        Gate::authorize('surat.edit');

        $suratPengajuan->load('penduduk');

        $suratTypes = [
            'sku' => 'Surat Keterangan Usaha',
            'sktm_dewasa' => 'Surat Keterangan Tidak Mampu (Dewasa)',
            'sktm_anak' => 'Surat Keterangan Tidak Mampu (Anak)',
            'domisili' => 'Surat Keterangan Domisili',
            'kelahiran' => 'Surat Keterangan Kelahiran',
            'kematian' => 'Surat Keterangan Kematian',
            'pindah' => 'Surat Keterangan Pindah'
        ];

        return view('admin.surat-pengajuan.edit', compact('suratPengajuan', 'suratTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SuratPengajuan $suratPengajuan)
    {
        Gate::authorize('surat.edit');

        $validated = $request->validate([
            'jenis_surat' => 'required|string',
            'penduduk_id' => 'required|exists:penduduks,id',
            'keperluan' => 'nullable|string|max:500',
            'tujuan' => 'nullable|string|max:255',
            'tanggal_surat' => 'required|date',
            'keterangan_tambahan' => 'nullable|string|max:1000',
            'data_tambahan' => 'nullable|array',
            'penandatangan' => 'nullable|in:kepala_desa,sekretaris_desa'
        ]);

        try {
            DB::beginTransaction();

            $suratPengajuan->update([
                ...$validated,
                'data_tambahan' => $validated['data_tambahan'] ?? [],
            ]);

            // If types changed, maybe regenerate number? 
            if ($suratPengajuan->wasChanged('jenis_surat')) {
                 $suratPengajuan->update([
                    'nomor_surat' => $this->generateNomorSurat($validated['jenis_surat'])
                 ]);
            }

            DB::commit();

            return redirect()->route('admin.surat-pengajuan.index')
                           ->with('success', 'Surat pengajuan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                           ->withInput();
        }
    }
}

