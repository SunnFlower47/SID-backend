<?php

namespace App\Http\Controllers\Tenant\Kependudukan;

use App\Http\Controllers\Controller;

use App\Models\Dusun;
use App\Models\KartuKeluarga;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\PendudukDomisili;
use App\Models\WilayahChangeLog;
use App\Models\WilayahImportConflict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use App\Traits\WilayahResolver;

class WilayahController extends Controller
{
    use WilayahResolver;
    public function index()
    {
        Gate::authorize('admin_sistem');

        $dusuns = Dusun::orderBy('nama')->get();
        $rws = Rw::orderBy('kode')->get();
        $rts = Rt::with(['rw', 'dusun'])->orderBy('kode')->get();

        $pendudukCounts = Penduduk::query()
            ->join('kartu_keluargas', 'penduduks.kartu_keluarga_id', '=', 'kartu_keluargas.id')
            ->selectRaw('kartu_keluargas.rt_id, COUNT(*) as total')
            ->whereNull('penduduks.deleted_at')
            ->groupBy('kartu_keluargas.rt_id')
            ->pluck('total', 'rt_id');

        $domisiliCounts = PendudukDomisili::query()
            ->selectRaw('rt_id, COUNT(*) as total')
            ->where('status', 'aktif')
            ->groupBy('rt_id')
            ->pluck('total', 'rt_id');

        foreach ($rts as $rt) {
            $rt->penduduk_count = (int) ($pendudukCounts[$rt->id] ?? 0);
            $rt->domisili_count = (int) ($domisiliCounts[$rt->id] ?? 0);
        }

        $summary = [
            'dusun' => $dusuns->count(),
            'rw' => $rws->count(),
            'rt' => $rts->count(),
            'penduduk_terpetakan' => (int) $rts->sum('penduduk_count'),
            'domisili_terpetakan' => (int) $rts->sum('domisili_count'),
        ];

        $recentChangeLogs = WilayahChangeLog::query()
            ->where('entity_type', 'rt')
            ->latest('id')
            ->limit(15)
            ->get();

        return view('settings.wilayah.index', compact('dusuns', 'rws', 'rts', 'summary', 'recentChangeLogs'));
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

        // Hardened gate: update hanya boleh lewat alur preview + apply
        return back()->with('error', 'Untuk update RT, gunakan tombol Preview Impact lalu klik Lanjutkan Apply dari modal konfirmasi.');
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

            foreach ($pendudukBefore as $p) {
                $penduduk = Penduduk::find($p['id']);
                if ($penduduk && $penduduk->kartuKeluarga) {
                    $penduduk->kartuKeluarga->update([
                        'rt_id' => $rtBefore['id'], // We know it's this RT
                        'rw_id' => $rtBefore['rw_id'],
                        'dusun_id' => $rtBefore['dusun_id'],
                    ]);
                }
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

        $penduduks = Penduduk::query()
            ->withWilayah()
            ->whereHas('kartuKeluarga', fn($q) => $q->where('rt_id', $rt->id))
            ->orderBy('nama')
            ->paginate(50)
            ->withQueryString();

        return view('settings.wilayah.rt-penduduk', [
            'rt' => $rt->load(['rw', 'dusun']),
            'penduduks' => $penduduks,
        ]);
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

    public function importConflicts(Request $request)
    {
        Gate::authorize('admin_sistem');

        $query = WilayahImportConflict::query()->latest('id');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', 'like', '%' . $request->batch_id . '%');
        }

        if ($request->filled('issue_type') && $request->issue_type !== 'all') {
            $query->where('issue_type', $request->issue_type);
        }

        $conflicts = $query->paginate(30)->withQueryString();

        // Load existing resident data for comparison in NIK conflicts
        foreach ($conflicts->items() as $conflict) {
            if ($conflict->issue_type === 'nik_conflict' && $conflict->nik) {
                $conflict->existing_resident = \App\Models\Penduduk::withTrashed()
                    ->withWilayah()
                    ->where('nik', $conflict->nik)
                    ->first();
            }
        }

        $rws = Rw::with('rts')->orderBy('kode')->get();

        return view('settings.wilayah.import-conflicts', compact('conflicts', 'rws'));
    }

    public function resolveImportConflict(Request $request, WilayahImportConflict $conflict)
    {
        Gate::authorize('admin_sistem');

        $data = $request->validate([
            'action' => 'required|in:use_existing,create_override,skip,fix_fields,keep_existing_nik,update_existing_from_incoming,change_incoming_nik',
            'rw_id' => 'nullable|exists:rws,id',
            'rt_id' => 'nullable|exists:rts,id',
            'nik_new' => 'nullable|string|max:20',
            'nama_new' => 'nullable|string|max:255',
            'nkk_new' => 'nullable|string|max:20',
            'alamat_new' => 'nullable|string|max:500',
            'dusun_new' => 'nullable|string|max:100',
            'rw_new' => 'nullable|string|max:10',
            'rt_new' => 'nullable|string|max:10',
        ]);

        // Strict validation for NIK and NKK if provided
        if ($request->filled('nik_new')) {
            $nikClean = preg_replace('/\D+/', '', $data['nik_new']);
            if (strlen($nikClean) !== 16) {
                return back()->with('error', 'Format NIK salah! Harus tepat 16 digit angka.');
            }
        }
        if ($request->filled('nkk_new')) {
            $nkkClean = preg_replace('/\D+/', '', $data['nkk_new']);
            if (strlen($nkkClean) !== 16) {
                return back()->with('error', 'Format No. KK salah! Harus tepat 16 digit angka.');
            }
        }

        // Biarkan re-edit jika belum sukses reprocess
        $canReEdit = $conflict->status === 'pending' || ($conflict->status === 'resolved' && ($conflict->reprocess_status ?? '') !== 'success');
        if (!$canReEdit) {
            return back()->with('error', 'Konflik ini sudah sukses di-import dan tidak dapat diubah lagi.');
        }

        $meta = $conflict->meta ?? [];
        $payloadFixed = $conflict->payload_fixed ?? [];
        
        // Always capture field improvements if provided
        if ($request->filled('nik_new')) $payloadFixed['nik'] = $data['nik_new'];
        if ($request->filled('nama_new')) $payloadFixed['nama'] = $data['nama_new'];
        if ($request->filled('nkk_new')) $payloadFixed['nkk'] = $data['nkk_new'];
        if ($request->filled('alamat_new')) $payloadFixed['alamat'] = $data['alamat_new'];
        if ($request->filled('rt_new')) $payloadFixed['rt_raw'] = $data['rt_new'];
        if ($request->filled('rw_new')) $payloadFixed['rw_raw'] = $data['rw_new'];
        if ($request->filled('dusun_new')) $payloadFixed['dusun_raw'] = $data['dusun_new'];

        if ($data['action'] === 'use_existing') {
            if ($conflict->issue_type !== 'wilayah_conflict') {
                return back()->with('error', 'Aksi use_existing hanya untuk issue konflik wilayah.');
            }
            if (empty($data['rw_id']) || empty($data['rt_id'])) {
                return back()->with('error', 'RW dan RT existing wajib dipilih untuk aksi ini.');
            }

            $rt = Rt::find($data['rt_id']);
            if (!$rt || (int)$rt->rw_id !== (int)$data['rw_id']) {
                return back()->with('error', 'RT tidak sesuai dengan RW yang dipilih.');
            }

            $meta['resolution'] = [
                'action' => 'use_existing',
                'rw_id' => (int)$data['rw_id'],
                'rt_id' => (int)$data['rt_id'],
                'rw_kode' => optional($rt->rw)->kode,
                'rt_kode' => $rt->kode,
            ];
        }

        if ($data['action'] === 'create_override') {
            if ($conflict->issue_type !== 'wilayah_conflict') {
                return back()->with('error', 'Aksi create_override hanya untuk issue konflik wilayah.');
            }
            // Use manually typed codes or raw codes from excel
            $rwKode = $this->normalizeKodeWilayah($data['rw_new'] ?? $conflict->rw_raw, '001');
            $rtKode = $this->normalizeKodeWilayah($data['rt_new'] ?? $conflict->rt_raw, '001');

            $rw = Rw::firstOrCreate(
                ['kode' => $rwKode],
                ['nama' => "RW {$rwKode}", 'is_active' => true, 'is_auto_generated' => true, 'needs_review' => true]
            );

            $rt = Rt::firstOrCreate(
                ['kode' => $rtKode, 'rw_id' => $rw->id],
                ['nama' => "RT {$rtKode}", 'is_active' => true, 'is_auto_generated' => true, 'needs_review' => true]
            );

            $meta['resolution'] = [
                'action' => 'create_override',
                'rw_id' => $rw->id,
                'rt_id' => $rt->id,
                'rw_kode' => $rw->kode,
                'rt_kode' => $rt->kode,
            ];
        }

        if ($data['action'] === 'keep_existing_nik') {
            if ($conflict->issue_type !== 'nik_conflict') {
                return back()->with('error', 'Aksi ini hanya untuk issue nik_conflict.');
            }
            $meta['resolution'] = ['action' => 'keep_existing_nik'];
        }

        if ($data['action'] === 'update_existing_from_incoming') {
            if ($conflict->issue_type !== 'nik_conflict') {
                return back()->with('error', 'Aksi ini hanya untuk issue nik_conflict.');
            }
            $meta['resolution'] = ['action' => 'update_existing_from_incoming'];
        }

        if ($data['action'] === 'change_incoming_nik') {
            if ($conflict->issue_type !== 'nik_conflict') {
                return back()->with('error', 'Aksi ini hanya untuk issue nik_conflict.');
            }
            $newNik = preg_replace('/[^0-9]/', '', (string)($data['nik_new'] ?? ''));
            if (!$newNik) {
                return back()->with('error', 'NIK baru wajib diisi untuk aksi change_incoming_nik.');
            }
            $payloadFixed['nik'] = $newNik;
            $meta['resolution'] = ['action' => 'change_incoming_nik', 'nik_new' => $newNik];
        }

        if ($data['action'] === 'skip') {
            $meta['resolution'] = ['action' => 'skip'];
        }

        if ($data['action'] === 'fix_fields') {
            $meta['resolution'] = ['action' => 'fix_fields'];
        }

        $conflict->update([
            'status' => 'resolved',
            'meta' => $meta,
            'payload_fixed' => !empty($payloadFixed) ? $payloadFixed : null,
            'resolution_action' => $data['action'],
            'resolved_by' => optional($request->user())->id,
            'resolved_at' => now(),
            'reprocess_status' => in_array($data['action'], ['skip', 'keep_existing_nik']) ? 'skipped' : 'pending',
            'reprocess_message' => in_array($data['action'], ['skip', 'keep_existing_nik']) ? 'Tidak perlu reprocess untuk aksi ini.' : null,
        ]);

        return back()->with('success', 'Issue import berhasil diproses. Silakan jalankan Reprocess jika diperlukan.');
    }

    public function resetImportConflict(Request $request, WilayahImportConflict $conflict)
    {
        Gate::authorize('admin_sistem');

        if (($conflict->reprocess_status ?? '') === 'success') {
            return back()->with('error', 'Tidak bisa reset issue yang sudah sukses di-import.');
        }

        $conflict->update([
            'status' => 'pending',
            'resolution_action' => null,
            'reprocess_status' => null,
            'reprocess_message' => null,
            'resolved_at' => null,
            'resolved_by' => null,
            'payload_fixed' => null,
        ]);

        return back()->with('success', 'Status issue berhasil di-reset menjadi Pending.');
    }

    public function reprocessImportIssue(Request $request, WilayahImportConflict $conflict)
    {
        Gate::authorize('admin_sistem');

        if ($conflict->status !== 'resolved') {
            return back()->with('error', 'Issue harus resolved dulu sebelum reprocess.');
        }

        if (in_array((string)$conflict->reprocess_status, ['success', 'skipped'])) {
            return back()->with('info', 'Issue ini sudah diproses sebelumnya.');
        }

        $action = (string)($conflict->resolution_action ?? '');
        if (!$action) {
            return back()->with('error', 'Resolution action tidak ditemukan. Resolve issue dulu sebelum reprocess.');
        }

        try {
            DB::transaction(function () use ($conflict, $action) {
                $row = $this->buildReprocessRowData($conflict);

                if ($action === 'update_existing_from_incoming') {
                    $existing = Penduduk::withTrashed()->where('nik', $row['nik'])->first();
                    if (!$existing) {
                        throw new \RuntimeException('Data penduduk existing untuk NIK ini tidak ditemukan.');
                    }

                    if (method_exists($existing, 'trashed') && $existing->trashed()) {
                        $existing->restore();
                    }

                    $existing->update($row);
                } elseif (!in_array($action, ['skip', 'keep_existing_nik'], true)) {
                    $existing = Penduduk::withTrashed()->where('nik', $row['nik'])->first();
                    if ($existing) {
                        if (method_exists($existing, 'trashed') && $existing->trashed()) {
                            $existing->restore();
                        }
                        $existing->update($row);
                    } else {
                        Penduduk::create($row);
                    }
                }

                $conflict->update([
                    'reprocessed_at' => now(),
                    'reprocess_status' => in_array($action, ['skip', 'keep_existing_nik'], true) ? 'skipped' : 'success',
                    'reprocess_message' => in_array($action, ['skip', 'keep_existing_nik'], true)
                        ? 'Tidak ada perubahan data (aksi tidak memerlukan reprocess).'
                        : 'Reprocess berhasil. Data penduduk sudah diterapkan.',
                ]);
            });
        } catch (\Throwable $e) {
            $conflict->update([
                'reprocessed_at' => now(),
                'reprocess_status' => 'failed',
                'reprocess_message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Reprocess gagal: ' . $e->getMessage());
        }

        return back()->with('success', 'Reprocess issue berhasil dijalankan.');
    }

    private function buildReprocessRowData(WilayahImportConflict $conflict): array
    {
        $raw = is_array($conflict->payload_raw) ? $conflict->payload_raw : [];
        $fixed = is_array($conflict->payload_fixed) ? $conflict->payload_fixed : [];
        $meta = is_array($conflict->meta) ? $conflict->meta : [];

        $nik = preg_replace('/[^0-9]/', '', (string)($fixed['nik'] ?? $conflict->nik ?? $this->extractPayloadValue($raw, ['nik', 'nomor induk kependudukan']) ?? ''));
        $nama = trim((string)($fixed['nama'] ?? $conflict->nama ?? $this->extractPayloadValue($raw, ['nama', 'nama lengkap']) ?? ''));
        $nkk = preg_replace('/[^0-9]/', '', (string)($fixed['nkk'] ?? $conflict->nkk ?? $this->extractPayloadValue($raw, ['nkk', 'no kk', 'nomor kk', 'kartu keluarga']) ?? ''));

        if ($nik === '' || $nama === '') {
            throw new \RuntimeException('NIK/Nama belum valid untuk reprocess. Lengkapi dulu via resolve issue.');
        }

        if (strlen($nik) !== 16) {
            throw new \RuntimeException('NIK harus tepat 16 digit sebelum reprocess.');
        }

        if ($nkk === '' || strlen($nkk) !== 16) {
            throw new \RuntimeException('NKK harus tepat 16 digit sebelum reprocess.');
        }

        $jkRaw = (string)($fixed['jenis_kelamin'] ?? $this->extractPayloadValue($raw, ['jenis kelamin', 'jenis_kelamin']) ?? '');
        $ttlRaw = (string)($fixed['tanggal_lahir'] ?? $this->extractPayloadValue($raw, ['tanggal lahir', 'tanggal_lahir', 'tgl lahir']) ?? '');
        $tempatLahir = trim((string)($fixed['tempat_lahir'] ?? $this->extractPayloadValue($raw, ['tempat lahir', 'tempat_lahir']) ?? ''));
        $agama = trim((string)($fixed['agama'] ?? $this->extractPayloadValue($raw, ['agama']) ?? ''));
        $statusPerkawinan = trim((string)($fixed['status_perkawinan'] ?? $this->extractPayloadValue($raw, ['status perkawinan', 'status_perkawinan']) ?? ''));
        $kedudukanKeluarga = trim((string)($fixed['kedudukan_keluarga'] ?? $this->extractPayloadValue($raw, ['kedudukan keluarga', 'kedudukan_keluarga', 'kedudukan dalam keluarga']) ?? ''));
        $pendidikan = trim((string)($fixed['pendidikan'] ?? $this->extractPayloadValue($raw, ['pendidikan']) ?? ''));
        $pekerjaan = trim((string)($fixed['pekerjaan'] ?? $this->extractPayloadValue($raw, ['pekerjaan']) ?? ''));
        $namaAyah = trim((string)($fixed['nama_ayah'] ?? $this->extractPayloadValue($raw, ['nama ayah', 'nama_ayah']) ?? ''));
        $namaIbu = trim((string)($fixed['nama_ibu'] ?? $this->extractPayloadValue($raw, ['nama ibu', 'nama_ibu']) ?? ''));
        $keterangan = trim((string)($fixed['keterangan'] ?? $this->extractPayloadValue($raw, ['keterangan', 'catatan']) ?? ''));
        $alamat = trim((string)($fixed['alamat'] ?? $this->extractPayloadValue($raw, ['alamat']) ?? ''));

        $rwKode = $this->normalizeKodeWilayah((string)($fixed['rw'] ?? $fixed['rw_raw'] ?? $conflict->rw_raw), '001');
        $rtKode = $this->normalizeKodeWilayah((string)($fixed['rt'] ?? $fixed['rt_raw'] ?? $conflict->rt_raw), '001');
        $dusunNama = trim((string)($fixed['dusun'] ?? $fixed['dusun_raw'] ?? $conflict->dusun_raw ?? ''));

        $resolution = (array)($meta['resolution'] ?? []);
        $rtId = null;
        $rwId = null;
        $dusunId = null;

        if ($conflict->issue_type === 'wilayah_conflict' && in_array($conflict->resolution_action, ['use_existing', 'create_override'], true)) {
            $resolvedRtId = (int)($resolution['rt_id'] ?? 0);
            $rt = $resolvedRtId ? Rt::with('rw', 'dusun')->find($resolvedRtId) : null;
            if (!$rt) {
                throw new \RuntimeException('RT hasil resolusi tidak ditemukan.');
            }

            $rwKode = trim((string)optional($rt->rw)->kode);
            $rtKode = trim((string)$rt->kode);
            $dusunNama = trim((string)optional($rt->dusun)->nama);

            $rtId = (int)$rt->id;
            $rwId = (int)$rt->rw_id;
            $dusunId = $rt->dusun_id ? (int)$rt->dusun_id : null;
        } else {
            $wilayah = $this->resolveWilayah($rtKode, $rwKode, $dusunNama);

            $rtId = $wilayah['rt_id'];
            $rwId = $wilayah['rw_id'];
            $dusunId = $wilayah['dusun_id'];
        }

        if (!$rtId || !$rwId) {
            throw new \RuntimeException('Mapping wilayah gagal: RT/RW ID tidak valid untuk reprocess.');
        }

        $this->upsertKartuKeluargaAndGetId($nkk, [
            'alamat' => $alamat !== '' ? $alamat : 'Alamat tidak diketahui',
            'rt_id' => $rtId,
            'rw_id' => $rwId,
            'dusun_id' => $dusunId,
        ]);

        return [
            'nkk' => $nkk,
            'nik' => $nik,
            'nama' => $nama,
            'jenis_kelamin' => $this->mapJenisKelaminSimple($jkRaw),
            'tempat_lahir' => $tempatLahir !== '' ? $tempatLahir : 'Tidak diketahui',
            'tanggal_lahir' => $this->parseDateSimple($ttlRaw),
            'agama' => $agama !== '' ? $agama : 'Islam',
            'status_perkawinan' => $statusPerkawinan !== '' ? $statusPerkawinan : null,
            'kedudukan_keluarga' => $kedudukanKeluarga !== '' ? $kedudukanKeluarga : null,
            'pendidikan' => $pendidikan !== '' ? $pendidikan : 'Tidak/Belum Sekolah',
            'pekerjaan' => $pekerjaan !== '' ? $pekerjaan : '-',
            'nama_ayah' => $namaAyah !== '' ? $namaAyah : null,
            'nama_ibu' => $namaIbu !== '' ? $namaIbu : null,
            'keterangan' => $keterangan !== '' ? $keterangan : null,
            'alamat' => $alamat !== '' ? $alamat : 'Alamat tidak diketahui',
            // Area below is for virtual mapping/logging, NOT for Penduduk::create
            'kartu_keluarga_id' => KartuKeluarga::where('nkk', $nkk)->value('id'),
        ];
    }

    private function upsertKartuKeluargaAndGetId(string $nkk, array $attrs = []): void
    {
        KartuKeluarga::updateOrCreate(
            ['nkk' => $nkk],
            [
                'alamat' => $attrs['alamat'] ?? null,
                'rt_id' => $attrs['rt_id'] ?? null,
                'rw_id' => $attrs['rw_id'] ?? null,
                'dusun_id' => $attrs['dusun_id'] ?? null,
            ]
        );
    }

    private function extractPayloadValue(array $payloadRaw, array $candidates): ?string
    {
        foreach ($payloadRaw as $key => $val) {
            $keyLower = strtolower(trim((string)$key));
            foreach ($candidates as $cand) {
                if ($keyLower === strtolower($cand)) return (string)$val;
            }
        }
        return null;
    }
}
