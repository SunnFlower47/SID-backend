<?php

namespace App\Services;

use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use App\Models\Mutasi;
use Illuminate\Support\Facades\Log;

class KartuKeluargaService
{
    /**
     * Recalculate statistics for a given KK ID or NKK
     * Optimizes performance by using ID-based lookup and eager loading
     */
    public function recalculate($identifier)
    {
        if (empty($identifier)) return;

        // 1. Find the KK record (Source of Truth)
        // More robust lookup: Try ID first, then NKK
        $kk = \App\Models\KartuKeluarga::withTrashed()->find($identifier);
        
        if (!$kk) {
            $kk = \App\Models\KartuKeluarga::withTrashed()->where('nkk', (string)$identifier)->first();
        }

        if (!$kk) {
            Log::warning("Recalculate failed: Kartu Keluarga not found for identifier: {$identifier}");
            return;
        }

        // 2. Get all members using the real relational ID
        $members = \App\Models\Penduduk::withTrashed()
            ->with(['mutasis' => function($q) {
                // URUTKAN: Ambil mutasi terbaru dulu biar itungannya valid
                $q->latest('tanggal_mutasi')->latest('id');
            }])
            ->where('kartu_keluarga_id', $kk->id)
            ->get();

        if ($members->isEmpty()) {
            $kk->update([
                'jumlah_anggota'    => 0,
                'anggota_aktif'     => 0,
                'anggota_mutasi'    => 0,
                'anggota_meninggal' => 0,
                'anggota_pindah'    => 0,
                'anggota_pisah_kk'  => 0,
                'status_kk'         => 'normal', // Kosong tapi normal
            ]);
            return;
        }

        // 3. Find ALL Head of Family records (Active or Deleted)
        // Kita cari yang perannya bener-bener "KEPALA KELUARGA"
        $allHOFs = $members->filter(function($m) {
            $role = strtoupper(trim($m->kedudukan_keluarga));
            return $role === 'KEPALA KELUARGA';
        });
        
        $activeHOF = $allHOFs->filter(fn($m) => is_null($m->deleted_at))->first();
        
        // Untuk display name, fallback ke anggota tertua jika tidak ada KK aktif
        $activeMembers = $members->filter(fn($m) => is_null($m->deleted_at))->sortBy('tanggal_lahir');
        $displayHOF = $activeHOF ?? $activeMembers->first() ?? $members->first();

        $stats = [
            'active'  => 0,
            'dead'    => 0,
            'moved'   => 0,
            'split'   => 0,
            'mutated' => 0,
        ];

        foreach ($members as $member) {
            if (is_null($member->deleted_at)) {
                $stats['active']++;
            } else {
                $stats['mutated']++;
                // Ambil mutasi terbaru untuk menentukan jenis "OFF"-nya
                $mutasi = $member->mutasis->first();
                if ($mutasi) {
                    if ($mutasi->jenis_mutasi === 'kematian') $stats['dead']++;
                    elseif ($mutasi->jenis_mutasi === 'pindah_keluar') $stats['moved']++;
                    elseif ($mutasi->jenis_mutasi === 'pisah_kk') $stats['split']++;
                }
            }
        }

        // 4. Determine Status (REALTIME AUDIT)
        $statusKk = $kk->status_kk;
        $bermasalahSejak = $kk->kk_bermasalah_sejak;
        $mutasiPenyebabId = $kk->mutasi_penyebab_id;

        // Aturan Audit: KK Bermasalah jika ada anggota AKTIF tapi TIDAK ADA Kepala Keluarga AKTIF
        if ($stats['active'] > 0 && !$activeHOF) {
            // Jika sebelumnya normal, naikkan jadi bermasalah
            if (in_array($statusKk, ['normal', null])) {
                $statusKk = 'bermasalah';
                $bermasalahSejak = $bermasalahSejak ?? now();
                
                // Cari mutasi penyebab (biasanya kematian atau pindah KK)
                $latestMutation = \App\Models\Mutasi::whereIn('penduduk_id', $members->pluck('id'))
                    ->whereIn('jenis_mutasi', ['kematian', 'pindah_keluar', 'pisah_kk'])
                    ->latest('tanggal_mutasi')
                    ->latest('id')
                    ->first();
                $mutasiPenyebabId = $latestMutation ? $latestMutation->id : null;
            }
            // PENTING: Jika status = bermasalah_sementara, JANGAN ubah!
            // Status ini sengaja di-set saat Step 1 (tunjuk KK sementara).
            // Biarkan sampai user melakukan Step 2 (resolve permanen) atau Cancel.
        } else {
            // Ada Kepala Keluarga AKTIF
            // Hanya auto-fix status 'bermasalah' (hasil audit otomatis) ke normal.
            // JANGAN sentuh 'bermasalah_sementara' — itu status workflow manual
            // yang harus diselesaikan lewat Step 2 (resolve permanen).
            if ($statusKk === 'bermasalah') {
                $statusKk = 'normal';
                $bermasalahSejak = null;
                $mutasiPenyebabId = null;
                $kk->update(['kk_sementara_id' => null]);
            }
        }

        // 5. Update the KK record
        $kk->update([
            'nama_kepala_keluarga' => $displayHOF ? $displayHOF->nama : null,
            'nik_kepala_keluarga'  => $displayHOF ? $displayHOF->nik : null,
            'jumlah_anggota'       => $members->count(),
            'anggota_aktif'        => $stats['active'],
            'anggota_mutasi'       => $stats['mutated'],
            'anggota_meninggal'    => $stats['dead'],
            'anggota_pindah'       => $stats['moved'],
            'anggota_pisah_kk'     => $stats['split'],
            'status_kk'            => $statusKk,
            'kk_bermasalah_sejak'  => $bermasalahSejak,
            'mutasi_penyebab_id'   => $mutasiPenyebabId,
        ]);

        // Restore if it has active members but was deleted
        if ($stats['active'] > 0 && $kk->trashed()) {
            $kk->restore();
        }

        return $kk;
    }
}
