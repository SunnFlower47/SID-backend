<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearOptimizationCache extends Command
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

        if ($this->option('all')) {
            // Clear all caches
            Cache::flush();
            $this->info('✅ All caches cleared');
        } else {
            // Clear specific optimization caches
            $caches = [
                'api_statistics',
                'statistics_dashboard',
                'penduduk_rt_list',
                'penduduk_rw_list',
                'penduduk_dusun_list',
                'desa_info',
                'kartu_keluarga_list'
            ];

            foreach ($caches as $cache) {
                Cache::forget($cache);
                $this->line("  - Cleared: {$cache}");
            }

            $this->info('✅ Optimization caches cleared');
        }

        $this->newLine();
        $this->info('🚀 System is now ready with fresh data!');

        return Command::SUCCESS;
    }
}






