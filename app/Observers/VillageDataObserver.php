<?php

namespace App\Observers;

use App\Services\Kependudukan\VillageStatisticsService;

class VillageDataObserver
{
    protected $statsService;

    public function __construct(VillageStatisticsService $statsService)
    {
        $this->statsService = $statsService;
    }

    /**
     * Clear statistics when data changes
     */
    public function saved($model)
    {
        $this->statsService->clearStats();
        
        // If it's a wilayah-related model, clear wilayah cache too
        if (in_array(get_class($model), [
            \App\Models\Dusun::class,
            \App\Models\Rw::class,
            \App\Models\Rt::class,
            \App\Models\WilayahChangeLog::class
        ])) {
            $this->statsService->clearWilayah();
        }
    }

    public function deleted($model)
    {
        $this->saved($model);
    }

    public function restored($model)
    {
        $this->saved($model);
    }
}
