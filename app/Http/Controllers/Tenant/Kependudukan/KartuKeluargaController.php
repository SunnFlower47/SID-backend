<?php

namespace App\Http\Controllers\Tenant\Kependudukan;

use App\Http\Controllers\Controller;

use App\Models\Mutasi;
use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use App\Exports\KartuKeluargaExport;
use App\Services\KartuKeluargaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

class KartuKeluargaController extends Controller
{
    protected $kkService;

    public function __construct(KartuKeluargaService $kkService)
    {
        $this->kkService = $kkService;
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

        return Inertia::render('Tenant/KartuKeluarga/Index', [
            'kartuKeluarga' => $kartuKeluarga,
            'stats'         => $stats,
            'filters'       => $request->all(['search', 'status', 'dusun_id', 'rw_id', 'rt_id']),
            'dusunList'     => \App\Models\Dusun::orderBy('nama')->get(['id', 'nama']),
            'rwList'        => \App\Models\Rw::orderBy('kode')->get(['id', 'kode']),
            'rtList'        => \App\Models\Rt::orderBy('kode')->get(['id', 'kode']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Tenant/KartuKeluarga/Create', [
            'masterRwOptions' => $this->kkService->getMasterRwOptions()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nkk' => 'required|string|size:16|unique:kartu_keluargas,nkk',
            'nama_kepala_keluarga' => 'required|string|max:255',
            'nik_kepala_keluarga' => 'required|string|size:16',
            'alamat' => 'required|string|max:500',
            'rt_id' => 'required|exists:rts,id',
            'rw_id' => 'required|exists:rws,id',
            'jenis_kelamin' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'agama' => 'required|string',
            'status_perkawinan' => 'required|string',
            'pekerjaan' => 'required|string',
            'pendidikan' => 'required|string',
        ]);

        try {
            $this->kkService->createKK($validated);
            return redirect()->route('kk.index')->with('success', 'Kartu Keluarga berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat KK: ' . $e->getMessage())->withInput();
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($nkk)
    {
        $kk = KartuKeluarga::where('nkk', $nkk)->firstOrFail();
        
        $kartuKeluarga = Penduduk::withTrashed()
            ->where('kartu_keluarga_id', $kk->id)
            ->with(['mutasis' => fn($q) => $q->latest('tanggal_mutasi')->latest('id')])
            ->orderByRaw("CASE WHEN kedudukan_keluarga = 'Kepala Keluarga' THEN 1 ELSE 2 END")
            ->get();

        return Inertia::render('Tenant/KartuKeluarga/Show', [
            'kk' => $kk,
            'kartuKeluarga' => $kartuKeluarga,
            'kepalaKeluarga' => $kartuKeluarga->where('kedudukan_keluarga', 'Kepala Keluarga')->first(),
            'anggotaKeluarga' => $kartuKeluarga->where('kedudukan_keluarga', '!=', 'Kepala Keluarga')->values(),
            'nkk' => $nkk,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($nkk)
    {
        $kk = KartuKeluarga::where('nkk', $nkk)->firstOrFail();
        $members = Penduduk::where('kartu_keluarga_id', $kk->id)->get();
        
        if ($members->isEmpty()) {
            abort(404, 'Kartu Keluarga tidak memiliki anggota');
        }

        return Inertia::render('Tenant/KartuKeluarga/Edit', [
            'kk' => $kk,
            'kartuKeluarga' => $members,
            'nkk' => $nkk,
            'masterRwOptions' => $this->kkService->getMasterRwOptions()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $nkk)
    {
        $validated = $request->validate([
            'nama_kepala_keluarga' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'rt_id' => 'required|exists:rts,id',
            'rw_id' => 'required|exists:rws,id',
        ]);

        $kk = KartuKeluarga::where('nkk', $nkk)->firstOrFail();

        try {
            $this->kkService->updateKK($kk, $validated);
            return redirect()->route('kk.show', $nkk)->with('success', 'Kartu Keluarga berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui KK: ' . $e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($nkk)
    {
        Gate::authorize('kependudukan');
        $kk = KartuKeluarga::where('nkk', $nkk)->firstOrFail();

        try {
            $this->kkService->deleteKK($kk);
            return redirect()->route('kk.index')->with('success', 'Kartu Keluarga berhasil dihapus');
        } catch (\Exception $e) {
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
            set_time_limit(900);
            $total = $this->kkService->syncSummary();
            return redirect()->route('kk.index')->with('success', "Audit selesai! {$total} KK telah disinkronisasi.");
        } catch (\Throwable $e) {
            return redirect()->route('kk.index')->with('error', 'Gagal memproses audit data: ' . $e->getMessage());
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
        $tab    = $request->get('tab', 'pending');
        $search = $request->get('search');
        $status = $request->get('status');

        $baseQuery = KartuKeluarga::query()
            ->when($search, fn($q) => $q->where(fn($q) => $q->where('nkk', 'like', "%{$search}%")->orWhere('nama_kepala_keluarga', 'like', "%{$search}%")));

        if ($tab === 'resolved') {
            $kkList = (clone $baseQuery)->where('status_kk', 'resolved')->orderBy('updated_at', 'desc')->paginate(20)->withQueryString();
        } else {
            $kkList = (clone $baseQuery)->bermasalah()->when($status, fn($q) => $q->where('status_kk', $status))->orderBy('kk_bermasalah_sejak', 'asc')->paginate(20)->withQueryString();
        }

        $stats = [
            'bermasalah'           => KartuKeluarga::where('status_kk', 'bermasalah')->count(),
            'bermasalah_sementara' => KartuKeluarga::where('status_kk', 'bermasalah_sementara')->count(),
            'resolved'             => KartuKeluarga::where('status_kk', 'resolved')->count(),
        ];
        $stats['pending_total'] = $stats['bermasalah'] + $stats['bermasalah_sementara'];

        return Inertia::render('Tenant/KartuKeluarga/Bermasalah/Index', [
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
        $kk = KartuKeluarga::where('nkk', $nkk)->firstOrFail();

        if (!$kk->isBermasalah()) {
            return redirect()->route('kk.show', $nkk)->with('info', 'KK ini tidak dalam status bermasalah.');
        }

        $anggotaAktif = Penduduk::where('kartu_keluarga_id', $kk->id)
            ->orderByRaw("FIELD(kedudukan_keluarga,'Istri','Suami','Anak','Menantu','Cucu','Orang Tua','Mertua','Saudara')")
            ->get()
            ->map(fn($a) => tap($a, fn($p) => $p->umur = $p->tanggal_lahir ? \Carbon\Carbon::parse($p->tanggal_lahir)->age : 0));

        return Inertia::render('Tenant/KartuKeluarga/Bermasalah/Show', [
            'kkRecord'       => $kk,
            'nkk'            => $nkk,
            'anggotaAktif'   => $anggotaAktif,
            'mutasiPenyebab' => $kk->mutasiPenyebab()->with('penduduk')->first(),
            'kkSementara'    => $kk->kkSementara,
        ]);
    }

    /**
     * Step 1 — Tunjuk KK Sementara.
     * KK baru ditunjuk, NKK masih lama. Undo mutasi masih bisa dilakukan.
     */
    public function resolveKkSementara(Request $request, $nkk)
    {
        $request->validate(['kandidat_id' => 'required|exists:penduduks,id']);
        $kk = KartuKeluarga::where('nkk', $nkk)->firstOrFail();

        try {
            $this->kkService->resolveKkSementara($kk, $request->kandidat_id);
            return redirect()->route('kk.bermasalah', $nkk)->with('success', 'Kandidat berhasil ditunjuk sebagai Kepala Keluarga sementara.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Step 2 — Finalisasi Permanen dengan NKK Baru (wajib).
     * Setelah ini Undo mutasi asal akan diblokir.
     */
    public function resolveKkPermanen(Request $request, $nkk)
    {
        $request->validate(['nkk_baru' => 'required|string|size:16|unique:kartu_keluargas,nkk']);
        $kk = KartuKeluarga::where('nkk', $nkk)->firstOrFail();

        try {
            $newKk = $this->kkService->resolveKkPermanen($kk, $request->nkk_baru);
            return redirect()->route('kk.show', $newKk->nkk)->with('success', "KK berhasil diselesaikan secara permanen. NKK baru: {$newKk->nkk}.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Batalkan KK Sementara — rollback ke status bermasalah (sebelum Step 2).
     * Digunakan jika admin salah memilih kandidat KK sementara.
     */
    public function batalkanSementara($nkk)
    {
        $kk = KartuKeluarga::where('nkk', $nkk)->firstOrFail();

        try {
            $this->kkService->batalkanSementara($kk);
            return redirect()->route('kk.bermasalah', $nkk)->with('success', 'Penunjukan KK sementara berhasil dibatalkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
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
