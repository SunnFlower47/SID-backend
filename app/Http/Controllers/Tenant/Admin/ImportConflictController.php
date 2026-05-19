<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ImportConflict;
use App\Models\Rw;
use App\Models\Rt;
use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use App\Traits\WilayahResolver;

class ImportConflictController extends Controller
{
    use WilayahResolver;

    public function importConflicts(Request $request)
    {
        Gate::authorize('admin_sistem');

        return Inertia::render('Tenant/ImportConflict/Index', [
            'conflicts' => Inertia::defer(function() use ($request) {
                $query = ImportConflict::query()->latest('id');

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
                return $conflicts;
            }),
            'stats' => [
                'total' => ImportConflict::count(),
                'pending' => ImportConflict::where('status', 'pending')->count(),
                'resolved' => ImportConflict::where('status', 'resolved')->count(),
                'success' => ImportConflict::where('reprocess_status', 'success')->count(),
            ],
            'rws' => Rw::with('rts')->orderBy('kode')->get(),
            'filters' => $request->only(['status', 'batch_id', 'issue_type'])
        ]);
    }

    
public function resolveImportConflict(Request $request, ImportConflict $conflict)
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
        if ($request->filled('nik_new')) $payloadFixed['nik'] = preg_replace('/\D+/', '', $data['nik_new']);
        if ($request->filled('nama_new')) $payloadFixed['nama'] = $data['nama_new'];
        if ($request->filled('nkk_new')) $payloadFixed['nkk'] = preg_replace('/\D+/', '', $data['nkk_new']);
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
            if (strlen($newNik) !== 16) {
                return back()->with('error', 'NIK baru wajib tepat 16 digit untuk aksi ini.');
            }
            if (Penduduk::withTrashed()->where('nik', $newNik)->exists()) {
                return back()->with('error', 'NIK baru ini sudah terdaftar di sistem. Gunakan NIK lain.');
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

        // Tentukan apakah issue ini bisa langsung di-reprocess otomatis
        $autoReprocess = $this->shouldAutoReprocess($conflict->issue_type, $data['action']);
        $reprocessStatus = match(true) {
            in_array($data['action'], ['skip', 'keep_existing_nik']) => 'skipped',
            $autoReprocess => 'pending', // akan diupdate setelah reprocess
            default => 'pending',
        };

        $conflict->update([
            'status' => 'resolved',
            'meta' => $meta,
            'payload_fixed' => !empty($payloadFixed) ? $payloadFixed : null,
            'resolution_action' => $data['action'],
            'resolved_by' => optional($request->user())->id,
            'resolved_at' => now(),
            'reprocess_status' => $reprocessStatus,
            'reprocess_message' => in_array($data['action'], ['skip', 'keep_existing_nik'])
                ? 'Tidak perlu reprocess untuk aksi ini.'
                : null,
        ]);

        // Auto-reprocess untuk issue tipe sederhana (invalid_nik, invalid_nkk, wilayah_conflict)
        if ($autoReprocess) {
            try {
                DB::transaction(function () use ($conflict) {
                    $row = $this->buildReprocessRowData($conflict->fresh());
                    $action = (string)($conflict->fresh()->resolution_action ?? '');

                    $existing = Penduduk::withTrashed()->where('nik', $row['nik'])->first();
                    if ($existing) {
                        if ($existing->trashed()) $existing->restore();
                        $existing->update($row);
                    } else {
                        Penduduk::create($row);
                    }

                    $conflict->update([
                        'reprocessed_at' => now(),
                        'reprocess_status' => 'success',
                        'reprocess_message' => 'Auto-reprocess berhasil. Data penduduk langsung diterapkan.',
                    ]);
                });

                $namaInfo = $conflict->fresh()->nama ? " untuk {$conflict->fresh()->nama}" : '';
                return back()->with('success', "✅ Berhasil! Data{$namaInfo} sudah diperbaiki dan langsung diimport.");
            } catch (\Throwable $e) {
                $conflict->update([
                    'reprocessed_at' => now(),
                    'reprocess_status' => 'failed',
                    'reprocess_message' => 'Auto-reprocess gagal: ' . $e->getMessage(),
                ]);
                return back()->with('error', 'Data tersimpan tapi gagal diimport otomatis: ' . $e->getMessage() . '. Gunakan tombol Reprocess manual.');
            }
        }

        // Untuk nik_conflict: resolve dulu, reprocess manual nanti
        $namaInfo = $conflict->nama ? " untuk {$conflict->nama}" : '';
        return back()->with('success', "Keputusan{$namaInfo} berhasil disimpan. Klik tombol \"Konfirmasi Import\" untuk menerapkan data.");
    }

    /**
     * Menentukan apakah issue type ini bisa langsung di-reprocess otomatis setelah resolve.
     * Hanya issue sederhana yang tidak perlu konfirmasi manual admin.
     */
    private function shouldAutoReprocess(string $issueType, string $action): bool
    {
        // Aksi skip/keep tidak perlu reprocess
        if (in_array($action, ['skip', 'keep_existing_nik'])) return false;

        // Issue sederhana: perbaiki format atau petakan wilayah → langsung import
        return in_array($issueType, ['invalid_nik', 'invalid_nkk', 'wilayah_conflict', 'fix_fields']);
    }

    public function resetImportConflict(Request $request, ImportConflict $conflict)
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

    public function reprocessImportIssue(Request $request, ImportConflict $conflict)
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

    private function buildReprocessRowData(ImportConflict $conflict): array
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