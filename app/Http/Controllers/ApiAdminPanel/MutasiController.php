<?php

namespace App\Http\Controllers\ApiAdminPanel;

use App\Models\Mutasi;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use App\Services\Kependudukan\MutasiService;
use Illuminate\Http\JsonResponse;

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
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Mutasi::class);

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

        $mutasis = $query->orderBy('tanggal_mutasi', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $mutasis,
            'meta' => [
                'stats' => [
                    'total' => Mutasi::count(),
                    'kelahiran' => Mutasi::where('jenis_mutasi', 'kelahiran')->count(),
                    'kematian' => Mutasi::where('jenis_mutasi', 'kematian')->count(),
                    'pindah_masuk' => Mutasi::where('jenis_mutasi', 'pindah_masuk')->count(),
                    'pindah_keluar' => Mutasi::where('jenis_mutasi', 'pindah_keluar')->count(),
                    'pisah_kk' => Mutasi::where('jenis_mutasi', 'pisah_kk')->count(),
                ]
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\Mutasi\StoreMutasiRequest $request): JsonResponse
    {
        Gate::authorize('create', Mutasi::class);

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

            return response()->json([
                'status' => 'success',
                'message' => 'Data mutasi berhasil ditambahkan'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $mutasi = Mutasi::withTrashed()->findOrFail($id);
        
        Gate::authorize('view', $mutasi);

        $mutasi->load(['penduduk' => function($q) {
            $q->withTrashed()->with(['rtMaster', 'rwMaster', 'dusunMaster']);
        }]);
        
        return response()->json([
            'status' => 'success',
            'data' => $mutasi
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $mutasi = Mutasi::withTrashed()->findOrFail($id);
        Gate::authorize('update', $mutasi);

        // Base validation
        $rules = [
            'tanggal_mutasi' => 'required|date',
            'alasan' => 'nullable|string|max:500',
        ];

        // Conditional validation based on jenis_mutasi
        switch ($mutasi->jenis_mutasi) {
            case 'kematian':
                $rules = array_merge($rules, [
                    'hari_meninggal' => 'required|string|max:20',
                    'jam_meninggal' => 'required',
                    'bertempat_di' => 'required|string|max:255',
                    'hari_pemakaman' => 'required|string|max:20',
                    'tanggal_pemakaman' => 'required|date',
                    'jam_pemakaman' => 'required',
                    'lokasi_pemakaman' => 'required|string|max:255',
                    'pelapor_nama' => 'nullable|string|max:255',
                    'pelapor_umur' => 'nullable|numeric',
                    'pelapor_pekerjaan' => 'nullable|string|max:255',
                    'pelapor_alamat' => 'nullable|string',
                    'pelapor_hubungan' => 'nullable|string|max:255',
                ]);
                break;
            case 'kelahiran':
                $rules = array_merge($rules, [
                    'nama_bayi' => 'required|string|max:255',
                    'jenis_kelamin_bayi' => 'required|in:LAKI-LAKI,PEREMPUAN',
                    'tempat_lahir' => 'required|string|max:255',
                    'tanggal_lahir' => 'required|date',
                    'nkk' => 'required|string|size:16',
                    'nama_ayah' => 'nullable|string|max:255',
                    'nama_ibu' => 'nullable|string|max:255',
                    'alamat_bayi' => 'nullable|string',
                    'rt_id_bayi' => 'nullable|exists:rts,id',
                    'rw_id_bayi' => 'nullable|exists:rws,id',
                    'dusun_id_bayi' => 'nullable|exists:dusuns,id',
                ]);
                break;
            case 'pindah_masuk':
                $rules = array_merge($rules, [
                    'nik' => 'required|string|size:16',
                    'nama' => 'required|string|max:255',
                    'kategori_mutasi' => 'required|in:dalam_desa,dalam_kota,luar_kota,luar_negeri',
                    'asal_tujuan' => 'required|string|max:255',
                    'nkk' => 'nullable|string|size:16',
                    'nkk_new' => 'nullable|string|size:16',
                ]);
                break;
            case 'pindah_keluar':
            case 'pisah_kk':
                $rules = array_merge($rules, [
                    'kategori_mutasi' => 'required|in:dalam_desa,dalam_kota,luar_kota,luar_negeri',
                    'asal_tujuan' => 'required|string|max:255',
                ]);
                break;
        }

        $validated = $request->validate($rules);

        // Update logic
        $detailTambahan = $mutasi->detail_tambahan ?? [];

        if ($mutasi->jenis_mutasi === 'kematian') {
            $detailTambahan['kematian'] = [
                'hari' => $validated['hari_meninggal'],
                'jam' => $validated['jam_meninggal'],
                'bertempat_di' => $validated['bertempat_di'],
            ];
            $detailTambahan['pemakaman'] = [
                'hari' => $validated['hari_pemakaman'],
                'tanggal' => $validated['tanggal_pemakaman'],
                'jam' => $validated['jam_pemakaman'],
                'lokasi' => $validated['lokasi_pemakaman'],
            ];
            $detailTambahan['pelapor_nama'] = $validated['pelapor_nama'] ?? null;
            $detailTambahan['pelapor_umur'] = $validated['pelapor_umur'] ?? null;
            $detailTambahan['pelapor_pekerjaan'] = $validated['pelapor_pekerjaan'] ?? null;
            $detailTambahan['pelapor_alamat'] = $validated['pelapor_alamat'] ?? null;
            $detailTambahan['pelapor_hubungan'] = $validated['pelapor_hubungan'] ?? null;
        }

        if ($mutasi->jenis_mutasi === 'kelahiran' && $mutasi->penduduk) {
            $mutasi->penduduk->update([
                'nama' => $validated['nama_bayi'],
                'jenis_kelamin' => $validated['jenis_kelamin_bayi'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'nama_ayah' => $validated['nama_ayah'] ?? $mutasi->penduduk->nama_ayah,
                'nama_ibu' => $validated['nama_ibu'] ?? $mutasi->penduduk->nama_ibu,
            ]);

            // Update Kartu Keluarga info
            if ($mutasi->penduduk->kartuKeluarga) {
                $mutasi->penduduk->kartuKeluarga->update(['nkk' => $validated['nkk']]);
            }
        }

        if ($mutasi->jenis_mutasi === 'pindah_masuk' && $mutasi->penduduk) {
            $mutasi->penduduk->update([
                'nik' => $validated['nik'],
                'nama' => $validated['nama'],
            ]);
            
            if ($validated['nkk_new'] && $mutasi->penduduk->kartuKeluarga) {
                $mutasi->penduduk->kartuKeluarga->update(['nkk' => $validated['nkk_new']]);
                $detailTambahan['nkk_new'] = $validated['nkk_new'];
            }
        }

        $mutasi->detail_tambahan = $detailTambahan;
        $mutasi->tanggal_mutasi = $validated['tanggal_mutasi'];
        $mutasi->alasan = $validated['alasan'] ?? $mutasi->alasan;
        $mutasi->kategori_mutasi = $validated['kategori_mutasi'] ?? $mutasi->kategori_mutasi;
        $mutasi->asal_tujuan = $validated['asal_tujuan'] ?? $mutasi->asal_tujuan;
        $mutasi->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data mutasi berhasil diperbarui',
            'data' => $mutasi->load(['penduduk' => fn($q) => $q->withTrashed()])
        ]);
    }

    /**
     * Stream PDF Surat Kematian.
     */
    public function printKematian($id)
    {
        $mutasi = Mutasi::withTrashed()->findOrFail($id);
        
        if ($mutasi->jenis_mutasi !== 'kematian') {
            return response()->json(['status' => 'error', 'message' => 'Bukan data kematian'], 400);
        }

        $mutasi->load(['penduduk' => fn($q) => $q->withTrashed()]);
        
        $kadesInfo = \App\Models\DesaSetting::getKepalaDesaInfo();
        $data = [
            'mutasi' => $mutasi,
            'penduduk' => $mutasi->penduduk,
            'nomor_surat' => $this->generateNomorSuratKematian(),
            'kepala_desa' => $kadesInfo,
            'desa' => \App\Models\DesaSetting::getDesaInfo(),
            'logos' => \App\Models\DesaSetting::getLogos(),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('surat.templates.kematian', $data);
        $pdf->setPaper(array(0, 0, 609.4488, 935.433), 'landscape'); 
        
        return $pdf->stream('Surat_Kematian_' . $mutasi->penduduk->nik . '.pdf');
    }

    private function generateNomorSuratKematian()
    {
        return \App\Models\DesaSetting::generateNomorSurat('kematian');
    }

    /**
     * Cancel/Delete mutation record.
     */
    public function destroy($id): JsonResponse
    {
        $mutasi = Mutasi::withTrashed()->findOrFail($id);
        Gate::authorize('delete', $mutasi);

        try {
            DB::beginTransaction();

            // 1. Logic Pembatalan (Cancel) — Untuk mutasi yang menambah/mengubah data aktif
            if ($mutasi->jenis_mutasi == 'pindah_masuk' || $mutasi->jenis_mutasi == 'kelahiran') {
                $penduduk = $mutasi->penduduk;
                if ($penduduk) {
                    $penduduk->forceDelete();
                }
            }

            // 2. Logic Undo — Untuk mutasi yang menghapus/memindah data (kematian, pindah_keluar, pisah_kk)
            if (in_array($mutasi->jenis_mutasi, ['kematian', 'pindah_keluar', 'pisah_kk'])) {
                // Guard: Jika KK sudah diselesaikan secara permanen (untuk pisah_kk)
                if ($mutasi->detail_tambahan['kk_sudah_diselesaikan'] ?? false) {
                    throw new \Exception('Undo tidak dapat dilakukan karena KK sudah diselesaikan secara permanen.');
                }

                $penduduk = Penduduk::withTrashed()->find($mutasi->penduduk_id);
                if ($penduduk && $penduduk->trashed()) {
                    $penduduk->restore();
                }

                // Restore anggota yang ikut pindah dari snapshot
                $snapshot = $mutasi->detail_tambahan['snapshot_asal'] ?? null;
                if ($snapshot && !empty($snapshot['anggota_pindah'])) {
                    foreach ($snapshot['anggota_pindah'] as $anggotaData) {
                        $anggota = Penduduk::withTrashed()->find($anggotaData['id']);
                        if ($anggota && $anggota->trashed()) {
                            $anggota->restore();
                        }
                    }
                }
            }

            // 3. Revert Data wilayah (Pindah RT/RW)
            if ($mutasi->jenis_mutasi == 'pindah_rt_rw') {
                $snapshot = $mutasi->detail_tambahan['snapshot_asal'] ?? null;
                $penduduk = Penduduk::find($mutasi->penduduk_id);
                if ($penduduk && $penduduk->kartuKeluarga && $snapshot) {
                    $penduduk->kartuKeluarga->update([
                        'rt_id' => $snapshot['rt_id_asal'] ?? $penduduk->rt_id,
                        'rw_id' => $snapshot['rw_id_asal'] ?? $penduduk->rw_id,
                        'dusun_id' => $snapshot['dusun_id_asal'] ?? $penduduk->dusun_id,
                        'alamat' => $snapshot['alamat_asal'] ?? $penduduk->alamat,
                    ]);
                }
            }

            // 4. Revert Pisah KK (Dalam Desa)
            if ($mutasi->jenis_mutasi == 'pisah_kk' && $mutasi->kategori_mutasi === 'dalam_desa') {
                $snapshot = $mutasi->detail_tambahan['snapshot_asal'] ?? null;
                if ($snapshot) {
                    $penduduk = Penduduk::find($mutasi->penduduk_id);
                    if ($penduduk && !empty($snapshot['nkk_asal'])) {
                        // Find the original KK record or restore based on NKK
                        $kkAsal = \App\Models\KartuKeluarga::where('nkk', $snapshot['nkk_asal'])->first();
                        if ($kkAsal) {
                            $penduduk->update([
                                'kartu_keluarga_id' => $kkAsal->id,
                                'kedudukan_keluarga' => $snapshot['kedudukan_asal'] ?? $penduduk->kedudukan_keluarga,
                            ]);
                        }
                    }
                }
            }

            // Delete mutasi record (soft delete or hard delete depending on model)
            $mutasi->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Mutasi berhasil dibatalkan/di-undo'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membatalkan mutasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search KK for mutation source.
     */
    public function searchKK(Request $request): JsonResponse
    {
        $search = $request->get('query');
        $kks = \App\Models\KartuKeluarga::withWilayah()
                ->where(function($q) use ($search) {
                    $q->where('nkk', 'like', "%{$search}%")
                      ->orWhere('nama_kepala_keluarga', 'like', "%{$search}%");
                })
                ->limit(10)->get()
                ->map(function($kk) {
                    return [
                        'nkk' => $kk->nkk,
                        'nama' => $kk->nama_kepala_keluarga,
                        'alamat' => $kk->alamat,
                        'rt_label' => $kk->rt_label,
                        'rw_label' => $kk->rw_label,
                        'dusun_label' => $kk->dusun_label,
                        'rt_id' => $kk->rt_id,
                        'rw_id' => $kk->rw_id,
                        'dusun_id' => $kk->dusun_id,
                        'jumlah_anggota' => $kk->anggota_aktif,
                    ];
                });

        return response()->json([
            'status' => 'success',
            'data' => $kks
        ]);
    }

    /**
     * Search Penduduk for mutation.
     */
    public function searchPenduduk(Request $request): JsonResponse
    {
        $search = $request->get('query');
        $penduduks = Penduduk::withWilayah()
                ->where(function($q) use ($search) {
                    $q->where('nik', 'like', "%{$search}%")
                      ->orWhere('nama', 'like', "%{$search}%");
                })
                ->limit(10)->get()
                ->map(function($p) {
                    // Gunakan accessors yang sudah kita buat di model Penduduk
                    $p->rt_label = $p->rt_label;
                    $p->rw_label = $p->rw_label;
                    $p->dusun_label = $p->dusun_label;
                    return $p;
                });

        return response()->json([
            'status' => 'success',
            'data' => $penduduks
        ]);
    }

    /**
     * Get anggota keluarga for collective mutation.
     */
    public function getAnggotaKeluarga(Request $request): JsonResponse
    {
        $nkk = $request->query('nkk');
        $excludeId = $request->query('exclude_id');

        if (!$nkk) return response()->json(['status' => 'error', 'message' => 'NKK required'], 400);

        $kk = \App\Models\KartuKeluarga::where('nkk', $nkk)->first();
        if (!$kk) return response()->json(['status' => 'error', 'message' => 'KK not found'], 404);

        $anggota = Penduduk::where('kartu_keluarga_id', $kk->id)
            ->when($excludeId, function ($query, $excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->select('id', 'nik', 'nama', 'kedudukan_keluarga')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $anggota
        ]);
    }
}
