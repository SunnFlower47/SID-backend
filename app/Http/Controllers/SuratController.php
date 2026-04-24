<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use App\Models\Penduduk;
use App\Models\DesaSetting;
use App\Models\Surat;
use App\Models\SuratPengajuan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SuratController extends Controller
{
        public function __construct()
    {
        $this->middleware(['auth', 'can:surat.view']);
    }

    /**
     * Display a listing of available letter types
     */
    public function index()
    {
        Gate::authorize('surat.view');

        $suratTypes = [
            [
                'id' => 'keterangan-domisili',
                'name' => 'Surat Keterangan Domisili',
                'description' => 'Surat keterangan tempat tinggal penduduk',
                'icon' => 'fas fa-home',
                'color' => 'blue'
            ],
            [
                'id' => 'pengantar',
                'name' => 'Surat Pengantar',
                'description' => 'Surat pengantar untuk keperluan administrasi',
                'icon' => 'fas fa-file-alt',
                'color' => 'green'
            ],
            [
                'id' => 'pindah',
                'name' => 'Surat Keterangan Pindah',
                'description' => 'Surat keterangan pindah penduduk',
                'icon' => 'fas fa-walking',
                'color' => 'yellow'
            ],
            [
                'id' => 'kematian',
                'name' => 'Surat Keterangan Kematian',
                'description' => 'Surat keterangan kematian penduduk',
                'icon' => 'fas fa-skull',
                'color' => 'red'
            ],
            [
                'id' => 'kelahiran',
                'name' => 'Surat Keterangan Kelahiran',
                'description' => 'Surat keterangan kelahiran penduduk',
                'icon' => 'fas fa-baby',
                'color' => 'purple'
            ],
            [
                'id' => 'tidak-mampu-dewasa',
                'name' => 'Surat Keterangan Tidak Mampu (Dewasa)',
                'description' => 'Surat keterangan tidak mampu untuk orang dewasa',
                'icon' => 'fas fa-hand-holding-heart',
                'color' => 'indigo'
            ],
            [
                'id' => 'tidak-mampu-anak',
                'name' => 'Surat Keterangan Tidak Mampu (Anak)',
                'description' => 'Surat keterangan tidak mampu untuk anak/pelajar',
                'icon' => 'fas fa-child',
                'color' => 'pink'
            ]
        ];

        // Get penduduk data for selection (hanya yang aktif/tidak soft deleted)
        $penduduks = Penduduk::whereNull('deleted_at')
            ->orderBy('nama')
            ->get();

        // Get statistics data
        $stats = $this->getSuratStatistics();

        return View::make('surat.index', compact('suratTypes', 'penduduks', 'stats'));
    }


    /**
     * Generate and download PDF letter
     */
    public function generate(Request $request, $type)
    {
        Gate::authorize('surat.create');

        $validated = $request->validate([
            'penduduk_id' => 'required|exists:penduduks,id',
            'keperluan' => 'nullable|string|max:500',
            'tujuan' => 'nullable|string|max:255',
            'tanggal_surat' => 'nullable|date',
            'keterangan_tambahan' => 'nullable|string|max:1000'
        ]);

        $penduduk = Penduduk::findOrFail($validated['penduduk_id']);

        $data = $this->prepareSuratData($penduduk, $validated, $type);
        $filename = $this->generateFilename($type, $penduduk);

        return $this->generatePdf($type, $data, $filename);
    }

    /**
     * Preview letter before download
     */
    public function preview(Request $request, $type)
    {
        Gate::authorize('surat.view');

        $validated = $request->validate([
            'penduduk_id' => 'required|exists:penduduks,id',
            'keperluan' => 'nullable|string|max:500',
            'tujuan' => 'nullable|string|max:255',
            'tanggal_surat' => 'nullable|date',
            'keterangan_tambahan' => 'nullable|string|max:1000',
            'penandatangan' => 'nullable|in:kepala_desa,sekretaris_desa'
        ]);

        $penduduk = Penduduk::findOrFail($validated['penduduk_id']);
        $data = $this->prepareSuratData($penduduk, $validated, $type);
        $view = "surat.templates.{$type}";

        return View::make($view, $data);
    }

    /**
     * Store surat and save to history
     */
    public function store(Request $request, $type)
    {
        Gate::authorize('surat.create');

        $validated = $request->validate([
            'penduduk_id' => 'required|exists:penduduks,id',
            'keperluan' => 'nullable|string|max:500',
            'tujuan' => 'nullable|string|max:255',
            'tanggal_surat' => 'nullable|date',
            'keterangan_tambahan' => 'nullable|string|max:1000',
            'data_tambahan' => 'nullable|string',
            'penandatangan' => 'nullable|in:kepala_desa,sekretaris_desa'
        ]);

        $penduduk = Penduduk::findOrFail($validated['penduduk_id']);

        // Parse data_tambahan JSON
        $dataTambahan = [];
        if (!empty($validated['data_tambahan'])) {
            $dataTambahan = json_decode($validated['data_tambahan'], true) ?? [];
        }

        // Save to surat history
        $surat = Surat::create([
            'penduduk_id' => $penduduk->id,
            'jenis_surat' => $type,
            'keperluan' => $validated['keperluan'] ?? '',
            'tujuan' => $validated['tujuan'] ?? '',
            'tanggal_surat' => $validated['tanggal_surat'] ? Carbon::parse($validated['tanggal_surat']) : Carbon::now(),
            'keterangan_tambahan' => $validated['keterangan_tambahan'] ?? '',
            'data_tambahan' => $dataTambahan,
            'status' => 'selesai',
            'nomor_surat' => $this->generateNomorSurat($type),
            'created_by' => Auth::id(),
            'penandatangan' => $validated['penandatangan'] ?? 'kepala_desa',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Surat berhasil disimpan ke histori',
            'surat_id' => $surat->id
        ]);
    }

    /**
     * Prepare surat data with settings
     */
    private function prepareSuratData($penduduk, $validated, $type, $dataTambahan = [])
    {
        $penandatangan = $validated['penandatangan'] ?? 'kepala_desa';
        
        $signerData = ($penandatangan === 'sekretaris_desa') 
            ? DesaSetting::getSekretarisInfo() 
            : DesaSetting::getKepalaDesaInfo();

        return [
            'penduduk' => $penduduk,
            'keperluan' => $validated['keperluan'] ?? '',
            'tujuan' => $validated['tujuan'] ?? '',
            'tanggal_surat' => $validated['tanggal_surat'] ? Carbon::parse($validated['tanggal_surat']) : Carbon::now(),
            'keterangan_tambahan' => $validated['keterangan_tambahan'] ?? '',
            'desa' => DesaSetting::getDesaInfo(),
            'kepala_desa' => $signerData, // Pass the selected signer as 'kepala_desa' (standard variable name in view)
            'penandatangan_raw' => $signerData, // Also pass as raw for clarity if new views use it
            'is_sekdes' => ($penandatangan === 'sekretaris_desa'), // Flag for "a.n"
            'sekretaris' => DesaSetting::getSekretarisInfo(), // Pass sekdes explicitly if needed
            'logos' => DesaSetting::getLogos(),
            'nomor_surat' => $this->generateNomorSurat($type),
            'template_settings' => [
                'header' => DesaSetting::getValue('template_header', 'PEMERINTAH KABUPATEN GARUT\nKECAMATAN CIBATU\nDESA CIBATU'),
                'footer' => DesaSetting::getValue('template_footer', 'Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.')
            ]
        ] + (is_array($dataTambahan) ? $dataTambahan : json_decode($dataTambahan, true) ?? []); // Merge data tambahan dari JSON
    }

    /**
     * Generate PDF based on letter type
     */
    private function generatePdf($type, $data, $filename)
    {
        $view = "surat.templates.{$type}";

        $pdf = Pdf::loadView($view, $data);

        if ($type === 'kematian') {
             // F4 Landscape for Kematian
             $pdf->setPaper(array(0, 0, 609.4488, 935.433), 'landscape');
        } else {
             $pdf->setPaper('A4', 'portrait');
        }

        $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial'
            ]);

        return $pdf->download($filename);
    }

    /**
     * Generate filename for PDF
     */
    private function generateFilename($type, $penduduk)
    {
        $date = Carbon::now()->format('Y-m-d');
        $typeNames = [
            'keterangan-domisili' => 'Keterangan_Domisili',
            'pengantar' => 'Surat_Pengantar',
            'pindah' => 'Keterangan_Pindah',
            'kematian' => 'Keterangan_Kematian',
            'kelahiran' => 'Keterangan_Kelahiran',
            'tidak-mampu' => 'Keterangan_Tidak_Mampu'
        ];

        $typeName = $typeNames[$type] ?? 'Surat';
        $nama = str_replace(' ', '_', $penduduk->nama);

        return "{$typeName}_{$nama}_{$date}.pdf";
    }

    /**
     * Generate nomor surat
     */
    private function generateNomorSurat($type)
    {
        $suratSettings = DesaSetting::getSuratSettings();
        $kodeSurat = $suratSettings["kode_surat_{$type}"] ?? 'SK';

        return DesaSetting::generateNomorSurat($kodeSurat);
    }

    /**
     * Get surat statistics
     */
    private function getSuratStatistics()
    {
        $today = Carbon::today();
        $startOfWeek = $today->copy()->startOfWeek();
        $startOfMonth = $today->copy()->startOfMonth();

        return [
            'total' => Surat::count(),
            'hari_ini' => Surat::whereDate('created_at', $today)->count(),
            'minggu_ini' => Surat::whereBetween('created_at', [$startOfWeek, $today->endOfDay()])->count(),
            'bulan_ini' => Surat::whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->count(),
        ];
    }

    /**
     * Get surat statistics API
     */
    public function statistics()
    {
        Gate::authorize('surat.view');

        $stats = $this->getSuratStatistics();

        return response()->json($stats);
    }

    /**
     * Display surat history
     */
    public function history(Request $request)
    {
        Gate::authorize('surat.view');

        // Get surats from direct creation
        $suratQuery = Surat::with(['penduduk', 'creator']);

        // Get approved surat pengajuans
        $pengajuanQuery = SuratPengajuan::with(['penduduk', 'admin'])
            ->where('status', 'approved');

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

        // Apply jenis surat filter to both queries
        if ($request->filled('jenis_surat')) {
            $suratQuery->where('jenis_surat', $request->jenis_surat);
            $pengajuanQuery->where('jenis_surat', $request->jenis_surat);
        }

        // Apply year filter to both queries
        if ($request->filled('tahun')) {
            $suratQuery->whereYear('created_at', $request->tahun);
            $pengajuanQuery->whereYear('approved_at', $request->tahun);
        }

        // Apply date range filter to both queries
        if ($request->filled('date_from')) {
            $suratQuery->whereDate('created_at', '>=', $request->date_from);
            $pengajuanQuery->whereDate('approved_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $suratQuery->whereDate('created_at', '<=', $request->date_to);
            $pengajuanQuery->whereDate('approved_at', '<=', $request->date_to);
        }

        // Get results from both queries
        $surats = $suratQuery->get();
        $pengajuans = $pengajuanQuery->get();

        // Transform pengajuans to match surats structure
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

        // Transform surats to match structure
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
                'status' => $surat->status
            ];
        });

        // Combine and sort by created_at
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
                'pageName' => 'page',
            ]
        );

        return View::make('surat.history', compact('paginatedSurats'));
    }

    /**
     * Show surat detail
     */
    public function show($id)
    {
        Gate::authorize('surat.view');

        $surat = Surat::with(['penduduk', 'creator'])->findOrFail($id);

        // Prepare data for template
        $dataTambahan = $surat->data_tambahan ?
            (is_array($surat->data_tambahan) ? $surat->data_tambahan : json_decode($surat->data_tambahan, true) ?? []) : [];

        $data = $this->prepareSuratData($surat->penduduk, [
            'keperluan' => $surat->keperluan,
            'tujuan' => $surat->tujuan,
            'tanggal_surat' => $surat->tanggal_surat,
            'keterangan_tambahan' => $surat->keterangan_tambahan,
            'penandatangan' => $surat->penandatangan ?? 'kepala_desa'
        ], $surat->jenis_surat, $dataTambahan);

        // Add surat data
        $data['surat'] = $surat;
        $data['is_print'] = request('print') == '1';

        return View::make("surat.templates.{$surat->jenis_surat}", $data);
    }

    /**
     * Download surat PDF
     */
    public function download($id)
    {
        Gate::authorize('surat.view');

        $surat = Surat::with(['penduduk', 'creator'])->findOrFail($id);

        // Prepare data for template
        $dataTambahan = $surat->data_tambahan ?
            (is_array($surat->data_tambahan) ? $surat->data_tambahan : json_decode($surat->data_tambahan, true) ?? []) : [];

        $data = $this->prepareSuratData($surat->penduduk, [
            'keperluan' => $surat->keperluan,
            'tujuan' => $surat->tujuan,
            'tanggal_surat' => $surat->tanggal_surat,
            'keterangan_tambahan' => $surat->keterangan_tambahan,
            'penandatangan' => $surat->penandatangan ?? 'kepala_desa'
        ], $surat->jenis_surat, $dataTambahan);

        // Add surat data
        $data['surat'] = $surat;

        $filename = $this->generateFilename($surat->jenis_surat, $surat->penduduk);
        return $this->generatePdf($surat->jenis_surat, $data, $filename);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        Gate::authorize('surat.edit');

        $surat = Surat::with('penduduk')->findOrFail($id);
        
        // Similar to index, pass necessary data
        $suratTypes = [
            [
                'id' => 'keterangan-domisili',
                'name' => 'Surat Keterangan Domisili',
            ],
            [
                'id' => 'pengantar',
                'name' => 'Surat Pengantar',
            ],
            [
                'id' => 'pindah',
                'name' => 'Surat Keterangan Pindah',
            ],
            [
                'id' => 'kematian',
                'name' => 'Surat Keterangan Kematian',
            ],
            [
                'id' => 'kelahiran',
                'name' => 'Surat Keterangan Kelahiran',
            ],
            [
                'id' => 'tidak-mampu-dewasa',
                'name' => 'Surat Keterangan Tidak Mampu (Dewasa)',
            ],
            [
                'id' => 'tidak-mampu-anak',
                'name' => 'Surat Keterangan Tidak Mampu (Anak)',
            ]
        ];

        return View::make('surat.edit', compact('surat', 'suratTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        Gate::authorize('surat.edit');

        $surat = Surat::findOrFail($id);

        $validated = $request->validate([
            'keperluan' => 'nullable|string|max:500',
            'tujuan' => 'nullable|string|max:255',
            'tanggal_surat' => 'nullable|date',
            'keterangan_tambahan' => 'nullable|string|max:1000',
            'data_tambahan' => 'nullable|string',
            'penandatangan' => 'nullable|in:kepala_desa,sekretaris_desa'
        ]);

        // Parse data_tambahan JSON
        $dataTambahan = [];
        if (!empty($validated['data_tambahan'])) {
            $dataTambahan = json_decode($validated['data_tambahan'], true) ?? [];
        }

        $surat->update([
            'keperluan' => $validated['keperluan'] ?? '',
            'tujuan' => $validated['tujuan'] ?? '',
            'tanggal_surat' => $validated['tanggal_surat'] ? Carbon::parse($validated['tanggal_surat']) : Carbon::now(),
            'keterangan_tambahan' => $validated['keterangan_tambahan'] ?? '',
            'data_tambahan' => $dataTambahan,
            'penandatangan' => $validated['penandatangan'] ?? 'kepala_desa',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Surat berhasil diperbarui',
            'surat_id' => $surat->id
        ]);
    }

    /**
     * Delete surat (super admin only)
     */
    public function destroy($id)
    {
        Gate::authorize('surat.delete');

        try {
            $surat = Surat::findOrFail($id);
            $surat->delete();

            return redirect()->route('surat.history')
                           ->with('success', 'Surat berhasil dihapus');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
