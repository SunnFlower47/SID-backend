<?php

namespace App\Services\Wilayah;

use App\Models\Dusun;
use App\Models\KartuKeluarga;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\WilayahChangeLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WilayahService
{
    /**
     * Normalisasi kode numerik (RT/RW) menjadi format 3-digit zero-padded.
     */
    public function normalizeKode(string $kode): string
    {
        return str_pad(preg_replace('/[^0-9]/', '', $kode), 3, '0', STR_PAD_LEFT);
    }

    /**
     * Guard: cek apakah Dusun masih aman untuk dihapus.
     * Return null jika aman, string pesan error jika tidak.
     */
    public function canDeleteDusun(Dusun $dusun): ?string
    {
        $usedByRt = Rt::where('dusun_id', $dusun->id)->count();
        if ($usedByRt > 0) {
            return "Dusun {$dusun->nama} tidak bisa dihapus karena masih digunakan oleh {$usedByRt} RT. Ubah dulu RT yang menggunakan Dusun ini.";
        }
        return null;
    }

    /**
     * Guard: cek apakah RW masih aman untuk dihapus.
     * Return null jika aman, string pesan error jika tidak.
     */
    public function canDeleteRw(Rw $rw): ?string
    {
        $usedByRt = Rt::where('rw_id', $rw->id)->count();
        if ($usedByRt > 0) {
            return "RW {$rw->kode} tidak bisa dihapus karena memiliki {$usedByRt} RT. Hapus atau pindahkan RT di bawahnya terlebih dahulu.";
        }
        return null;
    }

    /**
     * Guard: cek apakah RT masih aman untuk dihapus.
     * Return null jika aman, string pesan error jika tidak.
     */
    public function canDeleteRt(Rt $rt): ?string
    {
        $usedByPenduduk = Penduduk::withTrashed()
            ->whereHas('kartuKeluarga', fn ($q) => $q->where('rt_id', $rt->id))
            ->count();
        $usedByKk = KartuKeluarga::where('rt_id', $rt->id)->count();

        if ($usedByPenduduk > 0 || $usedByKk > 0) {
            $reasons = [];
            if ($usedByPenduduk > 0) $reasons[] = "{$usedByPenduduk} data penduduk (termasuk yang diarsip/soft-delete)";
            if ($usedByKk > 0)       $reasons[] = "{$usedByKk} data Kartu Keluarga";

            return "RT {$rt->kode} tidak bisa dihapus karena masih terhubung dengan: " . implode(', ', $reasons) . ". Silakan pindahkan atau hapus permanen data tersebut terlebih dahulu.";
        }

        return null;
    }

    /**
     * Hitung dampak perubahan pada entitas Dusun.
     */
    public function previewDusunImpact(Dusun $dusun, string $newNama): array
    {
        $oldNama = trim((string) $dusun->nama);
        $query = Penduduk::query()->whereHas('kartuKeluarga', fn ($q) => $q->where('dusun_id', $dusun->id));

        return [
            'entity'         => 'dusun',
            'id'             => $dusun->id,
            'before'         => ['nama' => $oldNama],
            'after'          => ['nama' => trim($newNama)],
            'will_change'    => $oldNama !== trim($newNama),
            'affected_count' => $query->count(),
            'sample'         => $query->withWilayah()->limit(10)->get(),
        ];
    }

    /**
     * Hitung dampak perubahan pada entitas RW.
     */
    public function previewRwImpact(Rw $rw, string $newKode): array
    {
        $oldKode = trim((string) $rw->kode);
        $normalizedKode = $this->normalizeKode($newKode);
        $query = Penduduk::query()->whereHas('kartuKeluarga', fn ($q) => $q->where('rw_id', $rw->id));

        return [
            'entity'         => 'rw',
            'id'             => $rw->id,
            'before'         => ['kode' => $oldKode],
            'after'          => ['kode' => $normalizedKode],
            'will_change'    => $oldKode !== $normalizedKode,
            'affected_count' => $query->count(),
            'sample'         => $query->withWilayah()->limit(10)->get(),
        ];
    }

    /**
     * Hitung dampak perubahan hierarki RT (pindah RW/Dusun) dan simpan preview token ke session.
     */
    public function previewRtImpact(Rt $rt, array $data): array
    {
        $oldRt    = trim((string) $rt->kode);
        $oldRw    = trim((string) optional($rt->rw)->kode);
        $oldDusun = optional($rt->dusun)->nama;

        $newRt    = $this->normalizeKode($data['kode']);
        $newRw    = optional(Rw::find($data['rw_id']))->kode;
        $newDusun = !empty($data['dusun_id']) ? optional(Dusun::find($data['dusun_id']))->nama : null;

        $affectedCount = Penduduk::query()
            ->whereHas('kartuKeluarga', fn ($q) => $q->where('rt_id', $rt->id))
            ->count();

        $token = Str::uuid()->toString();

        $applyPayload = [
            'kode'         => $newRt,
            'rw_id'        => (int) $data['rw_id'],
            'dusun_id'     => !empty($data['dusun_id']) ? (int) $data['dusun_id'] : null,
            'nama'         => !empty($data['nama'] ?? null) ? trim($data['nama']) : "RT {$newRt}",
            'is_active'    => (bool) ($data['is_active'] ?? false),
            'needs_review' => (bool) ($data['needs_review'] ?? false),
        ];

        // Simpan ke session agar applyRtUpdate dapat memverifikasi token
        session()->put('wilayah_preview_rt.' . $token, [
            'rt_id'         => $rt->id,
            'apply_payload' => $applyPayload,
            'created_at'    => now()->toDateTimeString(),
        ]);

        return [
            'entity'         => 'rt',
            'id'             => $rt->id,
            'before'         => ['rt' => $oldRt, 'rw' => $oldRw, 'dusun' => $oldDusun],
            'after'          => ['rt' => $newRt, 'rw' => $newRw, 'dusun' => $newDusun],
            'will_change'    => $oldRt !== $newRt || $oldRw !== $newRw || $oldDusun !== $newDusun,
            'affected_count' => $affectedCount,
            'current_count'  => $affectedCount,
            'sample'         => Penduduk::whereHas('kartuKeluarga', fn ($q) => $q->where('rt_id', $rt->id))->withWilayah()->limit(10)->get()->toArray(),
            'preview_token'  => $token,
            'apply_payload'  => $applyPayload,
        ];
    }

    /**
     * Apply perubahan hierarki RT dengan DB transaction dan audit log.
     * Mengembalikan log yang dibuat untuk keperluan rollback.
     */
    public function applyRtChange(Rt $rt, array $data, ?int $userId): WilayahChangeLog
    {
        $kode     = $this->normalizeKode($data['kode']);
        $oldRt    = trim((string) $rt->kode);
        $oldRw    = trim((string) optional($rt->rw)->kode);
        $oldDusun = optional($rt->dusun)->nama;
        $newRw    = optional(Rw::find($data['rw_id']))->kode;
        $newDusun = !empty($data['dusun_id']) ? optional(Dusun::find($data['dusun_id']))->nama : null;

        $affectedRows = Penduduk::query()
            ->whereHas('kartuKeluarga', fn ($q) => $q->where('rt_id', $rt->id))
            ->get(['id', 'kartu_keluarga_id'])
            ->toArray();

        $backupPayload = [
            'rt_before' => [
                'id'           => $rt->id,
                'kode'         => $oldRt,
                'rw_id'        => $rt->rw_id,
                'dusun_id'     => $rt->dusun_id,
                'nama'         => $rt->nama,
                'is_active'    => (bool) $rt->is_active,
                'needs_review' => (bool) $rt->needs_review,
            ],
            'penduduk_before' => $affectedRows,
        ];

        $log = null;

        DB::transaction(function () use ($rt, $data, $kode, $newRw, $newDusun, $oldRt, $oldRw, $oldDusun, $affectedRows, $backupPayload, $userId, &$log) {
            $rt->update([
                'kode'         => $kode,
                'rw_id'        => $data['rw_id'],
                'dusun_id'     => $data['dusun_id'] ?? null,
                'nama'         => !empty($data['nama'] ?? null) ? trim($data['nama']) : "RT {$kode}",
                'is_active'    => (bool) ($data['is_active'] ?? false),
                'needs_review' => (bool) ($data['needs_review'] ?? false),
            ]);

            // Update hierarki di KartuKeluarga (Source of Truth)
            KartuKeluarga::where('rt_id', $rt->id)->each(function ($kk) use ($data) {
                $kk->update([
                    'rw_id'    => $data['rw_id'],
                    'dusun_id' => $data['dusun_id'] ?? null,
                ]);
            });

            $log = WilayahChangeLog::create([
                'entity_type'    => 'rt',
                'entity_id'      => $rt->id,
                'action'         => 'update_with_backup',
                'user_id'        => $userId,
                'preview_token'  => (string) $data['preview_token'],
                'before_payload' => ['rt' => $oldRt, 'rw' => $oldRw, 'dusun' => $oldDusun],
                'after_payload'  => ['rt' => $kode, 'rw' => $newRw, 'dusun' => $newDusun],
                'backup_payload' => $backupPayload,
                'affected_count' => count($affectedRows),
                'status'         => 'applied',
                'applied_at'     => now(),
            ]);
        });

        return $log;
    }

    /**
     * Rollback perubahan RT ke snapshot sebelumnya.
     */
    public function rollbackRtChange(WilayahChangeLog $log, ?int $userId): void
    {
        $backup    = $log->backup_payload ?? [];
        $rtBefore  = $backup['rt_before'] ?? null;

        if (!$rtBefore) {
            throw new \RuntimeException('Backup payload tidak lengkap.');
        }

        DB::transaction(function () use ($log, $rtBefore, $userId) {
            $rt = Rt::find($rtBefore['id']);
            if ($rt) {
                $rt->update([
                    'kode'         => $rtBefore['kode'],
                    'rw_id'        => $rtBefore['rw_id'],
                    'dusun_id'     => $rtBefore['dusun_id'],
                    'nama'         => $rtBefore['nama'],
                    'is_active'    => (bool) $rtBefore['is_active'],
                    'needs_review' => (bool) $rtBefore['needs_review'],
                ]);
            }

            // Kembalikan hierarki KartuKeluarga (Source of Truth)
            KartuKeluarga::where('rt_id', $rtBefore['id'])->each(function ($kk) use ($rtBefore) {
                $kk->update([
                    'rw_id'    => $rtBefore['rw_id'],
                    'dusun_id' => $rtBefore['dusun_id'],
                ]);
            });

            $log->update([
                'status'         => 'rolled_back',
                'rolled_back_at' => now(),
                'rolled_back_by' => $userId,
            ]);
        });
    }
}
