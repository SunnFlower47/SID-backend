<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;

use App\Models\Mutasi;
use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use App\Exports\KartuKeluargaExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Artisan;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class KartuKeluargaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:kependudukan']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'all'); // all, aktif, kosong, bermasalah
        $dusun_id = $request->get('dusun_id');
        $rw_id = $request->get('rw_id');
        $rt_id = $request->get('rt_id');

        // Use the new Summary Table with Eager Loading
        $query = KartuKeluarga::withWilayah();

        // Filter by Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nkk', 'like', "%{$search}%")
                  ->orWhere('nama_kepala_keluarga', 'like', "%{$search}%");
            });
        }

        // Filter by Status
        if ($status === 'aktif') {
            $query->where('anggota_aktif', '>', 0);
        } elseif ($status === 'bermasalah') {
            $query->bermasalah();
        } elseif ($status === 'kosong') {
            $query->where('anggota_aktif', 0);
        }

        // Filter by Wilayah
        if ($dusun_id) $query->where('dusun_id', $dusun_id);
        if ($rw_id) $query->where('rw_id', $rw_id);
        if ($rt_id) $query->where('rt_id', $rt_id);

        // Sort and Paginate
        $kartuKeluarga = $query->orderBy('updated_at', 'desc')->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total'       => KartuKeluarga::count(),
            'aktif'       => KartuKeluarga::where('anggota_aktif', '>', 0)->count(),
            'bermasalah'  => KartuKeluarga::bermasalah()->count(),
            'kosong'      => KartuKeluarga::where('anggota_aktif', 0)->count(),
        ];

        // Wilayah for filters
        $dusunList = \App\Models\Dusun::orderBy('nama')->get(['id', 'nama']);
        $rwList = \App\Models\Rw::orderBy('kode')->get(['id', 'kode']);
        $rtList = \App\Models\Rt::orderBy('kode')->get(['id', 'kode']);

        return \Inertia\Inertia::render('Tenant/KartuKeluarga/Index', [
            'kartuKeluarga' => $kartuKeluarga,
            'stats'         => $stats,
            'filters'       => $request->all(['search', 'status', 'dusun_id', 'rw_id', 'rt_id']),
            'dusunList'     => $dusunList,
            'rwList'        => $rwList,
            'rtList'        => $rtList,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $masterRwOptions = \App\Models\Rw::with('rts')->orderBy('kode')->get()->map(function($rw) {
            return [
                'id' => $rw->id,
                'kode' => $rw->kode,
                'nama' => $rw->nama,
                'rts' => $rw->rts->map(function($rt) {
                    return [
                        'id' => $rt->id,
                        'kode' => $rt->kode,
                        'dusun_id' => $rt->dusun_id,
                        'dusun' => optional($rt->dusun)->nama
                    ];
                })
            ];
        });

        return view('kartu-keluarga.create', compact('masterRwOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nkk' => 'required|string|size:16|unique:kartu_keluargas,nkk',
            'nama_kepala_keluarga' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'rt_id' => 'required|exists:rts,id',
            'rw_id' => 'required|exists:rws,id',
        ]);

        $rtModel = \App\Models\Rt::find($request->rt_id);

        DB::beginTransaction();
        try {
            // 1. Create the "House" (Kartu Keluarga) first - The Source of Truth
            $kk = KartuKeluarga::create([
                'nkk' => $request->nkk,
                'nama_kepala_keluarga' => $request->nama_kepala_keluarga,
                'nik_kepala_keluarga' => $request->nik_kepala_keluarga,
                'alamat' => $request->alamat,
                'rt_id' => $request->rt_id,
                'rw_id' => $request->rw_id,
                'dusun_id' => optional($rtModel)->dusun_id,
            ]);

            // 2. Create the Kepala Keluarga and link to KK ID
            Penduduk::create([
                'kartu_keluarga_id' => $kk->id,
                'nik' => $request->nik_kepala_keluarga,
                'nama' => $request->nama_kepala_keluarga,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'agama' => $request->agama,
                'status_perkawinan' => $request->status_perkawinan,
                'pekerjaan' => $request->pekerjaan,
                'pendidikan' => $request->pendidikan,
                'kedudukan_keluarga' => 'Kepala Keluarga',
            ]);

            DB::commit();
            return redirect()->route('kk.index')
                ->with('success', 'Kartu Keluarga berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membuat KK: ' . $e->getMessage());
        }

        return redirect()->route('kk.index')
            ->with('success', 'Kartu Keluarga berhasil dibuat');
    }


    /**
     * Display the specified resource.
     */
    public function show($nkk)
    {
        // Find by NKK in KartuKeluarga (The Source of Truth)
        $kk = KartuKeluarga::where('nkk', $nkk)->firstOrFail();
        
        // Get members using relational ID
        $kartuKeluarga = Penduduk::withTrashed()
            ->where('kartu_keluarga_id', $kk->id)
            ->with(['mutasis' => function($q) {
                $q->latest('tanggal_mutasi')->latest('id');
            }])
            ->orderByRaw("CASE 
                WHEN kedudukan_keluarga = 'Kepala Keluarga' THEN 1 
                ELSE 2 
                END")
            ->get();

        $kepalaKeluarga = $kartuKeluarga->where('kedudukan_keluarga', 'Kepala Keluarga')->first();
        $anggotaKeluarga = $kartuKeluarga->where('kedudukan_keluarga', '!=', 'Kepala Keluarga')->values();

        return \Inertia\Inertia::render('Tenant/KartuKeluarga/Show', [
            'kk' => $kk,
            'kartuKeluarga' => $kartuKeluarga,
            'kepalaKeluarga' => $kepalaKeluarga,
            'anggotaKeluarga' => $anggotaKeluarga,
            'nkk' => $nkk,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($nkk)
    {
        $kk = KartuKeluarga::where('nkk', $nkk)->firstOrFail();
        $kartuKeluarga = Penduduk::where('kartu_keluarga_id', $kk->id)->get();
        
        if ($kartuKeluarga->isEmpty()) {
            abort(404, 'Kartu Keluarga tidak memiliki anggota');
        }

        $masterRwOptions = \App\Models\Rw::with('rts')->orderBy('kode')->get()->map(function($rw) {
            return [
                'id' => $rw->id,
                'kode' => $rw->kode,
                'nama' => $rw->nama,
                'rts' => $rw->rts->map(function($rt) {
                    return [
                        'id' => $rt->id,
                        'kode' => $rt->kode,
                        'dusun_id' => $rt->dusun_id,
                        'dusun' => optional($rt->dusun)->nama
                    ];
                })
            ];
        });

        return view('kartu-keluarga.edit', compact('kartuKeluarga', 'nkk', 'masterRwOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $nkk)
    {
        $request->validate([
            'nama_kepala_keluarga' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'rt_id' => 'required|exists:rts,id',
            'rw_id' => 'required|exists:rws,id',
        ]);

        $rtModel = \App\Models\Rt::find($request->rt_id);

        // Update Kartu Keluarga record (The Source of Truth)
        $kkRecord = KartuKeluarga::where('nkk', $nkk)->firstOrFail();
        
        $kkRecord->update([
            'nama_kepala_keluarga' => $request->nama_kepala_keluarga,
            'alamat' => $request->alamat,
            'rt_id' => $request->rt_id,
            'rw_id' => $request->rw_id,
            'dusun_id' => optional($rtModel)->dusun_id,
        ]);

        // Also update the head of family's name in Penduduk table if it changed
        $kepalaKeluarga = Penduduk::where('kartu_keluarga_id', $kkRecord->id)
            ->where('kedudukan_keluarga', 'Kepala Keluarga')
            ->first();

        if ($kepalaKeluarga) {
            $kepalaKeluarga->update([
                'nama' => $request->nama_kepala_keluarga,
            ]);
        }

        return redirect()->route('kk.show', $nkk)
            ->with('success', 'Kartu Keluarga berhasil diperbarui');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($nkk)
    {
        Gate::authorize('kependudukan');

        try {
            DB::beginTransaction();

            // 1. Ambil data KK summary (Source of Truth)
            $kk = KartuKeluarga::where('nkk', $nkk)->firstOrFail();
            
            // 2. Hapus semua anggota keluarga (soft delete) berdasarkan ID KK
            Penduduk::where('kartu_keluarga_id', $kk->id)->delete();

            // 3. Hapus record KK
            $kk->delete();

            DB::commit();

            return redirect()->route('kk.index')
                ->with('success', 'Kartu Keluarga dan semua anggotanya berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Export data to Excel
     */
    public function export(Request $request)
    {
        Gate::authorize('kependudukan');

        try {
            set_time_limit(300);
            ini_set('memory_limit', '512M');

            $filename = 'data_kartu_keluarga_' . now()->format('Y-m-d') . '.xlsx';
            return Excel::download(new KartuKeluargaExport($request), $filename);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengexport Excel: ' . $e->getMessage());
        }
    }

    /**
     * Auto-update kepala keluarga logic (Simplified to rely on Observer mostly, but keeping specific logic for manual trigger)
     */

    public function syncSummary()
    {
        Gate::authorize('kependudukan');

        try {
            set_time_limit(900); // Audit besar-besaran butuh waktu

            DB::beginTransaction();

            // 1. Audit Penduduk: Pastikan yang sudah mutasi (Meninggal/Pindah) sudah soft-deleted
            // Cari penduduk yang punya mutasi 'kematian' atau 'pindah_keluar' tapi belum didelete
            $mustDeleteIds = \App\Models\Mutasi::whereIn('jenis_mutasi', ['kematian', 'pindah_keluar'])
                ->whereHas('penduduk', function($q) {
                    $q->whereNull('deleted_at');
                })
                ->pluck('penduduk_id');
            
            if ($mustDeleteIds->count() > 0) {
                Penduduk::whereIn('id', $mustDeleteIds)->delete();
            }


            // 3. Hitung Ulang Statistik & Status Bermasalah
            $kkService = app(\App\Services\KartuKeluargaService::class);
            $total = 0;
            $allKk = KartuKeluarga::all();
            foreach ($allKk as $kk) {
                $kkService->recalculate($kk->id);
                $total++;
            }

            DB::commit();
            
            $message = "Audit data selesai! {$total} KK telah disinkronisasi. Penduduk yang mutasi telah diarsipkan otomatis.";

            return redirect()->route('kk.index')->with('success', $message);
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('kk.index')
                ->with('error', 'Gagal memproses audit data: ' . $e->getMessage());
        }
    }


    public function getKkBermasalah()
    {
        // FASE 5: pakai scope bermasalah() yang presisi
        $data = KartuKeluarga::bermasalah()->orderBy('updated_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    // =========================================================
    // FASE 5: Resolusi KK Bermasalah
    // =========================================================

    // =========================================================
    // FASE 6: Halaman Dedicated KK Bermasalah (Index + Audit)
    // =========================================================

    /**
     * Halaman dedicated KK Bermasalah:
     * - Tab "Perlu Ditangani": status bermasalah + bermasalah_sementara
     * - Tab "Riwayat Resolved": status resolved (audit trail)
     */
    public function indexBermasalah(Request $request)
    {
        $tab    = $request->get('tab', 'pending');   // pending | resolved
        $search = $request->get('search');
        $status = $request->get('status');

        $baseQuery = KartuKeluarga::query()
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('nkk', 'like', "%{$search}%")
                  ->orWhere('nama_kepala_keluarga', 'like', "%{$search}%");
            }));

        if ($tab === 'resolved') {
            $kkList = (clone $baseQuery)
                ->where('status_kk', 'resolved')
                ->orderBy('updated_at', 'desc')
                ->paginate(20)->withQueryString();
        } else {
            $kkList = (clone $baseQuery)
                ->bermasalah()
                ->when($status, fn($q) => $q->where('status_kk', $status))
                ->orderBy('kk_bermasalah_sejak', 'asc')   // terlama dulu
                ->paginate(20)->withQueryString();
        }

        $stats = [
            'bermasalah'           => KartuKeluarga::where('status_kk', 'bermasalah')->count(),
            'bermasalah_sementara' => KartuKeluarga::where('status_kk', 'bermasalah_sementara')->count(),
            'resolved'             => KartuKeluarga::where('status_kk', 'resolved')->count(),
        ];
        $stats['pending_total'] = $stats['bermasalah'] + $stats['bermasalah_sementara'];

        return \Inertia\Inertia::render('Tenant/KartuKeluarga/Bermasalah/Index', [
            'kkList' => $kkList,
            'stats'  => $stats,
            'tab'    => $tab,
            'search' => $search,
            'status' => $status,
        ]);
    }

    /**
     * Halaman resolusi KK bermasalah — tampilkan anggota aktif sebagai kandidat KK baru.
     */
    public function showBermasalah($nkk)
    {
        $kkRecord = KartuKeluarga::where('nkk', $nkk)->firstOrFail();

        if (!$kkRecord->isBermasalah()) {
            return redirect()->route('kk.show', $nkk)
                ->with('info', 'KK ini tidak dalam status bermasalah.');
        }

        // Anggota aktif (tidak soft-deleted) sebagai kandidat KK baru
        $anggotaAktif = Penduduk::where('kartu_keluarga_id', $kkRecord->id)
            ->orderByRaw("FIELD(kedudukan_keluarga,'Istri','Suami','Anak','Menantu','Cucu','Orang Tua','Mertua','Saudara')")
            ->get()
            ->map(function($a) {
                $a->umur = $a->tanggal_lahir ? \Carbon\Carbon::parse($a->tanggal_lahir)->age : 0;
                return $a;
            });

        $mutasiPenyebab = $kkRecord->mutasiPenyebab()->with('penduduk')->first();
        $kkSementara    = $kkRecord->kkSementara;

        return \Inertia\Inertia::render('Tenant/KartuKeluarga/Bermasalah/Show', [
            'kkRecord'       => $kkRecord,
            'nkk'            => $nkk,
            'anggotaAktif'   => $anggotaAktif,
            'mutasiPenyebab' => $mutasiPenyebab,
            'kkSementara'    => $kkSementara,
        ]);
    }

    /**
     * Step 1 — Tunjuk KK Sementara.
     * KK baru ditunjuk, NKK masih lama. Undo mutasi masih bisa dilakukan.
     */
    public function resolveKkSementara(Request $request, $nkk)
    {
        $request->validate(['kandidat_id' => 'required|exists:penduduks,id']);

        $kkRecord = KartuKeluarga::where('nkk', $nkk)->firstOrFail();

        if ($kkRecord->status_kk !== 'bermasalah') {
            return redirect()->back()->with('error', 'KK ini tidak dalam status bermasalah atau sudah ada KK sementara.');
        }

        $kandidat = Penduduk::findOrFail($request->kandidat_id);

        // --- VALIDASI UMUR ---
        $umur = $kandidat->tanggal_lahir ? \Carbon\Carbon::parse($kandidat->tanggal_lahir)->age : 0;
        if ($umur < 17) {
            return redirect()->back()->with('error', "Kandidat ({$kandidat->nama}) masih di bawah umur ({$umur} thn). Untuk KK tanpa orang dewasa, disarankan melakukan penggabungan KK atau pemindahan anggota melalui Mutasi.");
        }

        DB::beginTransaction();
        try {
            // Simpan kedudukan asal kandidat ke detail_tambahan mutasi penyebab
            if ($kkRecord->mutasi_penyebab_id) {
                $mutasiPenyebab = Mutasi::find($kkRecord->mutasi_penyebab_id);
                if ($mutasiPenyebab) {
                    // Fix: Pastikan detail adalah array agar tidak error "offset string on string"
                    $detail = $mutasiPenyebab->detail_tambahan;
                    if (!is_array($detail)) {
                        $detail = json_decode($detail, true) ?: [];
                    }

                    $detail['kk_sementara_id']              = $kandidat->id;
                    $detail['kk_sementara_kedudukan_asal']  = $kandidat->kedudukan_keluarga;
                    $mutasiPenyebab->update(['detail_tambahan' => $detail]);
                }
            }

            // Simpan kedudukan asal SEBELUM update (untuk keperluan Undo)
            $kedudukanAsal = $kandidat->kedudukan_keluarga;

            // PENTING: Set status KK ke bermasalah_sementara DULU
            // sebelum update penduduk, karena PendudukObserver akan trigger
            // recalculate() dan recalculate() sekarang menghormati status ini.
            $kkRecord->update([
                'status_kk'      => 'bermasalah_sementara',
                'kk_sementara_id' => $kandidat->id,
            ]);

            // Naikkan kandidat jadi Kepala Keluarga
            // (PendudukObserver akan trigger recalculate, tapi status sudah aman)
            $kandidat->update(['kedudukan_keluarga' => 'Kepala Keluarga']);

            // Log audit trail mutasi
            Mutasi::create([
                'penduduk_id'     => $kandidat->id,
                'jenis_mutasi'    => 'pembaruan_kk',
                'kategori_mutasi' => 'dalam_desa',
                'asal_tujuan'     => "Dinaikkan jadi Kepala Keluarga sementara NKK {$nkk}",
                'tanggal_mutasi'  => now()->toDateString(),
                'alasan'          => 'Resolusi KK bermasalah — penunjukan sementara',
                'detail_tambahan' => ['nkk' => $nkk, 'tipe' => 'sementara', 'kedudukan_asal' => $kedudukanAsal],
            ]);

            DB::commit();
            return redirect()->route('kk.bermasalah', $nkk)
                ->with('success', "{$kandidat->nama} berhasil ditunjuk sebagai Kepala Keluarga sementara.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menunjuk KK sementara: ' . $e->getMessage());
        }
    }

    /**
     * Step 2 — Finalisasi Permanen dengan NKK Baru (wajib).
     * Setelah ini Undo mutasi asal akan diblokir.
     */
    public function resolveKkPermanen(Request $request, $nkk)
    {
        $request->validate([
            'nkk_baru' => 'required|string|size:16|unique:kartu_keluargas,nkk',
        ]);

        $kkRecord = KartuKeluarga::where('nkk', $nkk)->firstOrFail();

        if ($kkRecord->status_kk !== 'bermasalah_sementara') {
            return redirect()->back()->with('error', 'Harus menunjuk KK sementara terlebih dahulu (Step 1).');
        }

        $nkkBaru = $request->nkk_baru;

        DB::beginTransaction();
        try {
            $oldNkk = $kkRecord->nkk;

            // 1. Buat KK baru (Fotokopi data wilayah)
            $newKk = KartuKeluarga::create([
                'nkk'                  => $nkkBaru,
                'nama_kepala_keluarga' => $kkRecord->kkSementara?->nama,
                'nik_kepala_keluarga'  => $kkRecord->kkSementara?->nik,
                'alamat'               => $kkRecord->alamat,
                'rt_id'                => $kkRecord->rt_id,
                'rw_id'                => $kkRecord->rw_id,
                'dusun_id'             => $kkRecord->dusun_id,
                'status_kk'            => 'normal',
                'anggota_aktif'        => $kkRecord->anggotaAktif()->count(),
            ]);

            // 2. Pindahkan semua anggota aktif ke KK baru
            Penduduk::where('kartu_keluarga_id', $kkRecord->id)
                ->whereDoesntHave('mutasis', function($q) {
                    $q->whereIn('jenis_mutasi', ['kematian', 'pindah_keluar', 'pisah_kk']);
                })
                ->update(['kartu_keluarga_id' => $newKk->id]);

            // 2b. Recalculate KK baru untuk memastikan jumlah_anggota, dll akurat
            app(\App\Services\KartuKeluargaService::class)->recalculate($newKk->id);

            // 3. Update mutasi penyebab di record LAMA (tanda resolved)
            if ($kkRecord->mutasi_penyebab_id) {
                $mutasiPenyebab = Mutasi::find($kkRecord->mutasi_penyebab_id);
                if ($mutasiPenyebab) {
                    $detail = $mutasiPenyebab->detail_tambahan ?? [];
                    $detail['kk_sudah_diselesaikan'] = true;
                    $detail['nkk_baru']              = $nkkBaru;
                    $mutasiPenyebab->update(['detail_tambahan' => $detail]);
                }
            }

            // 3b. Hapus mutasi pembaruan_kk sementara (dari Step 1) karena sudah permanen
            // Ini mencegah "ghost mutation" yang bisa di-undo dari menu Mutasi
            if ($kkRecord->kk_sementara_id) {
                Mutasi::where('penduduk_id', $kkRecord->kk_sementara_id)
                    ->where('jenis_mutasi', 'pembaruan_kk')
                    ->where('id', '!=', 0) // safety
                    ->get()
                    ->each(function ($m) use ($nkk) {
                        $det = $m->detail_tambahan ?? [];
                        if (($det['tipe'] ?? null) === 'sementara' && ($det['nkk'] ?? null) === $nkk) {
                            $m->forceDelete();
                        }
                    });
            }

            // 4. Arsipkan KK lama
            $kkRecord->update([
                'status_kk'          => 'resolved',
                'anggota_aktif'      => 0,
                'catatan_bermasalah' => json_encode(['nkk_baru' => $newKk->nkk]),
            ]);

            // 5. Log audit trail di Kepala Keluarga Baru
            // Tandai dengan kk_sudah_diselesaikan agar TIDAK bisa di-undo dari menu Mutasi
            Mutasi::create([
                'penduduk_id'     => $kkRecord->kk_sementara_id,
                'jenis_mutasi'    => 'pembaruan_kk',
                'kategori_mutasi' => 'dalam_desa',
                'asal_tujuan'     => "NKK Lama: {$oldNkk} → NKK Baru: {$nkkBaru}",
                'tanggal_mutasi'  => now()->toDateString(),
                'alasan'          => 'Resolusi KK bermasalah — Penyelesaian Permanen',
                'detail_tambahan' => [
                    'nkk_lama' => $oldNkk,
                    'nkk_baru' => $newKk->nkk,
                    'tipe' => 'permanen',
                    'kk_sudah_diselesaikan' => true,
                ],
            ]);

            DB::commit();
            return redirect()->route('kk.show', $newKk->nkk)
                ->with('success', "KK berhasil diselesaikan secara permanen. NKK baru: {$newKk->nkk}.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyelesaikan KK permanen: ' . $e->getMessage());
        }
    }

    /**
     * Batalkan KK Sementara — rollback ke status bermasalah (sebelum Step 2).
     * Digunakan jika admin salah memilih kandidat KK sementara.
     */
    public function batalkanSementara($nkk)
    {
        $kkRecord = KartuKeluarga::where('nkk', $nkk)->firstOrFail();

        if ($kkRecord->status_kk !== 'bermasalah_sementara') {
            return redirect()->back()->with('error', 'Tidak ada KK sementara yang perlu dibatalkan.');
        }

        DB::beginTransaction();
        try {
            // ============================================================
            // 1. Cari penduduk yang ditunjuk sementara
            //    Sumber utama: KK record kk_sementara_id (selalu di-set saat Step 1)
            // ============================================================
            $kkSementaraId = $kkRecord->kk_sementara_id;

            if (!$kkSementaraId) {
                throw new \Exception('Data KK sementara tidak ditemukan di record KK.');
            }

            // ============================================================
            // 2. Cari record mutasi pembaruan_kk sementara untuk mendapatkan kedudukan_asal
            //    Ini PASTI ada karena dibuat saat resolveKkSementara()
            // ============================================================
            $pembaruanMutasi = Mutasi::where('penduduk_id', $kkSementaraId)
                ->where('jenis_mutasi', 'pembaruan_kk')
                ->latest('id')
                ->first();

            $kedudukanAsal = null;
            if ($pembaruanMutasi) {
                $detailPembaruan = $pembaruanMutasi->detail_tambahan ?? [];
                // Verifikasi ini mutasi untuk NKK yang benar dan tipe sementara
                if (($detailPembaruan['nkk'] ?? null) === $nkk && ($detailPembaruan['tipe'] ?? null) === 'sementara') {
                    $kedudukanAsal = $detailPembaruan['kedudukan_asal'] ?? null;
                }
            }

            // Fallback: ambil dari mutasi penyebab (backward compat)
            if (!$kedudukanAsal && $kkRecord->mutasi_penyebab_id) {
                $mutasiPenyebab = Mutasi::find($kkRecord->mutasi_penyebab_id);
                $detail = $mutasiPenyebab?->detail_tambahan;
                if ($detail && !is_array($detail)) {
                    $detail = json_decode($detail, true) ?: [];
                }
                $kedudukanAsal = $detail['kk_sementara_kedudukan_asal'] ?? null;
            }

            // ============================================================
            // 3. Rollback kedudukan penduduk ke posisi asal
            // ============================================================
            $penduduk = Penduduk::find($kkSementaraId);
            if ($penduduk && $kedudukanAsal) {
                $penduduk->update(['kedudukan_keluarga' => $kedudukanAsal]);
            } elseif ($penduduk) {
                // Safety fallback: jika kedudukan_asal tidak ditemukan tapi penduduk ada,
                // set ke ANGGOTA sebagai default aman (bukan Kepala Keluarga)
                $penduduk->update(['kedudukan_keluarga' => 'ANGGOTA']);
            }

            // ============================================================
            // 4. Hapus record mutasi pembaruan_kk (forceDelete karena SoftDeletes)
            // ============================================================
            if ($pembaruanMutasi) {
                $detailCheck = $pembaruanMutasi->detail_tambahan ?? [];
                if (($detailCheck['nkk'] ?? null) === $nkk && ($detailCheck['tipe'] ?? null) === 'sementara') {
                    $pembaruanMutasi->forceDelete();
                }
            }

            // ============================================================
            // 5. Bersihkan info kk_sementara dari mutasi penyebab (jika ada)
            // ============================================================
            if ($kkRecord->mutasi_penyebab_id) {
                $mutasiPenyebab = Mutasi::find($kkRecord->mutasi_penyebab_id);
                if ($mutasiPenyebab) {
                    $detail = $mutasiPenyebab->detail_tambahan;
                    if (!is_array($detail)) {
                        $detail = json_decode($detail, true) ?: [];
                    }
                    unset($detail['kk_sementara_id'], $detail['kk_sementara_kedudukan_asal']);
                    $mutasiPenyebab->update(['detail_tambahan' => $detail]);
                }
            }

            // ============================================================
            // 6. Reset KK status ke bermasalah
            // ============================================================
            $kkRecord->update([
                'status_kk'       => 'bermasalah',
                'kk_sementara_id' => null,
            ]);

            DB::commit();
            return redirect()->route('kk.bermasalah', $nkk)
                ->with('success', 'Penunjukan KK sementara berhasil dibatalkan. Silakan pilih kandidat lain.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membatalkan KK sementara: ' . $e->getMessage());
        }
    }

    public function downloadPdf($nkk)
    {
        Gate::authorize('kependudukan');
        // Keep existing PDF logic...
         try {
            $kkRecord = KartuKeluarga::where('nkk', $nkk)->first();
            if (!$kkRecord) return back()->with('error', 'KK null');
            
             $kepalaKeluarga = Penduduk::where('kartu_keluarga_id', $kkRecord->id)->where('kedudukan_keluarga', 'Kepala Keluarga')->first();
             $anggotaKeluarga = Penduduk::where('kartu_keluarga_id', $kkRecord->id)->orderByRaw("CASE
                    WHEN kedudukan_keluarga = 'Kepala Keluarga' THEN 1
                    WHEN kedudukan_keluarga = 'Istri' THEN 2
                    WHEN kedudukan_keluarga = 'Anak' THEN 3
                    ELSE 9 END")->get();
                    
            $kk = $kkRecord; // For backward compatibility in template if it uses $kk->nkk
                    
            $desaInfo = DB::table('desa_settings')->where('key', 'like', '%desa%')->pluck('value', 'key')->toArray();
            $desaInfoObj = (object) [
                'nama_desa' => $desaInfo['desa_nama'] ?? 'Desa Cibatu',
                'kecamatan' => $desaInfo['desa_kecamatan'] ?? 'Kecamatan',
                'kabupaten' => $desaInfo['desa_kabupaten'] ?? 'Kabupaten',
                'provinsi' => $desaInfo['desa_provinsi'] ?? 'Provinsi',
                'kode_pos' => $desaInfo['desa_kode_pos'] ?? '12345',
                'nip_kepala_dinas' => $desaInfo['nip_kepala_dinas'] ?? '-'
            ];

             $pdf = Pdf::loadView('kartu-keluarga.pdf-template', [
                'kk' => $kk,
                'kepalaKeluarga' => $kepalaKeluarga,
                'anggotaKeluarga' => $anggotaKeluarga,
                'desaInfo' => $desaInfoObj
            ]);
            return $pdf->download('KK_'.$nkk.'.pdf');
         } catch (\Exception $e) {
             return back()->with('error', $e->getMessage());
         }
    }
    
    
    // Static helper (kept for compatibility)
    public static function getTotalKK() {
        return KartuKeluarga::count();
    }
}
