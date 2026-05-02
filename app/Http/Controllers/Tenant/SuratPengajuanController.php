<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratPengajuan;
use App\Models\Penduduk;
use App\Models\Surat;
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
        $this->middleware(['auth', 'can:pelayanan_informasi']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SuratPengajuan::with(['penduduk' => function($q) {
            $q->withTrashed();
        }, 'admin']);

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
            'diproses' => 'Diproses',
            'ditolak' => 'Ditolak',
            'selesai' => 'Selesai'
        ];

        $suratTypes = \App\Models\SuratType::where('is_active', true)->get();

        return view('surat-pengajuan.index', compact(
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
        $suratTypes = \App\Models\SuratType::where('is_active', true)->orderBy('nama')->get();
        $dynamicTypes = \App\Models\SuratType::where('is_active', true)
            ->whereNotNull('form_json')
            ->pluck('id')
            ->toArray();

        return view('surat-pengajuan.create', compact('penduduks', 'suratTypes', 'dynamicTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('pelayanan_informasi');

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
            $status = 'selesai'; // Direct completion for admin created letters
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

        return view('surat-pengajuan.show', compact('suratPengajuan'));
    }

    /**
     * Update status of surat pengajuan
     */
    public function updateStatus(Request $request, SuratPengajuan $suratPengajuan)
    {
        Gate::authorize('pelayanan_informasi');

        $validated = $request->validate([
            'status' => 'required|in:pending,diproses,selesai,ditolak',
            'keterangan' => 'nullable|string|max:1000'
        ]);

        $updateData = [
            'status' => $validated['status'],
            'keterangan_admin' => $validated['keterangan'] ?? $suratPengajuan->keterangan_admin,
        ];

        // Update timestamps based on status
        if ($validated['status'] === 'selesai' || $validated['status'] === 'diproses') {
            $updateData['approved_at'] = now();
        }

        if ($validated['status'] === 'selesai') {
            $updateData['completed_at'] = now();
        }

        $suratPengajuan->update($updateData);

        return redirect()->back()->with('success', 'Status surat berhasil diperbarui');
    }

    /**
     * Generate PDF for surat
     */
    public function generatePdf(SuratPengajuan $suratPengajuan)
    {
        Gate::authorize('pelayanan_informasi');

        $suratPengajuan->load(['penduduk.kartuKeluarga']);

        // Get desa settings
        $desaSettings = DesaSetting::getDesaInfo();

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
            'desa' => $desaSettings,
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
            $templateName = $this->getTemplateName($suratPengajuan->jenis_surat);
            $pdf = Pdf::loadView("surat.templates.{$templateName}", $data);
            
            // Tentukan orientasi berdasarkan jenis surat
            // Hanya Kematian dan Domisili yang Landscape, sisanya Portrait
            $landscapeTemplates = ['kematian', 'keterangan-domisili'];
            $orientation = in_array($templateName, $landscapeTemplates) ? 'landscape' : 'portrait';
            
            $pdf->setPaper(array(0, 0, 609.4488, 935.433), $orientation); // Ukuran F4

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
        Gate::authorize('pelayanan_informasi');

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
    private function getTemplateName($suratType)
    {
        // Cari data di database berdasarkan ID jenis surat
        $type = \App\Models\SuratType::find($suratType);

        // Jika ada di database dan punya template_code, gunakan itu
        if ($type && $type->template_code) {
            return $type->template_code;
        }

        // Jika tidak ditemukan atau tidak punya template (Surat Lainnya/Manual)
        return null;
    }

    /**
     * Generate nomor surat
     */
    private function generateNomorSurat($suratType)
    {
        $type = \App\Models\SuratType::find($suratType);
        $kodeSurat = $type ? $type->kode : 'SK';

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
        Gate::authorize('pelayanan_informasi');

        $suratPengajuan->load('penduduk');

        $suratTypes = \App\Models\SuratType::where('is_active', true)->orderBy('nama')->get();
        $dynamicTypes = \App\Models\SuratType::where('is_active', true)
            ->whereNotNull('form_json')
            ->pluck('id')
            ->toArray();

        return view('surat-pengajuan.edit', compact('suratPengajuan', 'suratTypes', 'dynamicTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SuratPengajuan $suratPengajuan)
    {
        Gate::authorize('pelayanan_informasi');

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

    /**
     * Display unified history of letters (both from pengajuan and manual creation)
     */
    public function history(Request $request)
    {
        Gate::authorize('pelayanan_informasi');

        // Get surats from direct creation (legacy)
        $suratQuery = Surat::with(['penduduk', 'creator']);

        // Get approved surat pengajuans
        $pengajuanQuery = SuratPengajuan::with(['penduduk', 'admin'])
            ->whereIn('status', ['selesai', 'diproses']);

        // Apply search filter to both queries
        if ($request->filled('search')) {
            $search = $request->search;

            $suratQuery->where(function($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhereHas('penduduk', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%");
                  });
            });

            $pengajuanQuery->where(function($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhereHas('penduduk', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%");
                  });
            });
        }

        // Apply filters
        if ($request->filled('jenis_surat')) {
            $suratQuery->where('jenis_surat', $request->jenis_surat);
            $pengajuanQuery->where('jenis_surat', $request->jenis_surat);
        }

        if ($request->filled('tahun')) {
            $suratQuery->whereYear('created_at', $request->tahun);
            $pengajuanQuery->whereYear('created_at', $request->tahun);
        }

        if ($request->filled('date_from')) {
            $suratQuery->whereDate('created_at', '>=', $request->date_from);
            $pengajuanQuery->whereDate('created_at', '>=', $request->date_from);
        }

        // Get results from both queries
        $surats = $suratQuery->get();
        $pengajuans = $pengajuanQuery->get();

        // Transform pengajuans to match surats structure for unified list
        $transformedPengajuans = $pengajuans->map(function($pengajuan) {
            return (object) [
                'id' => 'pengajuan_' . $pengajuan->id,
                'nomor_surat' => $pengajuan->nomor_surat,
                'jenis_surat' => $pengajuan->jenis_surat,
                'penduduk' => $pengajuan->penduduk,
                'created_at' => $pengajuan->approved_at ?? $pengajuan->created_at,
                'creator' => $pengajuan->admin,
                'source' => 'pengajuan',
                'pengajuan_id' => $pengajuan->id,
                'keperluan' => $pengajuan->keperluan,
                'tujuan' => $pengajuan->tujuan,
                'tanggal_surat' => $pengajuan->tanggal_surat,
                'keterangan_tambahan' => $pengajuan->keterangan_tambahan,
                'data_tambahan' => $pengajuan->data_tambahan,
                'status' => $pengajuan->status
            ];
        });

        // Transform legacy surats
        $transformedSurats = $surats->map(function($surat) {
            return (object) [
                'id' => 'surat_' . $surat->id,
                'nomor_surat' => $surat->nomor_surat,
                'jenis_surat' => $surat->jenis_surat,
                'penduduk' => $surat->penduduk,
                'created_at' => $surat->created_at,
                'creator' => $surat->creator,
                'source' => 'surat',
                'surat_id' => $surat->id,
                'keperluan' => $surat->keperluan,
                'tujuan' => $surat->tujuan,
                'tanggal_surat' => $surat->tanggal_surat,
                'keterangan_tambahan' => $surat->keterangan_tambahan,
                'data_tambahan' => $surat->data_tambahan,
                'status' => 'selesai'
            ];
        });

        // Combine and sort
        $allSurats = $transformedSurats->concat($transformedPengajuans)
            ->sortByDesc('created_at');

        // Manual pagination
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $items = $allSurats->slice($offset, $perPage)->values();

        $paginatedSurats = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $allSurats->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('surat-pengajuan.history', compact('paginatedSurats'));
    }

    /**
     * Download legacy surat (from old surats table)
     */
    public function downloadLegacy($id)
    {
        Gate::authorize('pelayanan_informasi');

        $surat = Surat::with(['penduduk', 'creator'])->findOrFail($id);
        
        $data = [
            'penduduk' => $surat->penduduk,
            'desa_info' => DesaSetting::getDesaInfo(),
            'kepala_desa' => ($surat->penandatangan === 'sekretaris_desa') 
                ? DesaSetting::getSekretarisInfo() 
                : DesaSetting::getKepalaDesaInfo(),
            'is_sekdes' => ($surat->penandatangan === 'sekretaris_desa'),
            'tanggal_surat' => $surat->tanggal_surat,
            'nomor_surat' => $surat->nomor_surat,
            'keperluan' => $surat->keperluan,
            'tujuan' => $surat->tujuan,
            'keterangan_tambahan' => $surat->keterangan_tambahan,
            'data_tambahan' => $surat->data_tambahan ?? []
        ];

        // Merge data tambahan
        if ($surat->data_tambahan) {
            $dataTambahan = $surat->data_tambahan;
            if (is_string($dataTambahan)) $dataTambahan = json_decode($dataTambahan, true);
            if (is_array($dataTambahan)) {
                foreach ($dataTambahan as $key => $value) {
                    $data[$key] = $value;
                }
            }
        }

        $pdf = Pdf::loadView("surat.templates.{$this->getTemplateName($surat->jenis_surat)}", $data);
        
        if ($surat->jenis_surat === 'kematian') {
            $pdf->setPaper(array(0, 0, 609.4488, 935.433), 'landscape');
        } else {
            $pdf->setPaper('A4', 'portrait');
        }

        $filename = str_replace(['/', '\\'], '-', $surat->nomor_surat) . '.pdf';

        if (request('preview') == '1' || request('print') == '1') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    /**
     * Delete legacy surat (from old surats table)
     */
    public function destroyLegacy($id)
    {
        Gate::authorize('pelayanan_informasi');

        try {
            $surat = Surat::findOrFail($id);
            $surat->delete();

            return redirect()->route('admin.surat-pengajuan.history')
                           ->with('success', 'Surat berhasil dihapus');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Delete surat pengajuan
     */
    public function destroy($id)
    {
        Gate::authorize('pelayanan_informasi');

        try {
            $pengajuan = SuratPengajuan::findOrFail($id);
            $pengajuan->delete();

            return redirect()->route('admin.surat-pengajuan.index')
                           ->with('success', 'Pengajuan surat berhasil dihapus');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

