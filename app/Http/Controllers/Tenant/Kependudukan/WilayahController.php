<?php

namespace App\Http\Controllers\Tenant\Kependudukan;

use App\Http\Controllers\Controller;

use Inertia\Inertia;
use App\Models\Dusun;
use App\Models\KartuKeluarga;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\PendudukDomisili;
use App\Models\WilayahChangeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Traits\WilayahResolver;
use App\Services\VillageStatisticsService;

class WilayahController extends Controller
{
    use WilayahResolver;

    protected $statsService;

    public function __construct(VillageStatisticsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function index()
    {
        Gate::authorize('admin_sistem');

        return Inertia::render('Tenant/MasterWilayah/Index', [
            'mapping' => Inertia::defer(fn() => [
                'dusuns' => once(fn() => $this->statsService->getWilayahData())['dusuns'],
                'rws' => once(fn() => $this->statsService->getWilayahData())['rws'],
                'rts' => once(fn() => $this->statsService->getWilayahData())['rts']
            ]),
            'summary' => Inertia::defer(fn() => once(fn() => $this->statsService->getWilayahData())['summary']),
            'recentChangeLogs' => Inertia::defer(fn() => $this->statsService->getRecentWilayahLogs())
        ]);
    }

    public function storeDusun(Request $request)
    {
        Gate::authorize('admin_sistem');

        $data = $request->validate([
            'nama' => 'required|string|max:100|unique:dusuns,nama',
            'kode' => 'nullable|string|max:20|unique:dusuns,kode',
        ]);

        Dusun::create([
            'nama' => trim($data['nama']),
            'kode' => isset($data['kode']) ? trim($data['kode']) : null,
            'is_active' => true,
            'is_auto_generated' => false,
            'needs_review' => false,
        ]);

        return back()->with('success', 'Dusun berhasil ditambahkan.');
    }

    public function updateDusun(Request $request, Dusun $dusun)
    {
        Gate::authorize('admin_sistem');

        $data = $request->validate([
            'nama' => 'required|string|max:100|unique:dusuns,nama,' . $dusun->id,
            'kode' => 'nullable|string|max:20|unique:dusuns,kode,' . $dusun->id,
            'is_active' => 'nullable|boolean',
            'needs_review' => 'nullable|boolean',
        ]);

        $dusun->update([
            'nama' => trim($data['nama']),
            'kode' => isset($data['kode']) && $data['kode'] !== '' ? trim($data['kode']) : null,
            'is_active' => (bool)($data['is_active'] ?? false),
            'needs_review' => (bool)($data['needs_review'] ?? false),
        ]);

        return back()->with('success', 'Dusun berhasil diperbarui.');
    }

    public function storeRw(Request $request)
    {
        Gate::authorize('admin_sistem');

        $data = $request->validate([
            'kode' => 'required|string|max:3|unique:rws,kode',
            'nama' => 'nullable|string|max:100',
        ]);

        $kode = str_pad(preg_replace('/[^0-9]/', '', $data['kode']), 3, '0', STR_PAD_LEFT);

        Rw::create([
            'kode' => $kode,
            'nama' => !empty($data['nama'] ?? null) ? trim($data['nama']) : "RW {$kode}",
            'is_active' => true,
            'is_auto_generated' => false,
            'needs_review' => false,
        ]);

        return back()->with('success', 'RW berhasil ditambahkan.');
    }

    public function updateRw(Request $request, Rw $rw)
    {
        Gate::authorize('admin_sistem');

        $data = $request->validate([
            'kode' => 'required|string|max:3|unique:rws,kode,' . $rw->id,
            'nama' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
            'needs_review' => 'nullable|boolean',
        ]);

        $kode = str_pad(preg_replace('/[^0-9]/', '', $data['kode']), 3, '0', STR_PAD_LEFT);

        $rw->update([
            'kode' => $kode,
            'nama' => !empty($data['nama'] ?? null) ? trim($data['nama']) : "RW {$kode}",
            'is_active' => (bool)($data['is_active'] ?? false),
            'needs_review' => (bool)($data['needs_review'] ?? false),
        ]);

        return back()->with('success', 'RW berhasil diperbarui.');
    }

    public function storeRt(Request $request)
    {
        Gate::authorize('admin_sistem');

        $data = $request->validate([
            'kode' => 'required|string|max:3',
            'rw_id' => 'required|exists:rws,id',
            'dusun_id' => 'nullable|exists:dusuns,id',
            'nama' => 'nullable|string|max:100',
        ]);

        $kode = str_pad(preg_replace('/[^0-9]/', '', $data['kode']), 3, '0', STR_PAD_LEFT);

        Rt::updateOrCreate(
            [
                'kode' => $kode,
                'rw_id' => $data['rw_id'],
            ],
            [
                'dusun_id' => $data['dusun_id'] ?? null,
                'nama' => !empty($data['nama'] ?? null) ? trim($data['nama']) : "RT {$kode}",
                'is_active' => true,
                'is_auto_generated' => false,
                'needs_review' => false,
            ]
        );

        return back()->with('success', 'RT berhasil ditambahkan/diperbarui.');
    }

    public function updateRt(Request $request, Rt $rt)
    {
        Gate::authorize('admin_sistem');

        // Allow minor updates (nama, is_active) but prevent structural changes (rw_id)
        if (($request->has('rw_id') && $request->rw_id != $rt->rw_id) || 
            ($request->has('dusun_id') && $request->dusun_id != $rt->dusun_id)) {
            return back()->with('error', 'Untuk mengubah hierarki RT (pindah RW atau Dusun), gunakan fitur Preview Impact.');
        }

        $data = $request->validate([
            'kode' => 'required|string|max:3',
            'dusun_id' => 'nullable|exists:dusuns,id',
            'nama' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
            'needs_review' => 'nullable|boolean',
        ]);

        $kode = str_pad(preg_replace('/[^0-9]/', '', $data['kode']), 3, '0', STR_PAD_LEFT);

        $rt->update([
            'kode' => $kode,
            'nama' => !empty($data['nama'] ?? null) ? trim($data['nama']) : "RT {$kode}",
            'is_active' => (bool)($data['is_active'] ?? false),
            'needs_review' => (bool)($data['needs_review'] ?? false),
        ]);

        return back()->with('success', 'RT berhasil diperbarui (Info Dasar).');
    }

    public function applyRtUpdate(Request $request, Rt $rt)
    {
        Gate::authorize('admin_sistem');

        $data = $request->validate([
            'preview_token' => 'required|string',
            'kode' => 'required|string|max:3',
            'rw_id' => 'required|exists:rws,id',
            'dusun_id' => 'nullable|exists:dusuns,id',
            'nama' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
            'needs_review' => 'nullable|boolean',
        ]);

        $previewStore = session('wilayah_preview_rt.' . $data['preview_token']);
        if (!$previewStore || (int)($previewStore['rt_id'] ?? 0) !== (int)$rt->id) {
            return back()->with('error', 'Token preview tidak valid/expired. Silakan preview ulang sebelum apply.');
        }

        $kode = str_pad(preg_replace('/[^0-9]/', '', $data['kode']), 3, '0', STR_PAD_LEFT);
        $exists = Rt::where('kode', $kode)
            ->where('rw_id', $data['rw_id'])
            ->where('id', '!=', $rt->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'RT dengan kode tersebut sudah ada di RW yang sama.');
        }

        $oldRt = trim((string)$rt->kode);
        $oldRw = trim((string)optional($rt->rw)->kode);
        $oldDusun = optional($rt->dusun)->nama;

        $newRw = optional(Rw::find($data['rw_id']))->kode;
        $newDusun = !empty($data['dusun_id']) ? optional(Dusun::find($data['dusun_id']))->nama : null;

        $affectedRows = Penduduk::query()
            ->whereHas('kartuKeluarga', fn($q) => $q->where('rt_id', $rt->id))
            ->get(['id', 'kartu_keluarga_id'])
            ->toArray();

        $backupPayload = [
            'rt_before' => [
                'id' => $rt->id,
                'kode' => $oldRt,
                'rw_id' => $rt->rw_id,
                'dusun_id' => $rt->dusun_id,
                'nama' => $rt->nama,
                'is_active' => (bool)$rt->is_active,
                'needs_review' => (bool)$rt->needs_review,
            ],
            'penduduk_before' => $affectedRows,
        ];

        $log = null;
        DB::transaction(function () use ($request, $rt, $data, $kode, $newRw, $newDusun, $oldRt, $oldRw, $oldDusun, $affectedRows, $backupPayload, &$log) {
            $rt->update([
                'kode' => $kode,
                'rw_id' => $data['rw_id'],
                'dusun_id' => $data['dusun_id'] ?? null,
                'nama' => !empty($data['nama'] ?? null) ? trim($data['nama']) : "RT {$kode}",
                'is_active' => (bool)($data['is_active'] ?? false),
                'needs_review' => (bool)($data['needs_review'] ?? false),
            ]);

            // In the relational architecture, we update the KartuKeluarga (Source of Truth)
            // and all linked inhabitants automatically reflect this change via accessors.
            $kkAffected = \App\Models\KartuKeluarga::where('rt_id', $rt->id)->get();
            foreach ($kkAffected as $kk) {
                $kk->update([
                    'rw_id' => $data['rw_id'],
                    'dusun_id' => $data['dusun_id'] ?? null,
                ]);
            }

            $log = WilayahChangeLog::create([
                'entity_type' => 'rt',
                'entity_id' => $rt->id,
                'action' => 'update_with_backup',
                'user_id' => optional($request->user())->id,
                'preview_token' => (string)$data['preview_token'],
                'before_payload' => ['rt' => $oldRt, 'rw' => $oldRw, 'dusun' => $oldDusun],
                'after_payload' => ['rt' => $kode, 'rw' => $newRw, 'dusun' => $newDusun],
                'backup_payload' => $backupPayload,
                'affected_count' => count($affectedRows),
                'status' => 'applied',
                'applied_at' => now(),
            ]);
        });

        session()->forget('wilayah_preview_rt.' . $data['preview_token']);

        return redirect()->route('settings.wilayah.index')
            ->with('success', 'RT berhasil diupdate dengan auto-backup.')
            ->with('last_wilayah_change_log_id', $log?->id);
    }

