<?php

namespace App\Http\Controllers\ApiAdminPanel;

use App\Models\Mutasi;
use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\JsonResponse;

class KartuKeluargaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('view', KartuKeluarga::class);

        $search = $request->get('search');
        $status = $request->get('status', 'all');

        $query = KartuKeluarga::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nkk', 'like', "%{$search}%")
                  ->orWhere('nama_kepala_keluarga', 'like', "%{$search}%");
            });
        }

        if ($status === 'aktif') {
            $query->where('anggota_aktif', '>', 0);
        } elseif ($status === 'bermasalah') {
            $query->bermasalah();
        } elseif ($status === 'kosong') {
            $query->where('anggota_aktif', 0);
        }

        $kartuKeluarga = $query->orderBy('updated_at', 'desc')->paginate($request->get('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $kartuKeluarga,
            'meta' => [
                'stats' => [
                    'total'       => KartuKeluarga::count(),
                    'aktif'       => KartuKeluarga::where('anggota_aktif', '>', 0)->count(),
                    'bermasalah'  => KartuKeluarga::bermasalah()->count(),
                    'kosong'      => KartuKeluarga::where('anggota_aktif', 0)->count(),
                ]
            ]
        ]);
    }

    /**
     * Dedicated index for Problematic KK.
     */
    public function indexBermasalah(Request $request): JsonResponse
    {
        Gate::authorize('view', KartuKeluarga::class);

        $search = $request->get('search');
        $tab = $request->get('tab', 'pending'); // pending | resolved

        $query = KartuKeluarga::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nkk', 'like', "%{$search}%")
                  ->orWhere('nama_kepala_keluarga', 'like', "%{$search}%");
            });
        }

        if ($tab === 'resolved') {
            $query->where('status_kk', 'resolved');
        } else {
            $query->bermasalah();
        }

        $kartuKeluarga = $query->orderBy('updated_at', 'desc')->paginate($request->get('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $kartuKeluarga,
            'meta' => [
                'stats' => [
                    'bermasalah' => KartuKeluarga::where('status_kk', 'bermasalah')->count(),
                    'bermasalah_sementara' => KartuKeluarga::where('status_kk', 'bermasalah_sementara')->count(),
                    'resolved' => KartuKeluarga::where('status_kk', 'resolved')->count(),
                ]
            ]
        ]);
    }

    /**
     * Store a newly created resource (Simplified).
     */
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create', KartuKeluarga::class);

        $request->validate([
            'nkk' => 'required|string|size:16|unique:kartu_keluargas,nkk',
            'nik_kepala_keluarga' => 'required|string|size:16|unique:penduduks,nik',
            'nama_kepala_keluarga' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:LAKI-LAKI,PEREMPUAN',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'agama' => 'required|string|max:50',
            'status_perkawinan' => 'required|string|max:50',
            'pekerjaan' => 'required|string|max:100',
            'pendidikan' => 'nullable|string|max:100',
            'alamat' => 'required|string|max:500',
            'rt_id' => 'required|exists:rts,id',
            'rw_id' => 'required|exists:rws,id',
            'dusun_id' => 'required|exists:dusuns,id',
        ]);

        DB::beginTransaction();
        try {
            // 1. Create the Kartu Keluarga first (The Source of Truth)
            $kk = KartuKeluarga::create([
                'nkk' => $request->nkk,
                'nama_kepala_keluarga' => $request->nama_kepala_keluarga,
                'nik_kepala_keluarga' => $request->nik_kepala_keluarga,
                'alamat' => $request->alamat,
                'rt_id' => $request->rt_id,
                'rw_id' => $request->rw_id,
                'dusun_id' => $request->dusun_id,
            ]);

            // 2. Create the Kepala Keluarga and link via ID
            $penduduk = Penduduk::create([
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
            return response()->json([
                'status' => 'success',
                'message' => 'Kartu Keluarga berhasil dibuat',
                'data' => $penduduk
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat Kartu Keluarga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource details.
     */
    public function show($nkk): JsonResponse
    {
        Gate::authorize('view', KartuKeluarga::class);

        $summary = KartuKeluarga::where('nkk', $nkk)->first();
        if (!$summary) {
            return response()->json(['status' => 'error', 'message' => 'Kartu Keluarga tidak ditemukan'], 404);
        }

        $members = Penduduk::withTrashed()->where('kartu_keluarga_id', $summary->id)
            ->orderByRaw("CASE 
                WHEN kedudukan_keluarga = 'Kepala Keluarga' THEN 1 
                ELSE 2 
                END")
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'summary' => $summary,
                'members' => $members
            ]
        ]);
    }

    /**
     * Update KK details (via Kepala Keluarga).
     */
    public function update(Request $request, $nkk): JsonResponse
    {
        Gate::authorize('update', KartuKeluarga::class);

        $request->validate([
            'nama_kepala_keluarga' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'rt_id' => 'required|exists:rts,id',
            'rw_id' => 'required|exists:rws,id',
            'dusun_id' => 'required|exists:dusuns,id',
        ]);

        $kkRecord = KartuKeluarga::where('nkk', $nkk)->firstOrFail();
        
        $kkRecord->update([
            'nama_kepala_keluarga' => $request->nama_kepala_keluarga,
            'alamat' => $request->alamat,
            'rt_id' => $request->rt_id,
            'rw_id' => $request->rw_id,
            'dusun_id' => $request->dusun_id,
        ]);

        $kepalaKeluarga = Penduduk::where('kartu_keluarga_id', $kkRecord->id)
            ->where('kedudukan_keluarga', 'Kepala Keluarga')
            ->first();

        if ($kepalaKeluarga) {
            $kepalaKeluarga->update(['nama' => $request->nama_kepala_keluarga]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data Kartu Keluarga berhasil diperbarui'
        ]);
    }

    /**
     * Delete entire KK.
     */
    public function destroy($nkk): JsonResponse
    {
        Gate::authorize('delete', KartuKeluarga::class);

        try {
            $kk = KartuKeluarga::where('nkk', $nkk)->firstOrFail();
            Penduduk::where('kartu_keluarga_id', $kk->id)->delete();
            $kk->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Kartu Keluarga berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus KK: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Step 1 Resolution - Assign Temporary Head of Family.
     */
    public function resolveKkSementara(Request $request, $nkk): JsonResponse
    {
        $request->validate(['kandidat_id' => 'required|exists:penduduks,id']);

        $kkRecord = KartuKeluarga::where('nkk', $nkk)->firstOrFail();
        if ($kkRecord->status_kk !== 'bermasalah') {
            return response()->json(['status' => 'error', 'message' => 'Status KK tidak sesuai'], 422);
        }

        $kandidat = Penduduk::findOrFail($request->kandidat_id);

        DB::beginTransaction();
        try {
            // Save current position for rollback
            if ($kkRecord->mutasi_penyebab_id) {
                $mutasi = Mutasi::find($kkRecord->mutasi_penyebab_id);
                if ($mutasi) {
                    $detail = $mutasi->detail_tambahan ?? [];
                    $detail['kk_sementara_id'] = $kandidat->id;
                    $detail['kk_sementara_kedudukan_asal'] = $kandidat->kedudukan_keluarga;
                    $mutasi->update(['detail_tambahan' => $detail]);
                }
            }

            $kandidat->update(['kedudukan_keluarga' => 'Kepala Keluarga']);
            $kkRecord->update(['status_kk' => 'bermasalah_sementara', 'kk_sementara_id' => $kandidat->id]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => "{$kandidat->nama} ditunjuk sebagai KK sementara"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Step 2 Resolution - Finalize with New NKK.
     */
    public function resolveKkPermanen(Request $request, $nkk): JsonResponse
    {
        $request->validate([
            'nkk_baru' => 'required|string|size:16|unique:kartu_keluargas,nkk',
        ]);

        $kkRecord = KartuKeluarga::where('nkk', $nkk)->firstOrFail();
        if ($kkRecord->status_kk !== 'bermasalah_sementara') {
            return response()->json(['status' => 'error', 'message' => 'Step 1 belum selesai'], 422);
        }

        $nkkBaru = $request->nkk_baru;

        DB::beginTransaction();
        try {
            // Update NKK on the KartuKeluarga record (Source of Truth)
            // Accessor on Penduduk will handle the display
            $kkRecord->update([
                'nkk' => $nkkBaru,
                'status_kk' => 'normal',
                'kk_sementara_id' => null
            ]);

            // Mark the mutation cause so Undo is blocked
            if ($kkRecord->mutasi_penyebab_id) {
                $mutasi = Mutasi::find($kkRecord->mutasi_penyebab_id);
                if ($mutasi) {
                    $detail = $mutasi->detail_tambahan ?? [];
                    $detail['kk_sudah_diselesaikan'] = true;
                    $detail['nkk_baru'] = $nkkBaru;
                    $mutasi->update(['detail_tambahan' => $detail]);
                }
            }

            $kkRecord->update(['status_kk' => 'resolved', 'anggota_aktif' => 0]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'KK berhasil diselesaikan secara permanen', 'nkk_baru' => $nkkBaru]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Cancel Temporary Resolution (Undo Step 1).
     */
    public function batalkanSementara($nkk): JsonResponse
    {
        $kkRecord = KartuKeluarga::where('nkk', $nkk)->firstOrFail();

        if ($kkRecord->status_kk !== 'bermasalah_sementara') {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada resolusi sementara yang bisa dibatalkan'], 422);
        }

        DB::beginTransaction();
        try {
            // Restore original position of the temporary head
            $mutasiPenyebab = Mutasi::find($kkRecord->mutasi_penyebab_id);
            if ($mutasiPenyebab && isset($mutasiPenyebab->detail_tambahan['kk_sementara_id'])) {
                $kandidatId = $mutasiPenyebab->detail_tambahan['kk_sementara_id'];
                $kedudukanAsal = $mutasiPenyebab->detail_tambahan['kk_sementara_kedudukan_asal'] ?? 'Anggota Keluarga';
                
                Penduduk::where('id', $kandidatId)->update(['kedudukan_keluarga' => $kedudukanAsal]);
                
                // Clear the temporary info from mutasi
                $detail = $mutasiPenyebab->detail_tambahan;
                unset($detail['kk_sementara_id'], $detail['kk_sementara_kedudukan_asal']);
                $mutasiPenyebab->update(['detail_tambahan' => $detail]);
            }

            // Reset KK record
            $kkRecord->update([
                'status_kk' => 'bermasalah',
                'kk_sementara_id' => null
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Penunjukan KK sementara berhasil dibatalkan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Sync Summary table with Penduduk table.
     */
    public function sync(): JsonResponse
    {
        Gate::authorize('kartu_keluarga.delete');
        try {
            Artisan::call('sync:kartu-keluarga');
            Artisan::call('kk:scan-historis', ['--force' => true]);
            return response()->json(['status' => 'success', 'message' => 'Sinkronisasi berhasil']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
