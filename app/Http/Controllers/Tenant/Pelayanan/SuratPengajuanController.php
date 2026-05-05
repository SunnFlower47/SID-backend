<?php

namespace App\Http\Controllers\Tenant\Pelayanan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratPengajuan;
use App\Models\Penduduk;
use App\Models\DesaSetting;
use App\Services\SuratService;
use App\Models\PendudukDomisili;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;

class SuratPengajuanController extends Controller
{
    protected $suratService;

    public function __construct(SuratService $suratService)
    {
        $this->suratService = $suratService;
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

        return Inertia::render('Tenant/SuratPengajuan/Index', [
            'pengajuans' => $pengajuans,
            'statusList' => $statusList,
            'suratTypes' => $suratTypes,
            'filters' => $request->all(['status', 'jenis_surat', 'search'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suratTypes = \App\Models\SuratType::where('is_active', true)->orderBy('nama')->get();
        
        return Inertia::render('Tenant/SuratPengajuan/Create', [
            'suratTypes' => $suratTypes,
            'wilayah' => [
                'dusun' => \App\Models\Dusun::all(),
                'rw' => \App\Models\Rw::all(),
                'rt' => \App\Models\Rt::all(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('pelayanan_informasi');

        $validated = $request->validate([
            'jenis_surat' => 'required|string',
            'penduduk_id' => 'nullable|exists:penduduks,id',
            'keperluan' => 'nullable|string|max:500',
            'tujuan' => 'nullable|string|max:255',
            'tanggal_surat' => 'required|date',
            'keterangan_tambahan' => 'nullable|string|max:1000',
            'data_tambahan' => 'nullable|array',
            'penandatangan' => 'nullable|in:kepala_desa,sekretaris_desa'
        ]);

        try {
            $action = app(\App\Actions\Surat\StoreSuratAction::class);
            $result = $action->execute($validated);

            return redirect()->route('admin.surat-pengajuan.index')
                ->with($result['type'], $result['message']);

        } catch (\Exception $e) {
            Log::error('Error storing surat pengajuan: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update status pengajuan surat
     */
    public function updateStatus(Request $request, SuratPengajuan $suratPengajuan)
    {
        Gate::authorize('pelayanan_informasi');

        $validated = $request->validate([
            'status' => 'required|in:pending,diproses,selesai,ditolak',
            'keterangan_tambahan' => 'nullable|string|max:1000'
        ]);

        try {
            $updateData = [
                'status' => $validated['status'],
                'keterangan_tambahan' => $validated['keterangan_tambahan'] ?? $suratPengajuan->keterangan_tambahan,
            ];

            if ($validated['status'] === 'selesai' || $validated['status'] === 'diproses') {
                $updateData['approved_at'] = now();
                $updateData['approved_by'] = auth()->id();
            }

            if ($validated['status'] === 'selesai') {
                $updateData['completed_at'] = now();
            }

            $suratPengajuan->update($updateData);

            return redirect()->back()->with('success', 'Status pengajuan berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    /**
     * Search residents for surat creation
     */
    public function searchPenduduk(Request $request)
    {
        $search = $request->get('q');
        
        if (!$search || strlen($search) < 3) {
            return response()->json([]);
        }

        try {
            $penduduks = Penduduk::withWilayah()
                ->where(function($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nik', 'like', "%{$search}%");
                })
                ->limit(10)
                ->get();

            $results = $penduduks->map(function($p) {
                return [
                    'id' => $p->id,
                    'nik' => $p->nik,
                    'nama' => $p->nama,
                    'alamat' => $p->alamat,
                    'rt' => $p->rt_label ?? '-',
                    'rw' => $p->rw_label ?? '-',
                    'dusun' => $p->dusun_label ?? '-',
                    'jenis_kelamin' => $p->jenis_kelamin,
                    'tempat_lahir' => $p->tempat_lahir,
                    'tanggal_lahir' => $p->tanggal_lahir?->toDateString(),
                    'agama' => $p->agama,
                    'pekerjaan' => $p->pekerjaan,
                    'status_perkawinan' => $p->status_perkawinan,
                ];
            });

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SuratPengajuan $suratPengajuan)
    {
        $suratPengajuan->load(['penduduk', 'admin']);

        $statusList = [
            'pending' => 'Menunggu Persetujuan',
            'diproses' => 'Diproses',
            'ditolak' => 'Ditolak',
            'selesai' => 'Selesai'
        ];

        return Inertia::render('Tenant/SuratPengajuan/Show', [
            'suratPengajuan' => $suratPengajuan,
            'statusList' => $statusList
        ]);
    }


    /**
     * Generate PDF for surat
     */
    public function generatePdf(SuratPengajuan $suratPengajuan)
    {
        Gate::authorize('pelayanan_informasi');

        $suratPengajuan->load(['penduduk.kartuKeluarga']);
        $desaSettings = \App\Models\DesaSetting::getDesaInfo();

        // Prepare signer data
        $penandatangan = $suratPengajuan->penandatangan ?? 'kepala_desa';
        $signerData = ($penandatangan === 'sekretaris_desa')
            ? DesaSetting::getSekretarisInfo()
            : DesaSetting::getKepalaDesaInfo();

        // Get surat type and code
        $suratType = \App\Models\SuratType::find($suratPengajuan->jenis_surat);
        $kode_surat = $suratType ? $suratType->kode : 'SK';
        $kode_desa = \App\Models\DesaSetting::getValue('kode_desa', '2001');
        
        // Extract numeric part from nomor_surat
        $parts = explode('/', $suratPengajuan->nomor_surat);
        $nomor_urut = '......';
        if (count($parts) >= 2) {
             if (is_numeric($parts[0])) {
                 $nomor_urut = $parts[0];
             } else {
                 $nomor_urut = $parts[1] ?? '......';
             }
        }
        
        // 1. Data Dasar
        $data = [
            'desa' => $desaSettings['nama_desa'] ?? 'Cibatu',
            'kecamatan' => $desaSettings['kecamatan'] ?? 'Cisaat',
            'kabupaten' => $desaSettings['kabupaten'] ?? 'Sukabumi',
            'provinsi' => $desaSettings['provinsi'] ?? 'Jawa Barat',
            'nama_desa' => $desaSettings['nama_desa'] ?? 'Cibatu',
            'nama_kecamatan' => $desaSettings['kecamatan'] ?? 'Cisaat',
            'nama_kabupaten' => $desaSettings['kabupaten'] ?? 'Sukabumi',
            
            'pengajuan' => $suratPengajuan,
            'penduduk' => $suratPengajuan->penduduk,
            'desa_info' => $desaSettings,
            'kepala_desa' => $signerData,
            'is_sekdes' => ($penandatangan === 'sekretaris_desa'),
            
            'tanggal_surat' => $suratPengajuan->tanggal_surat, 
            'tanggal_lahir' => $suratPengajuan->penduduk->tanggal_lahir,
            
            'nomor_surat' => $suratPengajuan->nomor_surat,
            'keperluan' => $suratPengajuan->keperluan,
            'tujuan' => $suratPengajuan->tujuan,
            'keterangan_tambahan' => $suratPengajuan->keterangan_tambahan,
            'alamat_desa' => \App\Models\DesaSetting::getValue('alamat_lengkap', 'Jl. Cibatu Km. 15, Desa Cibatu'),
            'jenis_kelamin' => $suratPengajuan->penduduk->jenis_kelamin === 'L' ? 'Laki-laki' : ($suratPengajuan->penduduk->jenis_kelamin === 'P' ? 'Perempuan' : $suratPengajuan->penduduk->jenis_kelamin),
            'bulan_romawi' => \App\Models\DesaSetting::intToRoman(\Carbon\Carbon::parse($suratPengajuan->tanggal_surat)->format('n')),
            'tahun_surat' => \Carbon\Carbon::parse($suratPengajuan->tanggal_surat)->format('Y'),
        ];

        // 2. Auto-Map Data Penduduk
        foreach ($suratPengajuan->penduduk->toArray() as $key => $value) {
            if ($key === 'rt_id') $data['rt'] = $suratPengajuan->penduduk->rt_label ?? $value;
            elseif ($key === 'rw_id') $data['rw'] = $suratPengajuan->penduduk->rw_label ?? $value;
            elseif ($key === 'dusun_id') $data['dusun'] = $suratPengajuan->penduduk->dusun_label ?? $value;
            else $data[$key] = $value;
        }

        // 3. Khusus Word: Pakai format Indonesia
        $suratType = \App\Models\SuratType::find($suratPengajuan->jenis_surat);
        if ($suratType && $suratType->file_template) {
            $data['tanggal_surat'] = \Carbon\Carbon::parse($suratPengajuan->tanggal_surat)->isoFormat('D MMMM Y');
            $data['tanggal_lahir'] = $suratPengajuan->penduduk->tanggal_lahir?->isoFormat('D MMMM Y');
        }

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
            $suratType = \App\Models\SuratType::find($suratPengajuan->jenis_surat);
            
            // 1. CEK STATUS AKTIF
            if (!$suratType || !$suratType->is_active) {
                return redirect()->back()->with('error', 'Tipe surat ini sedang dinonaktifkan. Silakan aktifkan di Manajemen Tipe Surat.');
            }

            // 2. CEK APAKAH ADA TEMPLATE WORD (.docx)
            if ($suratType->file_template) {
                $filename = str_replace(['/', '\\'], '-', $suratPengajuan->nomor_surat) . '.docx';
                $outputPath = $this->suratService->generate(
                    $suratType->file_template, 
                    $data, 
                    $filename,
                    $suratPengajuan->penandatangan ?? 'kepala_desa'
                );
                return response()->download($outputPath);
            }

            // 3. JIKA TIDAK ADA WORD, CEK APAKAH ADA BLADE (PDF)
            $templateName = $this->getTemplateName($suratPengajuan->jenis_surat);
            
            if (!View::exists("surat.templates.{$templateName}")) {
                return redirect()->back()->with('error', 'Template surat (Word/PDF) belum disiapkan untuk jenis ini.');
            }

            $pdf = Pdf::loadView("surat.templates.{$templateName}", $data);
            
            $landscapeTemplates = ['kematian', 'keterangan-domisili'];
            $orientation = in_array($templateName, $landscapeTemplates) ? 'landscape' : 'portrait';
            
            $pdf->setPaper(array(0, 0, 609.4488, 935.433), $orientation); // Ukuran F4

            $filename = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $suratPengajuan->nomor_surat) . '.pdf';

            return $pdf->stream($filename);

        } catch (\Exception $e) {
            Log::error('Error generating surat: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Gagal membuat surat: ' . $e->getMessage());
        }
    }

    /**
     * Preview surat
     */
    public function preview(SuratPengajuan $suratPengajuan)
    {
        Gate::authorize('pelayanan_informasi');

        $suratPengajuan->load(['penduduk.kartuKeluarga']);
        $desaSettings = \App\Models\DesaSetting::getDesaInfo();

        // Prepare data for template
        $penduduk = $suratPengajuan->penduduk;
        $desaInfo = $desaSettings;
        $penandatangan = $suratPengajuan->penandatangan ?? 'kepala_desa';
        $signerData = ($penandatangan === 'sekretaris_desa')
            ? DesaSetting::getSekretarisInfo()
            : DesaSetting::getKepalaDesaInfo();

        // 1. Data Dasar
        $data = [
            'desa' => $desaInfo['nama_desa'] ?? 'Cibatu',
            'kecamatan' => $desaInfo['kecamatan'] ?? 'Cisaat',
            'kabupaten' => $desaInfo['kabupaten'] ?? 'Sukabumi',
            'provinsi' => $desaInfo['provinsi'] ?? 'Jawa Barat',
            'nama_desa' => $desaInfo['nama_desa'] ?? 'Cibatu',
            'nama_kecamatan' => $desaInfo['kecamatan'] ?? 'Cisaat',
            'nama_kabupaten' => $desaInfo['kabupaten'] ?? 'Sukabumi',
            'alamat_desa' => $desaInfo['alamat_lengkap'] ?? '',
            
            'pengajuan' => $suratPengajuan,
            'penduduk' => $penduduk,
            'desa_info' => $desaInfo,
            'kepala_desa' => $signerData,
            'is_sekdes' => ($penandatangan === 'sekretaris_desa'),
            'tanggal_surat' => $suratPengajuan->tanggal_surat,
            'tanggal_lahir' => $penduduk->tanggal_lahir,
            'keperluan' => $suratPengajuan->keperluan,
            'tujuan' => $suratPengajuan->tujuan,
            'keterangan_tambahan' => $suratPengajuan->keterangan_tambahan,
            'data_tambahan' => $suratPengajuan->data_tambahan ?? []
        ];

        // 2. Auto-Map SEMUA data penduduk
        $pendudukArray = $penduduk->toArray();
        foreach ($pendudukArray as $key => $value) {
            if ($key === 'rt_id') $data['rt'] = $penduduk->rt_label ?? $value;
            elseif ($key === 'rw_id') $data['rw'] = $penduduk->rw_label ?? $value;
            elseif ($key === 'dusun_id') $data['dusun'] = $penduduk->dusun_label ?? $value;
            else $data[$key] = $value;
        }

        // 3. Khusus Word: Pakai format Indonesia (Hanya jika preview Word diaktifkan di masa depan)
        // Saat ini preview Blade selalu pakai format mentah.

        // Merge additional data
        if ($suratPengajuan->data_tambahan) {
            $dataTambahan = $suratPengajuan->data_tambahan;
            if (is_string($dataTambahan)) $dataTambahan = json_decode($dataTambahan, true);
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
     * Show the form for editing the specified resource.
     */
    public function edit(SuratPengajuan $suratPengajuan)
    {
        Gate::authorize('pelayanan_informasi');

        $suratPengajuan->load('penduduk');

        $suratTypes = \App\Models\SuratType::where('is_active', true)->orderBy('nama')->get();
        
        return Inertia::render('Tenant/SuratPengajuan/Edit', [
            'suratPengajuan' => $suratPengajuan,
            'suratTypes' => $suratTypes,
            'wilayah' => [
                'dusun' => \App\Models\Dusun::all(),
                'rw' => \App\Models\Rw::all(),
                'rt' => \App\Models\Rt::all(),
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SuratPengajuan $suratPengajuan)
    {
        Gate::authorize('pelayanan_informasi');

        $validated = $request->validate([
            'jenis_surat' => 'required|string',
            'penduduk_id' => 'nullable|exists:penduduks,id',
            'keperluan' => 'nullable|string|max:500',
            'tujuan' => 'nullable|string|max:255',
            'tanggal_surat' => 'required|date',
            'keterangan_tambahan' => 'nullable|string|max:1000',
            'data_tambahan' => 'nullable|array',
            'penandatangan' => 'nullable|in:kepala_desa,sekretaris_desa'
        ]);

        try {
            $action = app(\App\Actions\Surat\UpdateSuratAction::class);
            $result = $action->execute($suratPengajuan, $validated);

            return redirect()->route('admin.surat-pengajuan.index')
                           ->with($result['type'], $result['message']);

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                           ->withInput();
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