    public function rollbackWilayahChange(Request $request, WilayahChangeLog $log)
    {
        Gate::authorize('admin_sistem');

        if ($log->entity_type !== 'rt' || $log->status !== 'applied') {
            return back()->with('error', 'Rollback tidak tersedia untuk log ini.');
        }

        $backup = $log->backup_payload ?? [];
        $rtBefore = $backup['rt_before'] ?? null;
        $pendudukBefore = $backup['penduduk_before'] ?? [];

        if (!$rtBefore) {
            return back()->with('error', 'Backup payload tidak lengkap.');
        }

        DB::transaction(function () use ($request, $log, $rtBefore, $pendudukBefore) {
            $rt = Rt::find($rtBefore['id']);
            if ($rt) {
                $rt->update([
                    'kode' => $rtBefore['kode'],
                    'rw_id' => $rtBefore['rw_id'],
                    'dusun_id' => $rtBefore['dusun_id'],
                    'nama' => $rtBefore['nama'],
                    'is_active' => (bool)$rtBefore['is_active'],
                    'needs_review' => (bool)$rtBefore['needs_review'],
                ]);
            }

            // Kembalikan hirarki wilayah di Kartu Keluarga (Source of Truth)
            $kkAffected = \App\Models\KartuKeluarga::where('rt_id', $rtBefore['id'])->get();
            foreach ($kkAffected as $kk) {
                $kk->update([
                    'rw_id' => $rtBefore['rw_id'],
                    'dusun_id' => $rtBefore['dusun_id'],
                ]);
            }

            $log->update([
                'status' => 'rolled_back',
                'rolled_back_at' => now(),
                'rolled_back_by' => optional($request->user())->id,
            ]);
        });


        return back()->with('success', 'Rollback berhasil, data dikembalikan ke snapshot sebelum perubahan.');
    }

