<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;

use App\Models\Mutasi;
use App\Models\Penduduk;
use App\Models\DesaSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Services\MutasiService;
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
        Gate::authorize('kependudukan');

        $query = Mutasi::with(['penduduk' => function($q) {
            $q->withTrashed(); // Include soft deleted penduduk
        }]);

        // Search (wrapped in closure to prevent OR leak bypassing other filters)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($main) use ($search) {
                $main->whereHas('penduduk', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nik', 'like', "%{$search}%");
                })->orWhere('alasan', 'like', "%{$search}%");
            });
        }

        // Filter jenis mutasi
        if ($request->filled('jenis_mutasi')) {
            $query->where('jenis_mutasi', $request->jenis_mutasi);
        }

        // Filter kategori mutasi
        if ($request->filled('kategori_mutasi')) {
            $query->where('kategori_mutasi', $request->kategori_mutasi);
        }

        $mutasis = $query->orderBy('tanggal_mutasi', 'desc')->paginate(15);

        $stats = [
            'kelahiran' => Mutasi::where('jenis_mutasi', 'kelahiran')->count(),
            'kematian' => Mutasi::where('jenis_mutasi', 'kematian')->count(),
            'pindahan' => Mutasi::whereIn('jenis_mutasi', ['pindah_masuk', 'pindah_keluar', 'pindah_rt_rw'])->count(),
            'pisah_kk' => Mutasi::where('jenis_mutasi', 'pisah_kk')->count(),
        ];

        return Inertia::render('Tenant/Mutasi/Index', [
            'mutasis' => Inertia::defer(fn() => $mutasis),
            'filters' => $request->only(['search', 'jenis_mutasi', 'kategori_mutasi']),
            'stats' => Inertia::defer(fn() => $stats)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('kependudukan');

        // Build a hierarchical tree of Wilayah: Dusun -> RW -> RT
        $dusuns = \App\Models\Dusun::orderBy('nama')->get();
        $rws = \App\Models\Rw::with('rts')->orderBy('kode')->get();

        $wilayahTree = $dusuns->map(function($dusun) use ($rws) {
            return [
                'id' => $dusun->id,
                'nama' => $dusun->nama,
                'rws' => $rws->filter(function($rw) use ($dusun) {
                    // Assuming RTs have dusun_id, we link RW to Dusun if it has any RT in that Dusun
                    return $rw->rts->where('dusun_id', $dusun->id)->count() > 0;
                })->map(function($rw) use ($dusun) {
                    return [
                        'id' => $rw->id,
                        'kode' => $rw->kode,
                        'rts' => $rw->rts->where('dusun_id', $dusun->id)->map(function($rt) {
                            return [
                                'id' => $rt->id,
                                'kode' => $rt->kode
                            ];
                        })->values()
                    ];
                })->values()
            ];
        })->values();

        return Inertia::render('Tenant/Mutasi/Create', [
            'wilayahTree' => $wilayahTree
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\Mutasi\StoreMutasiRequest $request)
    {
        Gate::authorize('kependudukan');

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

            if (($request->ajax() || $request->wantsJson()) && !$request->header('X-Inertia')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data mutasi berhasil ditambahkan.',
                    'redirect' => route('mutasi.data.index')
                ]);
            }

            return redirect()->route('mutasi.data.index')
                ->with('success', 'Data mutasi berhasil ditambahkan.');

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
        Gate::authorize('kependudukan');

        $penduduks = Penduduk::orderBy('nama')->get();
        $rws = \App\Models\Rw::with(['rts.dusun'])->orderBy('kode')->get();
        $masterRwOptions = $rws->map(function ($rw) {
            return [
                'id' => $rw->id,
                'kode' => $rw->kode,
                'nama' => $rw->nama,
                'rts' => $rw->rts->map(function ($rt) {
                    return [
                        'id' => $rt->id,
                        'kode' => $rt->kode,
                        'dusun_id' => $rt->dusun_id,
                        'dusun' => optional($rt->dusun)->nama,
                    ];
                })->values(),
            ];
        })->values();
        
        // Build a hierarchical tree of Wilayah: Dusun -> RW -> RT
        $dusuns = \App\Models\Dusun::orderBy('nama')->get();
        $wilayahTree = $dusuns->map(function($dusun) use ($rws) {
            return [
                'id' => $dusun->id,
                'nama' => $dusun->nama,
                'rws' => $rws->filter(function($rw) use ($dusun) {
                    return $rw->rts->where('dusun_id', $dusun->id)->count() > 0;
                })->map(function($rw) use ($dusun) {
                    return [
                        'id' => $rw->id,
                        'kode' => $rw->kode,
                        'rts' => $rw->rts->where('dusun_id', $dusun->id)->map(function($rt) {
                            return [
                                'id' => $rt->id,
                                'kode' => $rt->kode
                            ];
                        })->values()
                    ];
                })->values()
            ];
        })->values();
        
        return \Inertia\Inertia::render('Tenant/Mutasi/Edit', [
            'mutasi' => $mutasi->load(['penduduk' => fn($q) => $q->withWilayah()]),
            'penduduks' => $penduduks,
            'masterRwOptions' => $masterRwOptions,
            'wilayahTree' => $wilayahTree
        ]);
    }

    /**
     * Update the specified resource in storage.
     * Validasi dan update conditional berdasarkan jenis_mutasi.
     */
    public function update(Request $request, Mutasi $mutasi)
    {
        Gate::authorize('kependudukan');

        // Base validation — tanggal_mutasi selalu ada, jenis_mutasi tidak boleh diubah
        $baseRules = [
            'tanggal_mutasi' => 'required|date',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];

        // Rules conditional berdasarkan jenis mutasi saat ini (read-only, tidak diubah)
        switch ($mutasi->jenis_mutasi) {
            case 'kematian':
                $rules = $baseRules + [
                    'penduduk_id' => 'required|exists:penduduks,id',
                    'hari_meninggal' => 'required|string|max:20',
                    'jam_meninggal' => 'required|date_format:H:i',
                    'bertempat_di' => 'required|string|max:255',
                    'alasan' => 'required|string|max:500',
                    'hari_pemakaman' => 'required|string|max:20',
                    'tanggal_pemakaman' => 'required|date',
                    'jam_pemakaman' => 'required|date_format:H:i',
                    'lokasi_pemakaman' => 'required|string|max:255',
                ];
                break;

            case 'kelahiran':
                $rules = $baseRules + [
                    'nama_bayi' => 'required|string|max:255',
                    'jenis_kelamin_bayi' => 'required|in:LAKI-LAKI,PEREMPUAN',
                    'tempat_lahir' => 'required|string|max:255',
                    'tanggal_lahir' => 'required|date',
                    'nama_ayah' => 'required|string|max:255',
                    'nama_ibu' => 'required|string|max:255',
                    'nkk' => 'required|string|size:16',
                ];
                break;

            case 'pindah_masuk':
            case 'pindah_keluar':
                $rules = $baseRules + [
                    'penduduk_id' => ($mutasi->jenis_mutasi === 'pindah_keluar') ? 'required|exists:penduduks,id' : 'nullable',
                    'kategori_mutasi' => 'required|in:dalam_desa,dalam_kota,luar_kota,luar_negeri',
                    'asal_tujuan' => 'required|string|max:255',
                    'alasan' => 'nullable|string|max:500',
                ];
                break;

            case 'pindah_rt_rw':
                $rules = $baseRules + [
                    'asal_tujuan' => 'nullable|string|max:500',
                    'alasan' => 'nullable|string|max:500',
                ];
                break;

            case 'pisah_kk':
                $rules = $baseRules + [
                    'alasan' => 'nullable|string|max:500',
                ];
                break;

            default:
                $rules = $baseRules + [
                    'alasan' => 'nullable|string',
                ];
        }

        $validated = $request->validate($rules);

        // Handle file upload
        if ($request->hasFile('dokumen_pendukung')) {
            if ($mutasi->dokumen_pendukung) {
                Storage::delete($mutasi->dokumen_pendukung);
            }
            $validated['dokumen_pendukung'] = $request->file('dokumen_pendukung')->store('mutasi-documents');
        }

        // Untuk kematian, simpan data kematian ke detail_tambahan
        if ($mutasi->jenis_mutasi === 'kematian') {
            $detailTambahan = $mutasi->detail_tambahan ?? [];
            $detailTambahan['kematian'] = [
                'hari' => $validated['hari_meninggal'] ?? $detailTambahan['kematian']['hari'] ?? '',
                'jam' => $validated['jam_meninggal'] ?? $detailTambahan['kematian']['jam'] ?? '',
                'bertempat_di' => $validated['bertempat_di'] ?? $detailTambahan['kematian']['bertempat_di'] ?? '',
            ];
            $detailTambahan['pemakaman'] = [
                'hari' => $validated['hari_pemakaman'] ?? $detailTambahan['pemakaman']['hari'] ?? '',
                'tanggal' => $validated['tanggal_pemakaman'] ?? $detailTambahan['pemakaman']['tanggal'] ?? '',
                'jam' => $validated['jam_pemakaman'] ?? $detailTambahan['pemakaman']['jam'] ?? '',
                'lokasi' => $validated['lokasi_pemakaman'] ?? $detailTambahan['pemakaman']['lokasi'] ?? '',
            ];
            $validated['detail_tambahan'] = $detailTambahan;

            // Hapus field yang tidak ada di tabel mutasis
            unset($validated['hari_meninggal'], $validated['jam_meninggal'], $validated['bertempat_di'],
                  $validated['hari_pemakaman'], $validated['tanggal_pemakaman'], $validated['jam_pemakaman'],
                  $validated['lokasi_pemakaman']);
        }

        // Untuk kelahiran, update data penduduk yang terkait
        if ($mutasi->jenis_mutasi === 'kelahiran' && $mutasi->penduduk) {
            $updateData = [
                'nama' => $validated['nama_bayi'] ?? $mutasi->penduduk->nama,
                'jenis_kelamin' => $validated['jenis_kelamin_bayi'] ?? $mutasi->penduduk->jenis_kelamin,
                'tempat_lahir' => $validated['tempat_lahir'] ?? $mutasi->penduduk->tempat_lahir,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? $mutasi->penduduk->tanggal_lahir,
            ];

            // If NKK changed, we must find the KK ID, not just save the text
            if (isset($validated['nkk']) && $validated['nkk'] !== ($mutasi->penduduk->kartuKeluarga->nkk ?? '')) {
                 $newKk = \App\Models\KartuKeluarga::where('nkk', $validated['nkk'])->first();
                 if ($newKk) {
                     $updateData['kartu_keluarga_id'] = $newKk->id;
                 }
            }

            $mutasi->penduduk->update($updateData);

            // Hapus field yang tidak ada di tabel mutasis
            unset($validated['nama_bayi'], $validated['jenis_kelamin_bayi'], $validated['tempat_lahir'],
                  $validated['tanggal_lahir'], $validated['nama_ayah'], $validated['nama_ibu'], $validated['nkk']);
        }

        $mutasi->update($validated);

        return redirect()->route('mutasi.data.index')->with('success', 'Data mutasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function cancel(Request $request, Mutasi $mutasi)
    {
        Gate::authorize('mutasi.delete', $mutasi);

        $wantsJson = ($request->expectsJson() || $request->wantsJson() || $request->ajax()) && !$request->header('X-Inertia');

        // Pisah KK hanya bisa cancel jika dalam_desa
        if ($mutasi->jenis_mutasi == 'pisah_kk' && !in_array($mutasi->kategori_mutasi, ['dalam_desa'])) {
            $message = 'Mutasi ini tidak bisa dibatalkan. Gunakan tombol Undo untuk mengembalikan data.';
            return $wantsJson
                ? response()->json(['success' => false, 'message' => $message], 422)
                : redirect()->route('mutasi.data.index')->with('error', $message);
        }

        if (in_array($mutasi->jenis_mutasi, ['kematian', 'pindah_keluar'])) {
            $message = 'Mutasi ini tidak bisa dibatalkan. Gunakan tombol Undo untuk mengembalikan data.';
            return $wantsJson
                ? response()->json(['success' => false, 'message' => $message], 422)
                : redirect()->route('mutasi.data.index')->with('error', $message);
        }

        // Pembaruan KK tidak boleh di-cancel dari sini, harus lewat Undo
        if ($mutasi->jenis_mutasi === 'pembaruan_kk') {
            $message = 'Mutasi pembaruan KK tidak bisa dibatalkan dengan cara ini. Gunakan tombol Undo untuk mengembalikan status KK.';
            return $wantsJson
                ? response()->json(['success' => false, 'message' => $message], 422)
                : redirect()->route('mutasi.data.index')->with('error', $message);
        }

        try {
            DB::beginTransaction();

            if ($mutasi->jenis_mutasi == 'pindah_masuk' || $mutasi->jenis_mutasi == 'kelahiran') {
                $penduduk = $mutasi->penduduk;
                if ($penduduk) {
                    $penduduk->forceDelete();
                }
            }

            // Revert data Pindah RT/RW dari snapshot
            if ($mutasi->jenis_mutasi == 'pindah_rt_rw') {
                $snapshot = $mutasi->detail_tambahan['snapshot_asal'] ?? null;
                if ($snapshot && !empty($snapshot['nkk'])) {
                    $kk = \App\Models\KartuKeluarga::where('nkk', $snapshot['nkk'])->first();
                    if ($kk) {
                        $kk->update([
                            'rt_id' => $snapshot['rt_id_asal'] ?? $kk->rt_id,
                            'rw_id' => $snapshot['rw_id_asal'] ?? $kk->rw_id,
                            'dusun_id' => $snapshot['dusun_id_asal'] ?? $kk->dusun_id,
                            'alamat' => $snapshot['alamat_asal'] ?? $kk->alamat,
                        ]);
                    }
                }
            }

            // Revert data Pisah KK dalam_desa dari snapshot
            if ($mutasi->jenis_mutasi == 'pisah_kk' && $mutasi->kategori_mutasi === 'dalam_desa') {
                $snapshot = $mutasi->detail_tambahan['snapshot_asal'] ?? null;
                if ($snapshot) {
                    // Revert penduduk utama (back to original KK)
                    $penduduk = Penduduk::find($mutasi->penduduk_id);
                    if ($penduduk && !empty($snapshot['nkk_asal'])) {
                        $originalKk = \App\Models\KartuKeluarga::where('nkk', $snapshot['nkk_asal'])->first();
                        if ($originalKk) {
                            $penduduk->update([
                                'kartu_keluarga_id' => $originalKk->id,
                                'kedudukan_keluarga' => $snapshot['kedudukan_asal'] ?? $penduduk->kedudukan_keluarga,
                            ]);
                        }
                    }
                    // Revert anggota yang ikut pindah
                    if (!empty($snapshot['anggota_pindah'])) {
                        foreach ($snapshot['anggota_pindah'] as $anggotaData) {
                            $anggota = Penduduk::find($anggotaData['id']);
                            if ($anggota && !empty($anggotaData['nkk_asal'])) {
                                $originalKk = \App\Models\KartuKeluarga::where('nkk', $anggotaData['nkk_asal'])->first();
                                if ($originalKk) {
                                    $anggota->update([
                                        'kartu_keluarga_id' => $originalKk->id,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            // Delete mutasi record (forceDelete karena Mutasi model pakai SoftDeletes)
            $mutasi->forceDelete();

            DB::commit();

            return $wantsJson
                ? response()->json(['success' => true, 'message' => 'Mutasi berhasil dibatalkan.'])
                : redirect()->route('mutasi.data.index')->with('success', 'Mutasi berhasil dibatalkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            $message = 'Gagal membatalkan mutasi: ' . $e->getMessage();
            return $wantsJson
                ? response()->json(['success' => false, 'message' => $message], 500)
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
            return response()->json([]);
        }

        // 1. Search in Source of Truth (kartu_keluargas table)
        $kk = \App\Models\KartuKeluarga::where('nkk', $nkk)->first();

        if ($kk) {
            return response()->json([[
                'nkk' => $kk->nkk,
                'kepala_keluarga' => $kk->nama_kepala_keluarga ?? 'Kepala Keluarga',
                'alamat' => $kk->alamat,
                'rt_id' => $kk->rt_id,
                'rw_id' => $kk->rw_id,
                'dusun_id' => $kk->dusun_id,
                'rt' => $kk->rt_label,
                'rw' => $kk->rw_label,
                'dusun' => $kk->dusun_label,
            ]]);
        }

        // 2. Fallback to Penduduk table (Legacy/Search mode)
        $pendudukKK = Penduduk::withWilayah()
            ->whereHas('kartuKeluarga', function($q) use ($nkk) {
                $q->where('nkk', $nkk);
            })
            ->whereIn('kedudukan_keluarga', ['KEPALA KELUARGA', 'Kepala Keluarga', 'kepala keluarga'])
            ->first();

        if (!$pendudukKK) {
            return response()->json([]);
        }

        return response()->json([[
            'nkk' => $pendudukKK->kartuKeluarga->nkk ?? $pendudukKK->nkk,
            'kepala_keluarga' => $pendudukKK->nama,
            'alamat' => $pendudukKK->kartuKeluarga->alamat ?? $pendudukKK->alamat,
            'rt_id' => $pendudukKK->kartuKeluarga->rt_id ?? $pendudukKK->rt_id,
            'rw_id' => $pendudukKK->kartuKeluarga->rw_id ?? $pendudukKK->rw_id,
            'dusun_id' => $pendudukKK->kartuKeluarga->dusun_id ?? $pendudukKK->dusun_id,
            'rt' => $pendudukKK->rt_label,
            'rw' => $pendudukKK->rw_label,
            'dusun' => $pendudukKK->dusun_label,
        ]]);
    }
    
    public function undo(Request $request, Mutasi $mutasi)
    {
        Gate::authorize('mutasi.delete', $mutasi);

        $wantsJson = ($request->expectsJson() || $request->wantsJson() || $request->ajax()) && !$request->header('X-Inertia');

        // Guard: Blokir Undo jika KK sudah diselesaikan secara PERMANEN.
        if ($mutasi->detail_tambahan['kk_sudah_diselesaikan'] ?? false) {
            $msg = 'Undo tidak dapat dilakukan. KK dari mutasi ini sudah diselesaikan secara permanen.';
            return $wantsJson
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : redirect()->back()->with('error', $msg);
        }

        try {
            DB::beginTransaction();

            // ============================================================
            // Rollback KK Sementara jika ada (HARUS di dalam transaction!)
            // Ini terjadi saat undo mutasi kematian/pindah_keluar yang
            // sudah ada KK sementara ditunjuk → harus rollback semua.
            // ============================================================
            $kkSementaraId = $mutasi->detail_tambahan['kk_sementara_id'] ?? null;
            $kkSementaraAsal = $mutasi->detail_tambahan['kk_sementara_kedudukan_asal'] ?? null;
            if ($kkSementaraId && $kkSementaraAsal) {
                // 1. Rollback kedudukan penduduk KK sementara
                $kkSementara = Penduduk::find($kkSementaraId);
                if ($kkSementara) {
                    $kkSementara->update(['kedudukan_keluarga' => $kkSementaraAsal]);
                }

                // 2. Hapus mutasi pembaruan_kk sementara yang terkait (sinkron dengan batalkanSementara)
                $pembaruanKkMutasi = Mutasi::where('penduduk_id', $kkSementaraId)
                    ->where('jenis_mutasi', 'pembaruan_kk')
                    ->latest('id')
                    ->first();

                if ($pembaruanKkMutasi) {
                    $detPem = $pembaruanKkMutasi->detail_tambahan ?? [];
                    if (($detPem['tipe'] ?? null) === 'sementara') {
                        $pembaruanKkMutasi->forceDelete();
                    }
                }

                // 3. Reset KK record: kembali ke normal (bukan bermasalah, karena penyebab bermasalah-nya di-undo)
                $pendudukKk = $kkSementara?->kartuKeluarga;
                if ($pendudukKk && in_array($pendudukKk->status_kk, ['bermasalah', 'bermasalah_sementara'])) {
                    $pendudukKk->update([
                        'kk_sementara_id' => null,
                    ]);
                    // Status KK akan otomatis ter-recalculate di bawah via KartuKeluargaService
                }
            }

            $snapshot = $mutasi->detail_tambahan['snapshot_asal'] ?? null;
            $oldKK = null;
            if ($snapshot && !empty($snapshot['nkk_asal'])) {
                $oldKK = \App\Models\KartuKeluarga::where('nkk', $snapshot['nkk_asal'])->first();
            }

            // Capture the current KK ID before moving them back
            $currentKKIdOfPenduduk = null;
            $pendudukForRecalc = Penduduk::find($mutasi->penduduk_id);
            if ($pendudukForRecalc) {
                $currentKKIdOfPenduduk = $pendudukForRecalc->kartu_keluarga_id;
            }

            // ============================================================
            // HANDLER: Pembaruan KK (Resolusi KK Bermasalah)
            // Mutasi ini TIDAK melakukan soft-delete. Dia hanya mengubah
            // kedudukan penduduk. Jadi undo = kembalikan kedudukan.
            // ============================================================
            if ($mutasi->jenis_mutasi === 'pembaruan_kk') {
                $penduduk = Penduduk::find($mutasi->penduduk_id);
                if ($penduduk) {
                    // Ambil kedudukan asal dari detail_tambahan
                    $kedudukanAsal = $mutasi->detail_tambahan['kedudukan_asal'] ?? null;
                    if ($kedudukanAsal) {
                        $penduduk->update(['kedudukan_keluarga' => $kedudukanAsal]);
                    } else {
                        // Safety fallback: kalau kedudukan_asal tidak ada, set ke ANGGOTA
                        $penduduk->update(['kedudukan_keluarga' => 'ANGGOTA']);
                    }
                }

                // Reset status KK kembali ke bermasalah
                $nkk = $mutasi->detail_tambahan['nkk'] ?? null;
                if ($nkk) {
                    $kk = \App\Models\KartuKeluarga::where('nkk', $nkk)->first();
                    if ($kk) {
                        $kk->update([
                            'status_kk' => 'bermasalah',
                            'kk_sementara_id' => null,
                            'catatan_bermasalah' => 'Dikembalikan via Undo mutasi pembaruan KK',
                            'kk_bermasalah_sejak' => now(),
                        ]);

                        // Bersihkan info kk_sementara dari mutasi penyebab (sinkron dengan batalkanSementara)
                        if ($kk->mutasi_penyebab_id) {
                            $mutasiPenyebab = Mutasi::find($kk->mutasi_penyebab_id);
                            if ($mutasiPenyebab) {
                                $detail = $mutasiPenyebab->detail_tambahan;
                                if (!is_array($detail)) {
                                    $detail = json_decode($detail, true) ?: [];
                                }
                                unset($detail['kk_sementara_id'], $detail['kk_sementara_kedudukan_asal']);
                                $mutasiPenyebab->update(['detail_tambahan' => $detail]);
                            }
                        }

                        // Recalculate stats for this KK
                        app(\App\Services\KartuKeluargaService::class)->recalculate($kk->id);
                    }
                }

                // Hapus record mutasi (forceDelete karena Mutasi model pakai SoftDeletes)
                $mutasi->forceDelete();
                DB::commit();

                return $wantsJson
                    ? response()->json(['success' => true, 'message' => 'Undo pembaruan KK berhasil. Status KK dikembalikan ke bermasalah.'])
                    : redirect()->route('mutasi.data.index')->with('success', 'Undo pembaruan KK berhasil. Status KK dikembalikan ke bermasalah.');
            }

            // ============================================================
            // HANDLER: Kematian / Pindah Keluar / Pisah KK
            // Restore penduduk yang di-soft-delete & kembalikan ke KK asal
            // ============================================================
            if (in_array($mutasi->jenis_mutasi, ['kematian', 'pindah_keluar', 'pisah_kk'])) {
                // Main Resident
                $penduduk = Penduduk::withTrashed()->find($mutasi->penduduk_id);
                if ($penduduk) {
                    if ($penduduk->trashed()) {
                        $penduduk->restore();
                    }
                    
                    // Kembalikan kedudukan asal jika ada snapshot
                    $kedudukanRestore = $snapshot['kedudukan_asal'] ?? $penduduk->kedudukan_keluarga;
                    
                    // If it was a move within village, move them back
                    if ($oldKK && $penduduk->kartu_keluarga_id != $oldKK->id) {
                        $penduduk->update([
                            'kartu_keluarga_id' => $oldKK->id,
                            'kedudukan_keluarga' => $kedudukanRestore,
                        ]);
                    } else {
                        // Tetap kembalikan kedudukan meskipun KK sama
                        $penduduk->update([
                            'kedudukan_keluarga' => $kedudukanRestore,
                        ]);
                    }
                }

                // Accompanying Members
                if ($snapshot && !empty($snapshot['anggota_pindah'])) {
                    foreach ($snapshot['anggota_pindah'] as $anggotaData) {
                        $anggota = Penduduk::withTrashed()->find($anggotaData['id']);
                        if ($anggota) {
                            if ($anggota->trashed()) {
                                $anggota->restore();
                            }
                            
                            if ($oldKK && $anggota->kartu_keluarga_id != $oldKK->id) {
                                $anggota->update([
                                    'kartu_keluarga_id' => $oldKK->id,
                                    'kedudukan_keluarga' => $anggotaData['kedudukan_asal'] ?? $anggota->kedudukan_keluarga
                                ]);
                            }
                        }
                    }
                }
            }

            // ============================================================
            // HANDLER: Pindah RT/RW — Kembalikan alamat KK
            // ============================================================
            if ($mutasi->jenis_mutasi == 'pindah_rt_rw') {
                if ($snapshot && $oldKK) {
                    $oldKK->update([
                        'rt_id' => $snapshot['rt_id_asal'] ?? $oldKK->rt_id,
                        'rw_id' => $snapshot['rw_id_asal'] ?? $oldKK->rw_id,
                        'dusun_id' => $snapshot['dusun_id_asal'] ?? $oldKK->dusun_id,
                        'alamat' => $snapshot['alamat_asal'] ?? $oldKK->alamat,
                    ]);
                }
            }

            // ============================================================
            // Recalculate Stats for affected KKs
            // ============================================================
            $kkService = app(\App\Services\KartuKeluargaService::class);
            if ($oldKK) {
                $kkService->recalculate($oldKK->id);
            }
            if ($currentKKIdOfPenduduk && (!$oldKK || $currentKKIdOfPenduduk != $oldKK->id)) {
                $kkService->recalculate($currentKKIdOfPenduduk);
            }

            // Hapus record mutasi (forceDelete karena Mutasi model pakai SoftDeletes)
            $mutasi->forceDelete();

            DB::commit();

            return $wantsJson
                ? response()->json(['success' => true, 'message' => 'Undo mutasi berhasil.'])
                : redirect()->route('mutasi.data.index')->with('success', 'Undo mutasi berhasil.');

        } catch (\Exception $e) {
            DB::rollBack();
            $message = 'Gagal undo mutasi: ' . $e->getMessage();
            return $wantsJson
                ? response()->json(['success' => false, 'message' => $message], 500)
                : redirect()->route('mutasi.data.index')->with('error', $message);
        }
    }

    public function printSuratKematian(Mutasi $mutasi)
    {
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
