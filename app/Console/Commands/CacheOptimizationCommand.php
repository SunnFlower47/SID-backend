<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class CacheOptimizationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:optimization {--all : Clear all optimization caches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear optimization caches for better performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Clearing optimization caches...');

        // Clear specific optimization caches
        $caches = [
            'statistics_dashboard',
            'penduduk_rt_list',
            'penduduk_rw_list',
            'penduduk_dusun_list',
            'api_statistics',
            'desa_info',
            'surat_types',
            'services_list',
            'contact_info',
            'proyek_desa_list',
            'bantuan_sosial_list',
            'pengaduan_stats',
            'berita_list',
            'testimoni_list',
        ];

        $clearedCount = 0;
        foreach ($caches as $cache) {
            if (Cache::forget($cache)) {
                $clearedCount++;
                $this->line("✅ Cleared: {$cache}");
            }
        }

        // Clear all caches if --all flag is used
        if ($this->option('all')) {
            $this->info('🔄 Clearing all application caches...');

            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            $this->info('✅ All caches cleared');
        }

        $this->info("🎉 Optimization caches cleared successfully! ({$clearedCount} caches)");

        // Show performance tips
        $this->newLine();
        $this->info('💡 Performance Tips:');
        $this->line('• Run this command after data updates');
        $this->line('• Use --all flag to clear all caches');
        $this->line('• Caches will rebuild automatically on next request');
        $this->line('• Monitor cache hit rates for optimal performance');
    }
}
