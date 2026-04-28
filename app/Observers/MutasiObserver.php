<?php

namespace App\Observers;

use App\Models\Mutasi;
use App\Models\Penduduk;
use App\Models\KartuKeluarga;

class MutasiObserver
{
    /**
     * Handle the Mutasi "created" event.
     */
    public function created(Mutasi $mutasi): void
    {
        $this->updateFromPenduduk($mutasi->penduduk_id); // EXISTING — tetap dijalankan

        // BARU (Fase 2): Deteksi dan flag KK bermasalah
        $this->checkAndFlagKKBermasalah($mutasi);
    }

    /**
     * Handle the Mutasi "updated" event.
     */
    public function updated(Mutasi $mutasi): void
    {
        $this->updateFromPenduduk($mutasi->penduduk_id);
    }

    /**
     * Handle the Mutasi "deleted" event.
     */
    public function deleted(Mutasi $mutasi): void
    {
        $this->updateFromPenduduk($mutasi->penduduk_id);
    }

    // =========================================================
    // FASE 2: Deteksi KK Bermasalah
    // =========================================================

    /**
     * Cek apakah mutasi yang baru dibuat menyebabkan suatu KK kehilangan Kepala Keluarga.
     * Jika ya, tandai KK tersebut sebagai 'bermasalah' dan simpan ID mutasi penyebabnya.
     *
     * Diletakkan di MutasiObserver (bukan PendudukObserver) untuk menghindari race condition:
     * - Untuk kematian/pindah_keluar: penduduk di-soft delete SEBELUM Mutasi dibuat,
     *   tapi di sini kita fetch withTrashed() sehingga tetap bisa dibaca.
     * - Untuk pisah_kk: NKK sudah berubah SEBELUM Mutasi dibuat, jadi kita ambil
     *   NKK lama dari detail_tambahan['snapshot_asal']['nkk_asal'] yang sudah tersimpan.
     * - ID mutasi tersedia LANGSUNG ($mutasi->id) — 100% akurat, tanpa query tambahan.
     */
    private function checkAndFlagKKBermasalah(Mutasi $mutasi): void
    {
        // Hanya proses 3 jenis mutasi yang bisa menyebabkan KK kehilangan kepala
        if (!in_array($mutasi->jenis_mutasi, ['kematian', 'pindah_keluar', 'pisah_kk'])) {
            return;
        }

        // Ambil penduduk yang dimutasi (termasuk yang sudah di-soft delete)
        $penduduk = Penduduk::withTrashed()->find($mutasi->penduduk_id);

        if (!$penduduk) {
            return;
        }

        // Hanya lanjutkan jika penduduk ini adalah Kepala Keluarga
        if ($penduduk->kedudukan_keluarga !== 'Kepala Keluarga') {
            return;
        }

        // Tentukan NKK yang bermasalah:
        // - Untuk pisah_kk: NKK penduduk sudah berubah ke NKK baru, NKK lama
        //   tersimpan di detail_tambahan['snapshot_asal']['nkk_asal']
        // - Untuk kematian/pindah_keluar: NKK tidak berubah, ambil langsung
        $nkkBermasalah = ($mutasi->jenis_mutasi === 'pisah_kk')
            ? ($mutasi->detail_tambahan['snapshot_asal']['nkk_asal'] ?? $penduduk->nkk)
            : $penduduk->nkk;

        if (empty($nkkBermasalah)) {
            return;
        }

        // Cek apakah masih ada anggota aktif yang tersisa di NKK tersebut
        // (Jika KK sudah kosong, tidak perlu flag — bukan "bermasalah", tapi "kosong")
        $sisaAktif = Penduduk::where('nkk', $nkkBermasalah)->count();

        if ($sisaAktif <= 0) {
            return;
        }

        // Flag KK sebagai bermasalah — simpan ID mutasi penyebab langsung (tanpa query)
        KartuKeluarga::where('nkk', $nkkBermasalah)->update([
            'status_kk'           => 'bermasalah',
            'mutasi_penyebab_id'  => $mutasi->id,
            'kk_bermasalah_sejak' => now(),
            'catatan_bermasalah'  => "KK ({$penduduk->nama}) - {$mutasi->jenis_mutasi}.",
        ]);
    }

    // =========================================================
    // EXISTING: Recalculate KK Summary
    // =========================================================

    private function updateFromPenduduk($pendudukId)
    {
        $penduduk = Penduduk::withTrashed()->find($pendudukId);
        if ($penduduk) {
            app(\App\Services\KartuKeluargaService::class)->recalculate($penduduk->nkk);
        }
    }
}
