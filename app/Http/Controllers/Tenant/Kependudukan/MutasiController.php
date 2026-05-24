<?php

namespace App\Http\Controllers\Tenant\Kependudukan;

use App\Http\Controllers\Controller;

use App\Models\Mutasi;
use App\Models\Penduduk;
use App\Models\DesaSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Services\Kependudukan\MutasiService;
use Inertia\Inertia;

class MutasiController extends Controller
{
    protected $mutasiService;

    public function __construct(MutasiService $mutasiService)
    {
        $this->mutasiService = $mutasiService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('mutasi.view');

        $query = Mutasi::with(['penduduk' => function($q) {
            $q->withTrashed();
        }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($main) use ($search) {
                $main->whereHas('penduduk', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nik', 'like', "%{$search}%");
                })->orWhere('alasan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jenis_mutasi')) {
            $query->where('jenis_mutasi', $request->jenis_mutasi);
        }

        if ($request->filled('kategori_mutasi')) {
            $query->where('kategori_mutasi', $request->kategori_mutasi);
        }

        $mutasis = $query->orderBy('tanggal_mutasi', 'desc')->paginate(15);

        // Optimization: Use once() to avoid redundant service calls in deferred props
        return Inertia::render('Tenant/Mutasi/Index', [
            'mutasis' => Inertia::defer(fn() => $mutasis),
            'filters' => $request->only(['search', 'jenis_mutasi', 'kategori_mutasi']),
            'stats' => Inertia::defer(fn() => once(fn() => app(\App\Services\Kependudukan\VillageStatisticsService::class)->getDashboardStats())['mutasi'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('mutasi.create');

        return Inertia::render('Tenant/Mutasi/Create', [
            'wilayahTree' => $this->mutasiService->getWilayahTree()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\Mutasi\StoreMutasiRequest $request)
    {
        Gate::authorize('mutasi.create');

        $jenisMutasi = $request->input('jenis_mutasi');

        try {
            DB::beginTransaction();

            switch ($jenisMutasi) {
                case 'kelahiran':
                    $this->mutasiService->handleKelahiran($request->validated());
                    break;
                case 'kematian':
                    $this->mutasiService->handleKematian($request->validated());
                    break;
                case 'pindah_masuk':
                    $this->mutasiService->handlePindahMasuk($request->validated());
                    break;
                case 'pindah_keluar':
                    $this->mutasiService->handlePindahKeluar($request->validated());
                    break;
                case 'pindah_rt_rw':
                    $this->mutasiService->handlePindahRTRW($request->validated());
                    break;
                case 'pisah_kk':
                    $this->mutasiService->handlePisahKK($request->validated());
                    break;
                default:
                    throw new \Exception('Jenis mutasi tidak valid');
            }

            DB::commit();

            $message = 'Data mutasi berhasil ditambahkan.';
            switch ($jenisMutasi) {
                case 'kelahiran':
                    $message = 'Data kelahiran bayi berhasil disimpan.';
                    break;
                case 'kematian':
                    $message = 'Data kematian berhasil disimpan.';
                    break;
                case 'pindah_masuk':
                    $jiwa = 1 + count($request->input('family_members', []));
                    $message = "Data Pindah Masuk ({$jiwa} jiwa) berhasil disimpan.";
                    break;
                case 'pindah_keluar':
                    $message = 'Data pindah keluar berhasil diproses.';
                    break;
                case 'pindah_rt_rw':
                    $message = 'Seluruh anggota KK berhasil dipindah.';
                    break;
                case 'pisah_kk':
                    $message = 'Data Pisah KK berhasil disimpan.';
                    break;
            }

            if (($request->ajax() || $request->wantsJson()) && !$request->header('X-Inertia')) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect' => route('mutasi.data.index')
                ]);
            }

            return redirect()->route('mutasi.data.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            if (($request->ajax() || $request->wantsJson()) && !$request->header('X-Inertia')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Mutasi $mutasi)
    {
        Gate::authorize('mutasi.view', $mutasi);

        $mutasi->load(['penduduk' => fn($q) => $q->withWilayah()]);
        
        return \Inertia\Inertia::render('Tenant/Mutasi/Show', [
            'mutasi' => $mutasi
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mutasi $mutasi)
    {
        Gate::authorize('mutasi.edit');

        return \Inertia\Inertia::render('Tenant/Mutasi/Edit', [
            'mutasi' => $mutasi->load(['penduduk' => fn($q) => $q->withWilayah()]),
            'masterRwOptions' => $this->mutasiService->getMasterRwOptions(),
            'wilayahTree' => $this->mutasiService->getWilayahTree()
        ]);
    }

    /**
     * Update the specified resource in storage.
     * Validasi dan update conditional berdasarkan jenis_mutasi.
     */
    public function update(Request $request, Mutasi $mutasi)
    {
        Gate::authorize('mutasi.edit');

        // Validation logic remains in Controller for clarity
        $rules = [
            'tanggal_mutasi' => 'required|date',
            'alasan' => 'nullable|string|max:500',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'kategori_mutasi' => 'nullable|string',
            'asal_tujuan' => 'nullable|string',
        ];

        switch ($mutasi->jenis_mutasi) {
            case 'kematian':
                $rules += [
                    'hari_meninggal' => 'required|string',
                    'jam_meninggal' => 'required',
                    'bertempat_di' => 'required|string',
                    'hari_pemakaman' => 'required|string',
                    'tanggal_pemakaman' => 'required|date',
                    'jam_pemakaman' => 'required',
                    'lokasi_pemakaman' => 'required|string',
                ];
                break;
            case 'kelahiran':
                $rules += [
                    'nama_bayi' => 'required|string',
                    'nik_bayi' => 'nullable|string|size:16',
                    'jenis_kelamin_bayi' => 'required|in:LAKI-LAKI,PEREMPUAN',
                    'tanggal_lahir' => 'required|date',
                    'tempat_lahir' => 'required|string',
                    'nkk' => 'required|string|size:16',
                ];
                break;
            case 'pisah_kk':
                $rules += [
                    'anggota_pisah_data' => 'nullable|array',
                    'nkk_baru' => 'nullable|string|size:16',
                    'nkk_existing' => 'nullable|string|size:16',
                ];
                break;
        }

        $validated = $request->validate($rules);

        try {
            $this->mutasiService->updateMutasi($mutasi, $validated, $request->file('dokumen_pendukung'));
            return redirect()->route('mutasi.data.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function cancel(Request $request, Mutasi $mutasi)
    {
        Gate::authorize('mutasi.cancel', $mutasi);

        $wantsJson = ($request->expectsJson() || $request->wantsJson() || $request->ajax()) && !$request->header('X-Inertia');

        try {
            $this->mutasiService->cancelMutasi($mutasi);
            return $wantsJson
                ? response()->json(['success' => true, 'message' => 'Mutasi berhasil dibatalkan.'])
                : redirect()->route('mutasi.data.index')->with('success', 'Mutasi berhasil dibatalkan.');
        } catch (\Exception $e) {
            $message = 'Gagal membatalkan mutasi: ' . $e->getMessage();
            return $wantsJson
                ? response()->json(['success' => false, 'message' => $message], 422)
                : redirect()->route('mutasi.data.index')->with('error', $message);
        }
    }
    
    // Search helper methods
    public function searchKK(Request $request) {
        $search = $request->get('query');
        $kks = \App\Models\KartuKeluarga::withWilayah()
                ->where(function($q) use ($search) {
                    $q->where('nkk', 'like', "%{$search}%")
                      ->orWhere('nama_kepala_keluarga', 'like', "%{$search}%");
                })
                ->limit(10)->get();

        $result = $kks->map(function ($kk) {
            return [
                'nkk' => $kk->nkk,
                'kepala_keluarga' => $kk->nama_kepala_keluarga,
                'nama' => $kk->nama_kepala_keluarga,
                'alamat' => $kk->alamat,
                'rt' => $kk->rt_label,
                'rw' => $kk->rw_label,
                'dusun' => $kk->dusun_label,
                'rt_id' => $kk->rt_id,
                'rw_id' => $kk->rw_id,
                'dusun_id' => $kk->dusun_id,

                'jumlah_anggota' => $kk->anggota_aktif,
            ];
        })->values();
        
        return response()->json($result);
    }

    public function searchPenduduk(Request $request) {
        $search = $request->get('query');
        $penduduks = Penduduk::withWilayah()
                ->select('id', 'nik', 'nama', 'kartu_keluarga_id', 'agama', 'jenis_kelamin', 'tanggal_lahir')
                ->where(function($q) use ($search) {
                    $q->where('nik', 'like', "%{$search}%")
                      ->orWhere('nama', 'like', "%{$search}%");
                })
                ->limit(10)->get()
                ->map(function($p) {
                    $p->rt_label = optional($p->rtMaster)->kode;
                    $p->rw_label = optional($p->rwMaster)->kode;
                    $p->dusun_label = optional($p->dusunMaster)->nama;
                    // Ensure address is also from source of truth
                    $p->alamat = $p->alamat; 
                    return $p;
                });

        return response()->json($penduduks);
    }
    
    public function checkNKKExists(Request $request) {
        $nkk = $request->get('nkk');

        if (!$nkk) {
            return response()->json(['exists' => false, 'data' => null]);
        }

        // 1. Search in Source of Truth (kartu_keluargas table)
        $kk = \App\Models\KartuKeluarga::where('nkk', $nkk)->first();

        if ($kk) {
            return response()->json([
                'exists' => true,
                'data' => [
                    'nkk' => $kk->nkk,
                    'kepala_keluarga' => $kk->nama_kepala_keluarga ?? 'Kepala Keluarga',
                    'alamat' => $kk->alamat,
                    'rt_id' => $kk->rt_id,
                    'rw_id' => $kk->rw_id,
                    'dusun_id' => $kk->dusun_id,
                    'rt' => $kk->rt_label,
                    'rw' => $kk->rw_label,
                    'dusun' => $kk->dusun_label,
                ]
            ]);
        }

        // 2. Fallback to Penduduk table (Legacy/Search mode)
        $pendudukKK = Penduduk::withWilayah()
            ->whereHas('kartuKeluarga', function($q) use ($nkk) {
                $q->where('nkk', $nkk);
            })
            ->whereIn('kedudukan_keluarga', ['KEPALA KELUARGA', 'Kepala Keluarga', 'kepala keluarga'])
            ->first();

        if (!$pendudukKK) {
            return response()->json(['exists' => false, 'data' => null]);
        }

        return response()->json([
            'exists' => true,
            'data' => [
                'nkk' => $pendudukKK->kartuKeluarga->nkk ?? $pendudukKK->nkk,
                'kepala_keluarga' => $pendudukKK->nama,
                'alamat' => $pendudukKK->kartuKeluarga->alamat ?? $pendudukKK->alamat,
                'rt_id' => $pendudukKK->kartuKeluarga->rt_id ?? $pendudukKK->rt_id,
                'rw_id' => $pendudukKK->kartuKeluarga->rw_id ?? $pendudukKK->rw_id,
                'dusun_id' => $pendudukKK->kartuKeluarga->dusun_id ?? $pendudukKK->dusun_id,
                'rt' => $pendudukKK->rt_label,
                'rw' => $pendudukKK->rw_label,
                'dusun' => $pendudukKK->dusun_label,
            ]
        ]);
    }
    
    public function undo(Request $request, Mutasi $mutasi)
    {
        Gate::authorize('mutasi.undo', $mutasi);

        $wantsJson = ($request->expectsJson() || $request->wantsJson() || $request->ajax()) && !$request->header('X-Inertia');

        try {
            $this->mutasiService->undoMutasi($mutasi);
            return $wantsJson
                ? response()->json(['success' => true, 'message' => 'Undo mutasi berhasil.'])
                : redirect()->route('mutasi.data.index')->with('success', 'Undo mutasi berhasil.');
        } catch (\Exception $e) {
            $message = 'Gagal undo mutasi: ' . $e->getMessage();
            return $wantsJson
                ? response()->json(['success' => false, 'message' => $message], 500)
                : redirect()->route('mutasi.data.index')->with('error', $message);
        }
    }

    public function printSuratKematian(Mutasi $mutasi)
    {
        Gate::authorize('mutasi.print', $mutasi);

        if ($mutasi->jenis_mutasi !== 'kematian') {
            return redirect()->back()->with('error', 'Mutasi ini bukan data kematian.');
        }

        $mutasi->load(['penduduk' => function($q) {
            $q->withTrashed();
        }]);

        $penduduk = $mutasi->penduduk;
        
        if (!$penduduk) {
             return redirect()->back()->with('error', 'Data penduduk tidak ditemukan.');
        }

        $kadesInfo = DesaSetting::getKepalaDesaInfo();
        $data = [
            'mutasi' => $mutasi,
            'penduduk' => $penduduk,
            'nomor_surat' => $this->generateNomorSuratKematian(),   
            'kepala_desa' => $kadesInfo,
            'desa' => DesaSetting::getDesaInfo(),
            'logos' => DesaSetting::getLogos(),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('surat.templates.kematian', $data);
        $pdf->setPaper(array(0, 0, 609.4488, 935.433), 'landscape'); 
        
        return $pdf->stream('Surat_Kematian_' . $penduduk->nik . '.pdf');
    }

    private function generateNomorSuratKematian()
    {
        $bulan = date('m');
        $tahun = date('Y');
        $count = Mutasi::where('jenis_mutasi', 'kematian')
                ->whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan)
                ->count() + 1;
        
        return sprintf("%03d", $count);
    }

    public function getAnggotaKeluarga(Request $request)
    {
        $nkk = $request->query('nkk');
        $excludeId = $request->query('exclude_id');

        if (!$nkk) {
            return response()->json([]);
        }

        $kk = \App\Models\KartuKeluarga::where('nkk', $nkk)->first();
        if (!$kk) return response()->json([]);

        $anggota = Penduduk::where('kartu_keluarga_id', $kk->id)
            ->when($excludeId, function ($query, $excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->select('id', 'nik', 'nama', 'kedudukan_keluarga', 'kartu_keluarga_id')
            ->get();

        return response()->json($anggota);
    }
}
