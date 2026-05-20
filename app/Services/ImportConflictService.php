<?php

namespace App\Services;

use App\Models\ImportConflict;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use App\Traits\WilayahResolver;
use Illuminate\Support\Facades\DB;

class ImportConflictService
{
    use WilayahResolver;

    /**
     * Get conflicts list with optional filters and eager loading
     */
    public function getConflicts(array $filters): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = ImportConflict::query()->latest('id');

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['batch_id'])) {
            $query->where('batch_id', 'like', '%' . $filters['batch_id'] . '%');
        }

        if (!empty($filters['issue_type']) && $filters['issue_type'] !== 'all') {
            $query->where('issue_type', $filters['issue_type']);
        }

        $conflicts = $query->paginate(30)->withQueryString();

        // Load existing resident data for comparison in NIK conflicts
        foreach ($conflicts->items() as $conflict) {
            if ($conflict->issue_type === 'nik_conflict' && $conflict->nik) {
                $conflict->existing_resident = Penduduk::withTrashed()
                    ->withWilayah()
                    ->where('nik', $conflict->nik)
                    ->first();
            }
        }

        return $conflicts;
    }

    /**
     * Get aggregate statistics of import conflicts
     */
    public function getConflictStats(): array
    {
        return [
            'total' => ImportConflict::count(),
            'pending' => ImportConflict::where('status', 'pending')->count(),
            'resolved' => ImportConflict::where('status', 'resolved')->count(),
            'success' => ImportConflict::where('reprocess_status', 'success')->count(),
        ];
    }

    /**
     * Resolve import conflict with custom action
     */
    public function resolveConflict(ImportConflict $conflict, array $data, $userId): array
    {
        // Biarkan re-edit jika belum sukses reprocess
        $canReEdit = $conflict->status === 'pending' || ($conflict->status === 'resolved' && ($conflict->reprocess_status ?? '') !== 'success');
        if (!$canReEdit) {
            throw new \Exception('Konflik ini sudah sukses di-import dan tidak dapat diubah lagi.');
        }

        $meta = $conflict->meta ?? [];
        $payloadFixed = $conflict->payload_fixed ?? [];

        // Always capture field improvements if provided
        if (!empty($data['nik_new'])) $payloadFixed['nik'] = preg_replace('/\D+/', '', $data['nik_new']);
        if (!empty($data['nama_new'])) $payloadFixed['nama'] = $data['nama_new'];
        if (!empty($data['nkk_new'])) $payloadFixed['nkk'] = preg_replace('/\D+/', '', $data['nkk_new']);
        if (!empty($data['alamat_new'])) $payloadFixed['alamat'] = $data['alamat_new'];
        if (!empty($data['rt_new'])) $payloadFixed['rt_raw'] = $data['rt_new'];
        if (!empty($data['rw_new'])) $payloadFixed['rw_raw'] = $data['rw_new'];
        if (!empty($data['dusun_new'])) $payloadFixed['dusun_raw'] = $data['dusun_new'];

        if ($data['action'] === 'use_existing') {
            if ($conflict->issue_type !== 'wilayah_conflict') {
                throw new \Exception('Aksi use_existing hanya untuk issue konflik wilayah.');
            }
            if (empty($data['rw_id']) || empty($data['rt_id'])) {
                throw new \Exception('RW dan RT existing wajib dipilih untuk aksi ini.');
            }
            $rt = Rt::find($data['rt_id']);
            if (!$rt || (int)$rt->rw_id !== (int)$data['rw_id']) {
                throw new \Exception('RT tidak sesuai dengan RW yang dipilih.');
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
                throw new \Exception('Aksi create_override hanya untuk issue konflik wilayah.');
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
                throw new \Exception('Aksi ini hanya untuk issue nik_conflict.');
            }
            $meta['resolution'] = ['action' => 'keep_existing_nik'];
        }

        if ($data['action'] === 'update_existing_from_incoming') {
            if ($conflict->issue_type !== 'nik_conflict') {
                throw new \Exception('Aksi ini hanya untuk issue nik_conflict.');
            }
            $meta['resolution'] = ['action' => 'update_existing_from_incoming'];
        }

        if ($data['action'] === 'change_incoming_nik') {
            if ($conflict->issue_type !== 'nik_conflict') {
                throw new \Exception('Aksi ini hanya untuk issue nik_conflict.');
            }
            $newNik = preg_replace('/[^0-9]/', '', (string)($data['nik_new'] ?? ''));
            if (strlen($newNik) !== 16) {
                throw new \Exception('NIK baru wajib tepat 16 digit untuk aksi ini.');
            }
            if (Penduduk::withTrashed()->where('nik', $newNik)->exists()) {
                throw new \Exception('NIK baru ini sudah terdaftar di sistem. Gunakan NIK lain.');
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
            'resolved_by' => $userId,
            'resolved_at' => now(),
            'reprocess_status' => $reprocessStatus,
            'reprocess_message' => in_array($data['action'], ['skip', 'keep_existing_nik'])
                ? 'Tidak perlu reprocess untuk aksi ini.'
                : null,
        ]);

        // Auto-reprocess untuk issue tipe sederhana
        if ($autoReprocess) {
            try {
                DB::transaction(function () use ($conflict) {
                    $row = $this->buildReprocessRowData($conflict->fresh());

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
                return [
                    'success' => true,
                    'auto_reprocessed' => true,
                    'message' => "✅ Berhasil! Data{$namaInfo} sudah diperbaiki dan langsung diimport."
                ];
            } catch (\Throwable $e) {
                $conflict->update([
                    'reprocessed_at' => now(),
                    'reprocess_status' => 'failed',
                    'reprocess_message' => 'Auto-reprocess gagal: ' . $e->getMessage(),
                ]);
                return [
                    'success' => false,
                    'auto_reprocessed' => true,
                    'message' => 'Data tersimpan tapi gagal diimport otomatis: ' . $e->getMessage() . '. Gunakan tombol Reprocess manual.'
                ];
            }
        }

        $namaInfo = $conflict->nama ? " untuk {$conflict->nama}" : '';
        return [
            'success' => true,
            'auto_reprocessed' => false,
            'message' => "Keputusan{$namaInfo} berhasil disimpan. Klik tombol \"Konfirmasi Import\" untuk menerapkan data."
        ];
    }

    /**
     * Reset conflict back to pending
     */
    public function resetConflict(ImportConflict $conflict): void
    {
        if (($conflict->reprocess_status ?? '') === 'success') {
            throw new \Exception('Tidak bisa reset issue yang sudah sukses di-import.');
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
    }

    /**
     * Reprocess individual conflict manually
     */
    public function reprocessConflict(ImportConflict $conflict): void
    {
        if ($conflict->status !== 'resolved') {
            throw new \Exception('Issue harus resolved dulu sebelum reprocess.');
        }

        if (in_array((string)$conflict->reprocess_status, ['success', 'skipped'])) {
            return; // Already processed
        }

        $action = (string)($conflict->resolution_action ?? '');
        if (!$action) {
            throw new \Exception('Resolution action tidak ditemukan. Resolve issue dulu sebelum reprocess.');
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

            throw $e;
        }
    }

    /**
     * Build standard model attributes array from raw/fixed payloads of conflict record
     */
    public function buildReprocessRowData(ImportConflict $conflict): array
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
            'kartu_keluarga_id' => KartuKeluarga::where('nkk', $nkk)->value('id'),
        ];
    }

    // ──────────────── Helper Methods ────────────────

    private function shouldAutoReprocess(string $issueType, string $action): bool
    {
        if (in_array($action, ['skip', 'keep_existing_nik'])) return false;
        return in_array($issueType, ['invalid_nik', 'invalid_nkk', 'wilayah_conflict', 'fix_fields']);
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

    private function normalizeKodeWilayah(string $value, string $default = '001'): string
    {
        $clean = preg_replace('/\D+/', '', $value);
        return $clean ? str_pad(substr($clean, 0, 3), 3, '0', STR_PAD_LEFT) : $default;
    }

    private function mapJenisKelaminSimple(string $value): string
    {
        $v = strtoupper(trim($value));
        if (in_array($v, ['P', 'PEREMPUAN', 'FEMALE', 'WANITA', 'PR'])) return 'PEREMPUAN';
        return 'LAKI-LAKI';
    }

    private function parseDateSimple(string $value): ?string
    {
        $value = trim($value);
        if ($value === '' || $value === '-') return null;
        try {
            if (is_numeric($value)) {
                $excelDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $excelDate->format('Y-m-d');
            }
            if (strpos($value, '/') !== false) return \Carbon\Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) { return null; }
    }
}
