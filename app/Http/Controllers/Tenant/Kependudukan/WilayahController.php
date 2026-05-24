<?php

namespace App\Http\Controllers\Tenant\Kependudukan;

use App\Http\Controllers\Controller;
use App\Models\Dusun;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\WilayahChangeLog;
use App\Services\Kependudukan\VillageStatisticsService;
use App\Services\Wilayah\WilayahService;
use App\Traits\WilayahResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class WilayahController extends Controller
{
    use WilayahResolver;

    public function __construct(
        protected VillageStatisticsService $statsService,
        protected WilayahService $wilayahService
    ) {}

    // =========================================================================
    // INDEX & OVERVIEW
    // =========================================================================

    public function index()
    {
        Gate::authorize('settings.view');

        return Inertia::render('Tenant/MasterWilayah/Index', [
            'mapping' => Inertia::defer(fn () => [
                'dusuns' => once(fn () => $this->statsService->getWilayahData())['dusuns'],
                'rws'    => once(fn () => $this->statsService->getWilayahData())['rws'],
                'rts'    => once(fn () => $this->statsService->getWilayahData())['rts'],
            ]),
            'summary'          => Inertia::defer(fn () => once(fn () => $this->statsService->getWilayahData())['summary']),
            'recentChangeLogs' => Inertia::defer(fn () => $this->statsService->getRecentWilayahLogs()),
        ]);
    }

    public function detailRtPenduduk(Request $request, Rt $rt)
    {
        Gate::authorize('settings.view');

        return Inertia::render('Tenant/MasterWilayah/RtDetail', [
            'rt'        => $rt->load(['rw', 'dusun']),
            'penduduks' => Inertia::defer(fn () => Penduduk::query()
                ->withWilayah()
                ->whereHas('kartuKeluarga', fn ($q) => $q->where('rt_id', $rt->id))
                ->when($request->search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%")
                          ->orWhere('nik', 'like', "%{$search}%");
                    });
                })
                ->orderBy('nama')
                ->paginate(50)
                ->withQueryString()),
        ]);
    }

    // =========================================================================
    // DUSUN CRUD
    // =========================================================================

    public function storeDusun(Request $request)
    {
        Gate::authorize('settings.view');

        $data = $request->validate([
            'nama' => 'required|string|max:100|unique:dusuns,nama',
            'kode' => 'nullable|string|max:20|unique:dusuns,kode',
        ]);

        Dusun::create([
            'nama'               => trim($data['nama']),
            'kode'               => isset($data['kode']) ? trim($data['kode']) : null,
            'is_active'          => true,
            'is_auto_generated'  => false,
            'needs_review'       => false,
        ]);

        return back()->with('success', 'Dusun berhasil ditambahkan.');
    }

    public function updateDusun(Request $request, Dusun $dusun)
    {
        Gate::authorize('settings.view');

        $data = $request->validate([
            'nama'         => 'required|string|max:100|unique:dusuns,nama,' . $dusun->id,
            'kode'         => 'nullable|string|max:20|unique:dusuns,kode,' . $dusun->id,
            'is_active'    => 'nullable|boolean',
            'needs_review' => 'nullable|boolean',
        ]);

        $dusun->update([
            'nama'         => trim($data['nama']),
            'kode'         => isset($data['kode']) && $data['kode'] !== '' ? trim($data['kode']) : null,
            'is_active'    => (bool) ($data['is_active'] ?? false),
            'needs_review' => (bool) ($data['needs_review'] ?? false),
        ]);

        return back()->with('success', 'Dusun berhasil diperbarui.');
    }

    public function destroyDusun(Request $request, Dusun $dusun)
    {
        Gate::authorize('settings.view');

        if ($error = $this->wilayahService->canDeleteDusun($dusun)) {
            return back()->with('error', $error);
        }

        try {
            $dusun->delete();
            return back()->with('success', 'Dusun berhasil dihapus permanen.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus Dusun: ' . $e->getMessage());
        }
    }

    public function previewImpactDusun(Request $request, Dusun $dusun)
    {
        Gate::authorize('settings.view');
        $data = $request->validate(['nama' => 'required|string|max:100']);

        return response()->json($this->wilayahService->previewDusunImpact($dusun, $data['nama']));
    }

    // =========================================================================
    // RW CRUD
    // =========================================================================

    public function storeRw(Request $request)
    {
        Gate::authorize('settings.view');

        $data = $request->validate([
            'kode' => 'required|string|max:3|unique:rws,kode',
            'nama' => 'nullable|string|max:100',
        ]);

        $kode = $this->wilayahService->normalizeKode($data['kode']);

        Rw::create([
            'kode'              => $kode,
            'nama'              => !empty($data['nama'] ?? null) ? trim($data['nama']) : "RW {$kode}",
            'is_active'         => true,
            'is_auto_generated' => false,
            'needs_review'      => false,
        ]);

        return back()->with('success', 'RW berhasil ditambahkan.');
    }

    public function updateRw(Request $request, Rw $rw)
    {
        Gate::authorize('settings.view');

        $data = $request->validate([
            'kode'         => 'required|string|max:3|unique:rws,kode,' . $rw->id,
            'nama'         => 'nullable|string|max:100',
            'is_active'    => 'nullable|boolean',
            'needs_review' => 'nullable|boolean',
        ]);

        $kode = $this->wilayahService->normalizeKode($data['kode']);

        $rw->update([
            'kode'         => $kode,
            'nama'         => !empty($data['nama'] ?? null) ? trim($data['nama']) : "RW {$kode}",
            'is_active'    => (bool) ($data['is_active'] ?? false),
            'needs_review' => (bool) ($data['needs_review'] ?? false),
        ]);

        return back()->with('success', 'RW berhasil diperbarui.');
    }

    public function destroyRw(Request $request, Rw $rw)
    {
        Gate::authorize('settings.view');

        if ($error = $this->wilayahService->canDeleteRw($rw)) {
            return back()->with('error', $error);
        }

        try {
            $rw->delete();
            return back()->with('success', 'RW berhasil dihapus permanen.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus RW: ' . $e->getMessage());
        }
    }

    public function previewImpactRw(Request $request, Rw $rw)
    {
        Gate::authorize('settings.view');
        $data = $request->validate(['kode' => 'required|string|max:3']);

        return response()->json($this->wilayahService->previewRwImpact($rw, $data['kode']));
    }

    // =========================================================================
    // RT CRUD
    // =========================================================================

    public function storeRt(Request $request)
    {
        Gate::authorize('settings.view');

        $data = $request->validate([
            'kode'     => 'required|string|max:3',
            'rw_id'    => 'required|exists:rws,id',
            'dusun_id' => 'nullable|exists:dusuns,id',
            'nama'     => 'nullable|string|max:100',
        ]);

        $kode = $this->wilayahService->normalizeKode($data['kode']);

        Rt::updateOrCreate(
            ['kode' => $kode, 'rw_id' => $data['rw_id']],
            [
                'dusun_id'          => $data['dusun_id'] ?? null,
                'nama'              => !empty($data['nama'] ?? null) ? trim($data['nama']) : "RT {$kode}",
                'is_active'         => true,
                'is_auto_generated' => false,
                'needs_review'      => false,
            ]
        );

        return back()->with('success', 'RT berhasil ditambahkan/diperbarui.');
    }

    public function updateRt(Request $request, Rt $rt)
    {
        Gate::authorize('settings.view');

        // Guard: perubahan hierarki (pindah RW/Dusun) harus lewat preview impact
        if (($request->has('rw_id') && $request->rw_id != $rt->rw_id) ||
            ($request->has('dusun_id') && $request->dusun_id != $rt->dusun_id)) {
            return back()->with('error', 'Untuk mengubah hierarki RT (pindah RW atau Dusun), gunakan fitur Preview Impact.');
        }

        $data = $request->validate([
            'kode'         => 'required|string|max:3',
            'dusun_id'     => 'nullable|exists:dusuns,id',
            'nama'         => 'nullable|string|max:100',
            'is_active'    => 'nullable|boolean',
            'needs_review' => 'nullable|boolean',
        ]);

        $kode = $this->wilayahService->normalizeKode($data['kode']);

        $rt->update([
            'kode'         => $kode,
            'nama'         => !empty($data['nama'] ?? null) ? trim($data['nama']) : "RT {$kode}",
            'is_active'    => (bool) ($data['is_active'] ?? false),
            'needs_review' => (bool) ($data['needs_review'] ?? false),
        ]);

        return back()->with('success', 'RT berhasil diperbarui (Info Dasar).');
    }

    public function destroyRt(Request $request, Rt $rt)
    {
        Gate::authorize('settings.view');

        if ($error = $this->wilayahService->canDeleteRt($rt)) {
            return back()->with('error', $error);
        }

        try {
            $rt->delete();
            return back()->with('success', "RT {$rt->kode} berhasil dihapus.");
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Gagal menghapus RT karena batasan integritas database. Pastikan tidak ada data lain yang menggunakan RT ini.');
        }
    }

    public function previewImpactRt(Request $request, Rt $rt)
    {
        Gate::authorize('settings.view');

        $data = $request->validate([
            'kode'         => 'required|string|max:3',
            'rw_id'        => 'required|exists:rws,id',
            'dusun_id'     => 'nullable|exists:dusuns,id',
            'nama'         => 'nullable|string|max:100',
            'is_active'    => 'nullable|boolean',
            'needs_review' => 'nullable|boolean',
        ]);

        $payload = $this->wilayahService->previewRtImpact($rt, $data);

        if ($request->expectsJson()) {
            return response()->json($payload);
        }

        return back()->with('preview_impact', $payload);
    }

    public function applyRtUpdate(Request $request, Rt $rt)
    {
        Gate::authorize('settings.view');

        $data = $request->validate([
            'preview_token' => 'required|string',
            'kode'          => 'required|string|max:3',
            'rw_id'         => 'required|exists:rws,id',
            'dusun_id'      => 'nullable|exists:dusuns,id',
            'nama'          => 'nullable|string|max:100',
            'is_active'     => 'nullable|boolean',
            'needs_review'  => 'nullable|boolean',
        ]);

        $previewStore = session('wilayah_preview_rt.' . $data['preview_token']);
        if (!$previewStore || (int) ($previewStore['rt_id'] ?? 0) !== (int) $rt->id) {
            return back()->with('error', 'Token preview tidak valid/expired. Silakan preview ulang sebelum apply.');
        }

        $kode   = $this->wilayahService->normalizeKode($data['kode']);
        $exists = Rt::where('kode', $kode)->where('rw_id', $data['rw_id'])->where('id', '!=', $rt->id)->exists();
        if ($exists) {
            return back()->with('error', 'RT dengan kode tersebut sudah ada di RW yang sama.');
        }

        $log = $this->wilayahService->applyRtChange($rt, $data, optional($request->user())->id);

        session()->forget('wilayah_preview_rt.' . $data['preview_token']);

        return redirect()->route('settings.wilayah.index')
            ->with('success', 'RT berhasil diupdate dengan auto-backup.')
            ->with('last_wilayah_change_log_id', $log?->id);
    }

    public function rollbackWilayahChange(Request $request, WilayahChangeLog $log)
    {
        Gate::authorize('settings.view');

        if ($log->entity_type !== 'rt' || $log->status !== 'applied') {
            return back()->with('error', 'Rollback tidak tersedia untuk log ini.');
        }

        try {
            $this->wilayahService->rollbackRtChange($log, optional($request->user())->id);
            return back()->with('success', 'Rollback berhasil, data dikembalikan ke snapshot sebelum perubahan.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