    public function previewImpactDusun(Request $request, Dusun $dusun)
    {
        Gate::authorize('admin_sistem');

        $data = $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        $oldNama = trim((string) $dusun->nama);
        $newNama = trim((string) $data['nama']);

        $query = Penduduk::query()->whereHas('kartuKeluarga', fn($q) => $q->where('dusun_id', $dusun->id));

        return response()->json([
            'entity' => 'dusun',
            'id' => $dusun->id,
            'before' => ['nama' => $oldNama],
            'after' => ['nama' => $newNama],
            'will_change' => $oldNama !== $newNama,
            'affected_count' => $query->count(),
            'sample' => $query->withWilayah()->limit(10)->get(),
        ]);
    }

    public function previewImpactRw(Request $request, Rw $rw)
    {
        Gate::authorize('admin_sistem');

        $data = $request->validate([
            'kode' => 'required|string|max:3',
        ]);

        $oldKode = trim((string) $rw->kode);
        $newKode = str_pad(preg_replace('/\D+/', '', $data['kode']), 3, '0', STR_PAD_LEFT);

        $query = Penduduk::query()->whereHas('kartuKeluarga', fn($q) => $q->where('rw_id', $rw->id));

        return response()->json([
            'entity' => 'rw',
            'id' => $rw->id,
            'before' => ['kode' => $oldKode],
            'after' => ['kode' => $newKode],
            'will_change' => $oldKode !== $newKode,
            'affected_count' => $query->count(),
            'sample' => $query->withWilayah()->limit(10)->get(),
        ]);
    }

    public function detailRtPenduduk(Request $request, Rt $rt)
    {
        Gate::authorize('admin_sistem');

        return Inertia::render('Tenant/Master/Wilayah/RtDetail', [
            'rt' => $rt->load(['rw', 'dusun']),
            'penduduks' => Inertia::defer(fn() => Penduduk::query()
                ->withWilayah()
                ->whereHas('kartuKeluarga', fn($q) => $q->where('rt_id', $rt->id))
                ->orderBy('nama')
                ->paginate(50)
                ->withQueryString())
        ]);
    }
    public function destroyDusun(Request $request, Dusun $dusun)
    {
        Gate::authorize('admin_sistem');

        $usedByRt = Rt::where('dusun_id', $dusun->id)->count();
        if ($usedByRt > 0) {
            return back()->with('error', "Dusun {$dusun->nama} tidak bisa dihapus karena masih digunakan oleh {$usedByRt} RT. Ubah dulu RT yang menggunakan Dusun ini.");
        }

        try {
            $dusun->delete();
            return back()->with('success', 'Dusun berhasil dihapus permanen.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus Dusun: ' . $e->getMessage());
        }
    }

