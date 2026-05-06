<?php

namespace App\Http\Controllers\Tenant\Pelayanan;

use App\Http\Controllers\Controller;
use App\Http\Requests\BantuanSosial\StoreBantuanSosialRequest;
use App\Http\Requests\BantuanSosial\UpdateBantuanSosialRequest;
use App\Http\Requests\BantuanSosial\StorePenerimaRequest;
use App\Http\Requests\BantuanSosial\UpdatePenerimaRequest;
use App\Models\BantuanSosial;
use App\Models\PenerimaBantuanSosial;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BantuanSosialController extends Controller
{
    /**
     * Daftar program bantuan sosial.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'jenis_bantuan', 'tahun']);

        $query = BantuanSosial::withCount('penerima');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['jenis_bantuan'])) {
            $query->where('jenis_bantuan', $filters['jenis_bantuan']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nama_program', 'like', "%{$filters['search']}%")
                  ->orWhere('deskripsi', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['tahun'])) {
            $query->where(function ($q) use ($filters) {
                $q->whereYear('tanggal_mulai', $filters['tahun'])
                  ->orWhereYear('tanggal_selesai', $filters['tahun'])
                  ->orWhere('periode', 'like', "%{$filters['tahun']}%");
            });
        }

        $bantuanSosials = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'total_program'  => BantuanSosial::count(),
            'program_aktif'  => BantuanSosial::where('status', 'aktif')->count(),
            'total_penerima' => PenerimaBantuanSosial::count(),
            'penerima_aktif' => PenerimaBantuanSosial::where('status_penerimaan', 'aktif')->count(),
        ];

        return Inertia::render('Tenant/BantuanSosial/Index', [
            'bantuanSosials' => $bantuanSosials,
            'stats'          => $stats,
            'filters'        => $filters,
        ]);
    }

    /**
     * Form tambah program baru.
     */
    public function create()
    {
        return Inertia::render('Tenant/BantuanSosial/Create');
    }

    /**
     * Simpan program baru.
     */
    public function store(StoreBantuanSosialRequest $request)
    {
        DB::beginTransaction();
        try {
            BantuanSosial::create($request->validated());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan program bantuan sosial: ' . $e->getMessage());
        }

        return redirect()->route('bantuan-sosial.index')
            ->with('success', 'Program bantuan sosial berhasil ditambahkan!');
    }

    /**
     * Detail program bantuan sosial.
     */
    public function show(BantuanSosial $bantuanSosial)
    {
        $bantuanSosial->load(['penerima.penduduk']);
        $bantuanSosial->loadCount('penerima');

        return Inertia::render('Tenant/BantuanSosial/Show', [
            'bantuanSosial' => $bantuanSosial,
        ]);
    }

    /**
     * Form edit program.
     */
    public function edit(BantuanSosial $bantuanSosial)
    {
        return Inertia::render('Tenant/BantuanSosial/Edit', [
            'bantuanSosial' => $bantuanSosial,
        ]);
    }

    /**
     * Update program.
     */
    public function update(UpdateBantuanSosialRequest $request, BantuanSosial $bantuanSosial)
    {
        DB::beginTransaction();
        try {
            $bantuanSosial->update($request->validated());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui program bantuan sosial: ' . $e->getMessage());
        }

        return redirect()->route('bantuan-sosial.index')
            ->with('success', 'Program bantuan sosial berhasil diperbarui!');
    }

    /**
     * Hapus program.
     */
    public function destroy(BantuanSosial $bantuanSosial)
    {
        DB::beginTransaction();
        try {
            $bantuanSosial->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus program bantuan sosial: ' . $e->getMessage());
        }

        return redirect()->route('bantuan-sosial.index')
            ->with('success', 'Program bantuan sosial berhasil dihapus!');
    }

    /**
     * Cek bantuan sosial berdasarkan NIK (API).
     */
    public function checkByNik(Request $request)
    {
        $request->validate(['nik' => 'required|string|size:16']);

        $penduduk = Penduduk::where('nik', $request->nik)->first();

        if (!$penduduk) {
            return response()->json(['success' => false, 'message' => 'NIK tidak ditemukan'], 404);
        }

        $bantuanSosials = PenerimaBantuanSosial::with('bantuanSosial')
            ->where('penduduk_id', $penduduk->id)
            ->where('status_penerimaan', 'aktif')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => ['penduduk' => $penduduk, 'bantuan_sosials' => $bantuanSosials],
        ]);
    }

    // =========================================================================
    // PENERIMA BANTUAN SOSIAL
    // =========================================================================

    /**
     * Daftar penerima bantuan untuk program tertentu.
     */
    public function penerimaIndex(BantuanSosial $bantuanSosial)
    {
        $penerima = $bantuanSosial->penerima()
            ->with('penduduk')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Tenant/BantuanSosial/Penerima/Index', [
            'bantuanSosial' => $bantuanSosial,
            'penerima'      => $penerima,
        ]);
    }

    /**
     * Form tambah penerima.
     */
    public function penerimaCreate(BantuanSosial $bantuanSosial)
    {
        return Inertia::render('Tenant/BantuanSosial/Penerima/Create', [
            'bantuanSosial' => $bantuanSosial,
        ]);
    }

    /**
     * Simpan penerima baru.
     */
    public function penerimaStore(StorePenerimaRequest $request, BantuanSosial $bantuanSosial)
    {
        $pendudukIds = $request->penduduk_ids;
        $successCount = 0;
        $skipCount = 0;

        DB::beginTransaction();
        try {
            foreach ($pendudukIds as $pendudukId) {
                // Cek duplikasi penerima
                $existing = PenerimaBantuanSosial::where('bantuan_sosial_id', $bantuanSosial->id)
                    ->where('penduduk_id', $pendudukId)
                    ->first();

                if ($existing) {
                    $skipCount++;
                    continue; // Skip jika sudah ada
                }

                if ($request->sistem_pembayaran === 'sekali') {
                    $bantuanSosial->penerima()->create([
                        'penduduk_id'       => $pendudukId,
                        'nilai_diterima'    => $request->nilai_diterima,
                        'tanggal_penerimaan'=> $request->tanggal_penerimaan,
                        'status_penerimaan' => $request->status_penerimaan,
                        'keterangan'        => $request->keterangan,
                        'data_tambahan'     => json_encode(['sistem_pembayaran' => 'sekali']),
                    ]);
                } else {
                    $total       = (float) $request->nilai_total_berkala;
                    $perTahap    = (int) floor($total / 4);
                    $remainder   = (int) ($total % 4);

                    $bantuanSosial->penerima()->create([
                        'penduduk_id'       => $pendudukId,
                        'nilai_diterima'    => $total,
                        'tanggal_penerimaan'=> $request->tanggal_tahap_1,
                        'status_penerimaan' => $request->status_penerimaan,
                        'keterangan'        => $request->keterangan,
                        'data_tambahan'     => json_encode([
                            'sistem_pembayaran' => 'berkala',
                            'total_amount'      => $total,
                            'tahap_1'           => ['tanggal' => $request->tanggal_tahap_1, 'jumlah' => $perTahap + ($remainder >= 1 ? 1 : 0)],
                            'tahap_2'           => ['tanggal' => $request->tanggal_tahap_2, 'jumlah' => $perTahap + ($remainder >= 2 ? 1 : 0)],
                            'tahap_3'           => ['tanggal' => $request->tanggal_tahap_3, 'jumlah' => $perTahap + ($remainder >= 3 ? 1 : 0)],
                            'tahap_4'           => ['tanggal' => $request->tanggal_tahap_4, 'jumlah' => $perTahap],
                        ]),
                    ]);
                }
                $successCount++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan penerima: ' . $e->getMessage());
        }

        if ($successCount === 0 && $skipCount > 0) {
            return back()->with('error', 'Semua penduduk yang dipilih sudah terdaftar pada program ini.');
        }

        $msg = "Berhasil menambahkan {$successCount} penerima baru.";
        if ($skipCount > 0) {
            $msg .= " ({$skipCount} dilewati karena sudah terdaftar)";
        }

        return redirect()->route('bantuan-sosial.penerima.index', $bantuanSosial)
            ->with('success', $msg);
    }

    /**
     * Detail penerima.
     */
    public function penerimaShow(BantuanSosial $bantuanSosial, PenerimaBantuanSosial $penerima)
    {
        $penerima->load('penduduk');

        return Inertia::render('Tenant/BantuanSosial/Penerima/Show', [
            'bantuanSosial' => $bantuanSosial,
            'penerima'      => $penerima,
        ]);
    }

    /**
     * Form edit penerima.
     */
    public function penerimaEdit(BantuanSosial $bantuanSosial, PenerimaBantuanSosial $penerima)
    {
        $penerima->load('penduduk');

        return Inertia::render('Tenant/BantuanSosial/Penerima/Edit', [
            'bantuanSosial' => $bantuanSosial,
            'penerima'      => $penerima,
        ]);
    }

    /**
     * Update penerima.
     */
    public function penerimaUpdate(UpdatePenerimaRequest $request, BantuanSosial $bantuanSosial, PenerimaBantuanSosial $penerima)
    {
        DB::beginTransaction();
        try {
            if ($request->sistem_pembayaran === 'sekali') {
                $penerima->update([
                    'penduduk_id'       => $request->penduduk_id,
                    'nilai_diterima'    => $request->nilai_diterima,
                    'tanggal_penerimaan'=> $request->tanggal_penerimaan,
                    'status_penerimaan' => $request->status_penerimaan,
                    'keterangan'        => $request->keterangan,
                    'data_tambahan'     => json_encode(['sistem_pembayaran' => 'sekali']),
                ]);
            } else {
                $total       = (float) $request->nilai_total_berkala;
                $perTahap    = (int) floor($total / 4);
                $remainder   = (int) ($total % 4);

                $penerima->update([
                    'penduduk_id'       => $request->penduduk_id,
                    'nilai_diterima'    => $total,
                    'tanggal_penerimaan'=> $request->tanggal_tahap_1,
                    'status_penerimaan' => $request->status_penerimaan,
                    'keterangan'        => $request->keterangan,
                    'data_tambahan'     => json_encode([
                        'sistem_pembayaran' => 'berkala',
                        'total_amount'      => $total,
                        'tahap_1'           => ['tanggal' => $request->tanggal_tahap_1, 'jumlah' => $perTahap + ($remainder >= 1 ? 1 : 0)],
                        'tahap_2'           => ['tanggal' => $request->tanggal_tahap_2, 'jumlah' => $perTahap + ($remainder >= 2 ? 1 : 0)],
                        'tahap_3'           => ['tanggal' => $request->tanggal_tahap_3, 'jumlah' => $perTahap + ($remainder >= 3 ? 1 : 0)],
                        'tahap_4'           => ['tanggal' => $request->tanggal_tahap_4, 'jumlah' => $perTahap],
                    ]),
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui penerima: ' . $e->getMessage());
        }

        return redirect()->route('bantuan-sosial.penerima.index', $bantuanSosial)
            ->with('success', 'Data penerima berhasil diperbarui!');
    }

    /**
     * Hapus penerima.
     */
    public function penerimaDestroy(BantuanSosial $bantuanSosial, PenerimaBantuanSosial $penerima)
    {
        DB::beginTransaction();
        try {
            $penerima->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus penerima: ' . $e->getMessage());
        }

        return redirect()->route('bantuan-sosial.penerima.index', $bantuanSosial)
            ->with('success', 'Penerima bantuan berhasil dihapus!');
    }
}
