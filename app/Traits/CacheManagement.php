<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait CacheManagement
{
    /**
     * Clear all relevant caches after CRUD operations
     */
    protected function clearRelevantCaches()
    {
        // Clear main caches
        Cache::forget('desa_statistics');
        Cache::forget('api_statistics');
        Cache::forget('statistics_dashboard');
        Cache::forget('settings_statistics');
        Cache::forget('settings_users_list');
        Cache::forget('settings_roles_list');
        Cache::forget('settings_permissions_grouped');

        // Clear specific model caches
        $this->clearModelCaches();
    }

    /**
     * Clear model-specific caches
     */
    protected function clearModelCaches()
    {
        // Clear penduduk related caches
        Cache::forget('penduduk_list');
        Cache::forget('penduduk_statistics');

        // Clear berita related caches
        Cache::forget('latest_berita');
        Cache::forget('berita_list');

        // Clear testimoni related caches
        Cache::forget('testimonials-cache');
        Cache::forget('testimoni_stats');

        // Clear UMKM related caches
        Cache::forget('umkm_list');
        Cache::forget('umkm_statistics');

        // Clear other model caches as needed
        Cache::forget('mutasi_list');
        Cache::forget('surat_pengajuan_list');
    }

    /**
     * Clear cache for specific model
     */
    protected function clearModelCache($model)
    {
        $cacheKey = strtolower($model) . '_list';
        Cache::forget($cacheKey);

        $statsCacheKey = strtolower($model) . '_statistics';
        Cache::forget($statsCacheKey);
    }

    /**
     * Clear all cache (use with caution)
     */
    protected function clearAllCache()
    {
        Cache::flush();
    }
}
