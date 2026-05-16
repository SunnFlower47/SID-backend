<?php

namespace App\Observers;

use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use App\Console\Commands\SyncKartuKeluarga;
use Illuminate\Support\Facades\Artisan;
use App\Services\VillageStatisticsService;

class PendudukObserver
{
    protected $statsService;

    public function __construct(VillageStatisticsService $statsService)
    {
        $this->statsService = $statsService;
    }
    /**
     * Handle the Penduduk "created" event.
     */
    public function created(Penduduk $penduduk): void
    {
        $this->recalculateKK($penduduk->kartu_keluarga_id);
        $this->statsService->clearStats();
    }

    /**
     * Handle the Penduduk "updated" event.
     */
    public function updated(Penduduk $penduduk): void
    {
        // If KK ID changed, update both old and new KK
        if ($penduduk->isDirty('kartu_keluarga_id')) {
            $this->recalculateKK($penduduk->getOriginal('kartu_keluarga_id'));
            $this->recalculateKK($penduduk->kartu_keluarga_id);
        } else {
            $this->recalculateKK($penduduk->kartu_keluarga_id);
        }
        $this->statsService->clearStats();
    }

    /**
     * Handle the Penduduk "deleted" event.
     */
    public function deleted(Penduduk $penduduk): void
    {
        $this->recalculateKK($penduduk->kartu_keluarga_id);
        $this->statsService->clearStats();
    }

    /**
     * Handle the Penduduk "restored" event.
     */
    public function restored(Penduduk $penduduk): void
    {
        $this->recalculateKK($penduduk->kartu_keluarga_id);

        // Reset flag status KK jika yang di-restore adalah Kepala Keluarga
        if ($penduduk->kedudukan_keluarga === 'Kepala Keluarga' && $penduduk->kartu_keluarga_id) {
            KartuKeluarga::where('id', $penduduk->kartu_keluarga_id)->update([
                'status_kk'           => 'normal',
                'mutasi_penyebab_id'  => null,
                'kk_sementara_id'     => null,
                'kk_bermasalah_sejak' => null,
                'catatan_bermasalah'  => null,
            ]);
        }
        $this->statsService->clearStats();
    }

    /**
     * Update/Recalculate KK stats for a given KK ID
     */
    private function recalculateKK($kkId)
    {
        if (empty($kkId)) return;
        app(\App\Services\KartuKeluargaService::class)->recalculate($kkId);
    }
}
