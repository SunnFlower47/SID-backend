<?php

namespace App\Observers;

use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use App\Console\Commands\SyncKartuKeluarga;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class PendudukObserver
{
    /**
     * Handle the Penduduk "created" event.
     */
    public function created(Penduduk $penduduk): void
    {
        $this->updateKartuKeluarga($penduduk->nkk);
        $this->clearCache();
    }

    /**
     * Handle the Penduduk "updated" event.
     */
    public function updated(Penduduk $penduduk): void
    {
        // If NKK changed, update both old and new KK
        if ($penduduk->isDirty('nkk')) {
            $this->updateKartuKeluarga($penduduk->getOriginal('nkk'));
            $this->updateKartuKeluarga($penduduk->nkk);
        } else {
            $this->updateKartuKeluarga($penduduk->nkk);
        }
        $this->clearCache();
    }

    /**
     * Handle the Penduduk "deleted" event.
     */
    public function deleted(Penduduk $penduduk): void
    {
        $this->updateKartuKeluarga($penduduk->nkk);
        $this->clearCache();
    }

    /**
     * Handle the Penduduk "restored" event.
     * FASE 3: Reset flag KK bermasalah saat Kepala Keluarga lama di-restore via Undo.
     */
    public function restored(Penduduk $penduduk): void
    {
        $this->updateKartuKeluarga($penduduk->nkk); // EXISTING — tetap dijalankan

        // BARU (Fase 3): Reset flag status KK jika yang di-restore adalah Kepala Keluarga
        // Saat ini dipanggil SETELAH undo() di MutasiController sudah rollback KK sementara,
        // sehingga reset di sini aman (tidak ada konflik 2 Kepala Keluarga).
        if ($penduduk->kedudukan_keluarga === 'Kepala Keluarga') {
            KartuKeluarga::where('nkk', $penduduk->nkk)->update([
                'status_kk'           => 'normal',
                'mutasi_penyebab_id'  => null,
                'kk_sementara_id'     => null,
                'kk_bermasalah_sejak' => null,
                'catatan_bermasalah'  => null,
            ]);
        }
        $this->clearCache();
    }

    /**
     * Update/Recalculate KK stats for a given NKK
     * This essentially runs a mini-sync for one KK
     */
    private function updateKartuKeluarga($nkk)
    {
        if (empty($nkk)) return;
        app(\App\Services\KartuKeluargaService::class)->recalculate($nkk);
    }

    /**
     * Clear all relevant caches for real-time statistics
     */
    private function clearCache()
    {
        // Clear API statistics cache
        Cache::forget('api_penduduk_age_statistics');
        Cache::forget('api_penduduk_filter_options_v2');
        
        // Note: api_penduduk_ (dynamic search results) are left for TTL 
        // as they are many and short-lived (120s), but we could clear them if needed.
        // For Enterprise, we prioritize consistency for stats.
    }
}
