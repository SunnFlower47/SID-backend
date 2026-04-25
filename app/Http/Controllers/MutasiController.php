<?php

namespace App\Http\Controllers;

use App\Models\Mutasi;
use App\Models\Penduduk;
use App\Models\DesaSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Services\MutasiService;

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

        return view('mutasi.index', compact('mutasis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('mutasi.create');

        $penduduks = Penduduk::orderBy('nama')->get();

        return view('mutasi.create', compact('penduduks'));
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

            if ($request->ajax() || $request->wantsJson()) {
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

            if ($request->ajax() || $request->wantsJson()) {
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

        $mutasi->load('penduduk');
        return view('mutasi.show', compact('mutasi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mutasi $mutasi)
    {
        Gate::authorize('mutasi.edit');

        $penduduks = Penduduk::orderBy('nama')->get();
        return view('mutasi.edit', compact('mutasi', 'penduduks'));
    }

    /**
     * Update the specified resource in storage.
     * Validasi dan update conditional berdasarkan jenis_mutasi.
     */
    public function update(Request $request, Mutasi $mutasi)
    {
        Gate::authorize('mutasi.edit');

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
            $mutasi->penduduk->update([
                'nama' => $validated['nama_bayi'] ?? $mutasi->penduduk->nama,
                'jenis_kelamin' => $validated['jenis_kelamin_bayi'] ?? $mutasi->penduduk->jenis_kelamin,
                'tempat_lahir' => $validated['tempat_lahir'] ?? $mutasi->penduduk->tempat_lahir,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? $mutasi->penduduk->tanggal_lahir,
                'nkk' => $validated['nkk'] ?? $mutasi->penduduk->nkk,
            ]);

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

        $wantsJson = $request->expectsJson() || $request->wantsJson() || $request->ajax();

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
                if ($snapshot && !empty($snapshot['anggota'])) {
                    foreach ($snapshot['anggota'] as $anggotaData) {
                        $penduduk = Penduduk::find($anggotaData['id']);
                        if ($penduduk) {
                            $penduduk->update([
                                'rt' => $anggotaData['rt_asal'] ?? $penduduk->rt,
                                'rw' => $anggotaData['rw_asal'] ?? $penduduk->rw,
                                'dusun' => $anggotaData['dusun_asal'] ?? $penduduk->dusun,
                                'alamat' => $anggotaData['alamat_asal'] ?? $penduduk->alamat,
                            ]);
                        }
                    }
                }
            }

            // Revert data Pisah KK dalam_desa dari snapshot
            if ($mutasi->jenis_mutasi == 'pisah_kk' && $mutasi->kategori_mutasi === 'dalam_desa') {
                $snapshot = $mutasi->detail_tambahan['snapshot_asal'] ?? null;
                if ($snapshot) {
                    // Revert penduduk utama
                    $penduduk = Penduduk::find($mutasi->penduduk_id);
                    if ($penduduk && !empty($snapshot['nkk_asal'])) {
                        $penduduk->update([
                            'nkk' => $snapshot['nkk_asal'],
                            'rt' => $snapshot['rt_asal'] ?? $penduduk->rt,
                            'rw' => $snapshot['rw_asal'] ?? $penduduk->rw,
                            'dusun' => $snapshot['dusun_asal'] ?? $penduduk->dusun,
                            'alamat' => $snapshot['alamat_asal'] ?? $penduduk->alamat,
                            'kedudukan_keluarga' => $snapshot['kedudukan_asal'] ?? $penduduk->kedudukan_keluarga,
                        ]);
                    }
                    // Revert anggota yang ikut pindah
                    if (!empty($snapshot['anggota_pindah'])) {
                        foreach ($snapshot['anggota_pindah'] as $anggotaData) {
                            $anggota = Penduduk::find($anggotaData['id']);
                            if ($anggota) {
                                $anggota->update([
                                    'nkk' => $anggotaData['nkk_asal'] ?? $anggota->nkk,
                                    'rt' => $anggotaData['rt_asal'] ?? $anggota->rt,
                                    'rw' => $anggotaData['rw_asal'] ?? $anggota->rw,
                                    'dusun' => $anggotaData['dusun_asal'] ?? $anggota->dusun,
                                    'alamat' => $anggotaData['alamat_asal'] ?? $anggota->alamat,
                                ]);
                            }
                        }
                    }
                }
            }

            // Delete mutasi record
            $mutasi->delete();

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
        $kks = Penduduk::select('nkk', 'nama', 'alamat', 'rt', 'rw', 'dusun', 'kartu_keluarga_id')
                ->where('kedudukan_keluarga', 'Kepala Keluarga')
                ->where(function($q) use ($search) {
                    $q->where('nkk', 'like', "%{$search}%")
                      ->orWhere('nama', 'like', "%{$search}%");
                })
                ->limit(10)->get();

        $result = $kks->map(function ($kk) {
            return [
                'nkk' => $kk->nkk,
                'kepala_keluarga' => $kk->nama,
                'nama' => $kk->nama,
                'alamat' => $kk->alamat,
                'rt' => $kk->rt,
                'rw' => $kk->rw,
                'dusun' => $kk->dusun,
                'kartu_keluarga_id' => $kk->kartu_keluarga_id,
                'jumlah_anggota' => Penduduk::where('nkk', $kk->nkk)->count(),
            ];
        })->values();
        
        return response()->json($result);
    }

    public function searchPenduduk(Request $request) {
        $search = $request->get('query');
        $penduduks = Penduduk::select('id', 'nik', 'nama', 'nkk', 'alamat', 'agama', 'jenis_kelamin', 'tanggal_lahir')
                ->where(function($q) use ($search) {
                    $q->where('nik', 'like', "%{$search}%")
                      ->orWhere('nama', 'like', "%{$search}%");
                })
                ->limit(10)->get();

        return response()->json($penduduks);
    }
    
    public function checkNKKExists(Request $request) {
        $nkk = $request->get('nkk');

        if (!$nkk) {
            return response()->json([]);
        }

        // Kembalikan format array agar kompatibel dengan caller lama (penduduk/create.js)
        $kk = Penduduk::select('nkk', 'nama as kepala_keluarga', 'rt', 'rw', 'dusun')
            ->where('nkk', $nkk)
            ->whereIn('kedudukan_keluarga', ['KEPALA KELUARGA', 'Kepala Keluarga', 'kepala keluarga'])
            ->first();

        if (!$kk) {
            $fallback = Penduduk::select('nkk', 'rt', 'rw', 'dusun')->where('nkk', $nkk)->first();
            if (!$fallback) {
                return response()->json([]);
            }

            $result = [
                'nkk' => $fallback->nkk,
                'kepala_keluarga' => 'Data KK ditemukan',
                'rt' => $fallback->rt,
                'rw' => $fallback->rw,
                'dusun' => $fallback->dusun,
            ];

            return response()->json([$result]);
        }

        return response()->json([[
            'nkk' => $kk->nkk,
            'kepala_keluarga' => $kk->kepala_keluarga,
            'rt' => $kk->rt,
            'rw' => $kk->rw,
            'dusun' => $kk->dusun,
        ]]);
    }
    
    public function undo(Request $request, Mutasi $mutasi)
    {
        Gate::authorize('mutasi.delete', $mutasi);

        $wantsJson = $request->expectsJson() || $request->wantsJson() || $request->ajax();

        // ============================================================
        // FASE 3: Guard Proteksi Undo — KK Bermasalah
        // ============================================================

        // Guard 1: Blokir Undo jika KK sudah diselesaikan secara PERMANEN.
        // Setelah resolveKkPermanen(), NKK seluruh anggota sudah berbeda.
        // Merestore KK lama ke NKK yang sudah berubah akan merusak integritas data.
        if ($mutasi->detail_tambahan['kk_sudah_diselesaikan'] ?? false) {
            $msg = 'Undo tidak dapat dilakukan. KK dari mutasi ini sudah diselesaikan secara permanen oleh admin (NKK baru sudah diterbitkan). Gunakan menu Kartu Keluarga untuk melakukan koreksi manual.';
            return $wantsJson
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : redirect()->back()->with('error', $msg);
        }

        // Guard 2: Rollback KK Sementara jika status masih 'bermasalah_sementara'.
        // Jika admin sudah menunjuk KK sementara tapi belum permanen, rollback dulu
        // kedudukan KK sementara ke posisi asalnya sebelum KK lama di-restore.
        // Ini mencegah 2 orang berstatus 'Kepala Keluarga' secara bersamaan.
        $kkSementaraId      = $mutasi->detail_tambahan['kk_sementara_id'] ?? null;
        $kkSementaraAsal    = $mutasi->detail_tambahan['kk_sementara_kedudukan_asal'] ?? null;
        if ($kkSementaraId && $kkSementaraAsal) {
            $kkSementara = Penduduk::find($kkSementaraId);
            if ($kkSementara) {
                $kkSementara->update(['kedudukan_keluarga' => $kkSementaraAsal]);
            }
        }
        // Setelah guard selesai, PendudukObserver::restored() akan otomatis
        // mereset status_kk = 'normal' saat penduduk KK lama di-restore di bawah.

        try {
            DB::beginTransaction();

            // Minimal operasional: restore penduduk yang soft delete untuk jenis mutasi terkait
            if (in_array($mutasi->jenis_mutasi, ['kematian', 'pindah_keluar', 'pisah_kk'])) {
                $penduduk = Penduduk::withTrashed()->find($mutasi->penduduk_id);

                if ($penduduk && $penduduk->trashed()) {
                    $penduduk->restore();
                }

                // Restore juga anggota yang ikut pindah/pisah jika ada
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

            // Soft delete log mutasi agar tidak aktif lagi di daftar
            $mutasi->delete();

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

        // Load relasi penduduk (yang sudah di soft delete, jadi perlu withTrashed)
        $mutasi->load(['penduduk' => function($q) {
            $q->withTrashed();
        }]);

        $penduduk = $mutasi->penduduk;
        
        if (!$penduduk) {
             return redirect()->back()->with('error', 'Data penduduk tidak ditemukan (mungkin terhapus permanen).');
        }

        // Ambil info Kepala Desa dari setting
        $kadesInfo = DesaSetting::getKepalaDesaInfo();
        // Pastikan nama kades diambil stringnya saja jika object, atau biarkan object karena View sudah handle
        // Tapi untuk amannya pass string sesuai ekspektasi view lama kalau belum diupdate
        // View yang baru sudah handle object/string.

        $data = [
            'mutasi' => $mutasi,
            'penduduk' => $penduduk,
            'nomor_surat' => $this->generateNomorSuratKematian(),
            'kepala_desa' => $kadesInfo, // Pass object/array directly
            'desa' => DesaSetting::getDesaInfo(), // Pass Desa info
            'logos' => DesaSetting::getLogos(),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('surat.templates.kematian', $data);
        $pdf->setPaper(array(0, 0, 609.4488, 935.433), 'landscape'); // F4 Landscape
        
        return $pdf->stream('Surat_Kematian_' . $penduduk->nik . '.pdf');
    }

    private function generateNomorSuratKematian()
    {
        // Simple auto number logic (bisa dikembangkan)
        $bulan = date('m');
        $tahun = date('Y');
        $count = Mutasi::where('jenis_mutasi', 'kematian')
                ->whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan)
                ->count() + 1;
        
        return sprintf("%03d", $count);
    }
    /**
     * Get anggota keluarga based on NKK for collective mutations
     */
    public function getAnggotaKeluarga(Request $request)
    {
        $nkk = $request->query('nkk');
        $excludeId = $request->query('exclude_id');

        if (!$nkk) {
            return response()->json([]);
        }

        $anggota = Penduduk::where('nkk', $nkk)
            ->when($excludeId, function ($query, $excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->select('id', 'nik', 'nama', 'kedudukan_keluarga', 'nkk')
            ->get();

        return response()->json($anggota);
    }
}