    public function destroyRw(Request $request, Rw $rw)
    {
        Gate::authorize('admin_sistem');

        $usedByRt = Rt::where('rw_id', $rw->id)->count();
        if ($usedByRt > 0) {
            return back()->with('error', "RW {$rw->kode} tidak bisa dihapus karena memiliki {$usedByRt} RT. Hapus atau pindahkan RT di bawahnya terlebih dahulu.");
        }

        try {
            $rw->delete();
            return back()->with('success', 'RW berhasil dihapus permanen.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus RW: ' . $e->getMessage());
        }
    }

    public function destroyRt(Request $request, Rt $rt)
    {
        Gate::authorize('admin_sistem');

        try {
            // Check residents (using whereHas since rt_id is gone from penduduks)
            $usedByPenduduk = Penduduk::withTrashed()->whereHas('kartuKeluarga', fn($q) => $q->where('rt_id', $rt->id))->count();
            // Check KK
            $usedByKk = KartuKeluarga::where('rt_id', $rt->id)->count();

            if ($usedByPenduduk > 0 || $usedByKk > 0) {
                $reasons = [];
                if ($usedByPenduduk > 0) $reasons[] = "{$usedByPenduduk} data penduduk (termasuk yang diarsip/soft-delete)";
                if ($usedByKk > 0) $reasons[] = "{$usedByKk} data Kartu Keluarga";
                
                return back()->with('error', "RT {$rt->kode} tidak bisa dihapus karena masih terhubung dengan: " . implode(', ', $reasons) . ". Silakan pindahkan atau hapus permanen data tersebut terlebih dahulu.");
            }

            $rt->delete();
            return back()->with('success', "RT {$rt->kode} berhasil dihapus.");
            
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', "Gagal menghapus RT karena batasan integritas database. Pastikan tidak ada data lain yang menggunakan RT ini.");
        }
    }

    public function previewImpactRt(Request $request, Rt $rt)
    {
        Gate::authorize('admin_sistem');

        $data = $request->validate([
            'kode' => 'required|string|max:3',
            'rw_id' => 'required|exists:rws,id',
            'dusun_id' => 'nullable|exists:dusuns,id',
            'nama' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
            'needs_review' => 'nullable|boolean',
        ]);

        $oldRt = trim((string) $rt->kode);
        $oldRw = trim((string) optional($rt->rw)->kode);
        $oldDusun = optional($rt->dusun)->nama;

        $newRt = str_pad(preg_replace('/[^0-9]/', '', $data['kode']), 3, '0', STR_PAD_LEFT);
        $newRw = optional(Rw::find($data['rw_id']))->kode;
        $newDusun = !empty($data['dusun_id']) ? optional(Dusun::find($data['dusun_id']))->nama : null;

        $query = Penduduk::query()
            ->whereHas('kartuKeluarga', fn($q) => $q->where('rt_id', $rt->id));

        $token = Str::uuid()->toString();
        $payload = [
            'entity' => 'rt',
            'id' => $rt->id,
            'before' => ['rt' => $oldRt, 'rw' => $oldRw, 'dusun' => $oldDusun],
            'after' => ['rt' => $newRt, 'rw' => $newRw, 'dusun' => $newDusun],
            'will_change' => $oldRt !== $newRt || $oldRw !== $newRw || $oldDusun !== $newDusun,
            'affected_count' => $query->count(),
            'current_count' => $query->count(),
            'sample' => $query->withWilayah()->limit(10)->get()->toArray(),
            'preview_token' => $token,
            'apply_payload' => [
                'kode' => $newRt,
                'rw_id' => (int)$data['rw_id'],
                'dusun_id' => !empty($data['dusun_id']) ? (int)$data['dusun_id'] : null,
                'nama' => !empty($data['nama'] ?? null) ? trim($data['nama']) : ("RT {$newRt}"),
                'is_active' => (bool)($request->boolean('is_active')),
                'needs_review' => (bool)($request->boolean('needs_review')),
            ],
        ];


        session()->put('wilayah_preview_rt.' . $token, [
            'rt_id' => $rt->id,
            'apply_payload' => $payload['apply_payload'],
            'created_at' => now()->toDateTimeString(),
        ]);

        if ($request->expectsJson()) {
            return response()->json($payload);
        }

        return back()->with('preview_impact', $payload);
    }
}
