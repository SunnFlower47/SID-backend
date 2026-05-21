<?php

namespace App\Http\Controllers\Tenant\Pelayanan;

use App\Http\Controllers\Controller;
use App\Http\Requests\BantuanSosial\StoreBantuanSosialRequest;
use App\Http\Requests\BantuanSosial\UpdateBantuanSosialRequest;
use App\Http\Requests\BantuanSosial\StorePenerimaRequest;
use App\Http\Requests\BantuanSosial\UpdatePenerimaRequest;
use App\Models\BantuanSosial;
use App\Models\PenerimaBantuanSosial;
use App\Services\Pelayanan\BantuanSosialService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BantuanSosialController extends Controller
{
    public function __construct(protected BantuanSosialService $service) {}

    // =========================================================================
    // PROGRAM BANTUAN SOSIAL
    // =========================================================================

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

        return Inertia::render('Tenant/BantuanSosial/Index', [
            'bantuanSosials' => $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString(),
            'stats'          => $this->service->getStats(),
            'filters'        => $filters,
        ]);
    }

    public function create()
    {
        return Inertia::render('Tenant/BantuanSosial/Create');
    }

    public function store(StoreBantuanSosialRequest $request)
    {
        BantuanSosial::create($request->validated());
        return redirect()->route('bantuan-sosial.index')
            ->with('success', 'Program bantuan sosial berhasil ditambahkan!');
    }

    public function show(BantuanSosial $bantuanSosial)
    {
        $bantuanSosial->load(['penerima.penduduk'])->loadCount('penerima');

        return Inertia::render('Tenant/BantuanSosial/Show', [
            'bantuanSosial' => $bantuanSosial,
        ]);
    }

    public function edit(BantuanSosial $bantuanSosial)
    {
        return Inertia::render('Tenant/BantuanSosial/Edit', [
            'bantuanSosial' => $bantuanSosial,
        ]);
    }

    public function update(UpdateBantuanSosialRequest $request, BantuanSosial $bantuanSosial)
    {
        $bantuanSosial->update($request->validated());
        return redirect()->route('bantuan-sosial.index')
            ->with('success', 'Program bantuan sosial berhasil diperbarui!');
    }

    public function destroy(BantuanSosial $bantuanSosial)
    {
        $bantuanSosial->delete();
        return redirect()->route('bantuan-sosial.index')
            ->with('success', 'Program bantuan sosial berhasil dihapus!');
    }

    /**
     * Cek bantuan sosial berdasarkan NIK (API endpoint).
     */
    public function checkByNik(Request $request)
    {
        $request->validate(['nik' => 'required|string|size:16']);

        $result = $this->service->getBantuanByNik($request->nik);

        if (!$result['found']) {
            return response()->json(['success' => false, 'message' => 'NIK tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => ['penduduk' => $result['penduduk'], 'bantuan_sosials' => $result['bantuan']],
        ]);
    }

    // =========================================================================
    // PENERIMA BANTUAN SOSIAL
    // =========================================================================

    public function penerimaIndex(BantuanSosial $bantuanSosial)
    {
        return Inertia::render('Tenant/BantuanSosial/Penerima/Index', [
            'bantuanSosial' => $bantuanSosial,
            'penerima'      => $bantuanSosial->penerima()->with('penduduk')->paginate(15)->withQueryString(),
        ]);
    }

    public function penerimaCreate(BantuanSosial $bantuanSosial)
    {
        if (!$this->service->isEditable($bantuanSosial)) {
            return redirect()->route('bantuan-sosial.penerima.index', $bantuanSosial)
                ->with('error', 'Program bantuan ini telah selesai/kadaluarsa dan tidak dapat menambah penerima lagi.');
        }

        return Inertia::render('Tenant/BantuanSosial/Penerima/Create', [
            'bantuanSosial' => $bantuanSosial,
        ]);
    }

    public function penerimaStore(StorePenerimaRequest $request, BantuanSosial $bantuanSosial)
    {
        if (!$this->service->isEditable($bantuanSosial)) {
            return back()->with('error', 'Gagal: Program bantuan ini telah selesai atau kadaluarsa.');
        }

        try {
            $result = $this->service->storePenerima($bantuanSosial, $request->penduduk_ids, $request);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan penerima: ' . $e->getMessage());
        }

        if ($result['success'] === 0 && $result['skipped'] > 0) {
            return back()->with('error', 'Semua penduduk yang dipilih sudah terdaftar pada program ini.');
        }

        $msg = "Berhasil menambahkan {$result['success']} penerima baru.";
        if ($result['skipped'] > 0) {
            $msg .= " ({$result['skipped']} dilewati karena sudah terdaftar)";
        }

        return redirect()->route('bantuan-sosial.penerima.index', $bantuanSosial)->with('success', $msg);
    }

    public function penerimaShow(BantuanSosial $bantuanSosial, PenerimaBantuanSosial $penerima)
    {
        return Inertia::render('Tenant/BantuanSosial/Penerima/Show', [
            'bantuanSosial' => $bantuanSosial,
            'penerima'      => $penerima->load('penduduk'),
        ]);
    }

    public function penerimaEdit(BantuanSosial $bantuanSosial, PenerimaBantuanSosial $penerima)
    {
        if (!$this->service->isEditable($bantuanSosial)) {
            return redirect()->route('bantuan-sosial.penerima.index', $bantuanSosial)
                ->with('error', 'Program bantuan ini telah selesai/kadaluarsa. Data tidak dapat diubah.');
        }

        return Inertia::render('Tenant/BantuanSosial/Penerima/Edit', [
            'bantuanSosial' => $bantuanSosial,
            'penerima'      => $penerima->load('penduduk'),
        ]);
    }

    public function penerimaUpdate(UpdatePenerimaRequest $request, BantuanSosial $bantuanSosial, PenerimaBantuanSosial $penerima)
    {
        if (!$this->service->isEditable($bantuanSosial)) {
            return back()->with('error', 'Gagal: Program bantuan ini telah selesai atau kadaluarsa.');
        }

        try {
            $this->service->updatePenerima($penerima, $request);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui penerima: ' . $e->getMessage());
        }

        return redirect()->route('bantuan-sosial.penerima.index', $bantuanSosial)
            ->with('success', 'Data penerima berhasil diperbarui!');
    }

    public function penerimaDestroy(BantuanSosial $bantuanSosial, PenerimaBantuanSosial $penerima)
    {
        if (!$this->service->isEditable($bantuanSosial)) {
            return back()->with('error', 'Gagal: Data tidak dapat dihapus karena program telah selesai/kadaluarsa.');
        }

        $penerima->delete();

        return redirect()->route('bantuan-sosial.penerima.index', $bantuanSosial)
            ->with('success', 'Penerima bantuan berhasil dihapus!');
    }
}
