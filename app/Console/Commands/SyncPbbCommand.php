<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PajakPbbObjek;
use App\Jobs\SyncPbbMapagbumiJob;

class SyncPbbCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pbb:sync {--limit=10 : Jumlah maksimal NOP yang disinkronisasi dalam 1 putaran}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi data PBB (Objek & Tagihan) dari server Mapagbumi untuk NOP yang belum lengkap atau sudah lama tidak disinkronisasi.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        
        $this->info("Memulai proses sinkronisasi PBB (Maksimal: {$limit} NOP)...");

        // Ambil NOP yang belum pernah disinkronisasi atau terakhir sinkron > 1 hari
        $objeks = PajakPbbObjek::whereNull('last_synced_at')
            ->orWhere('last_synced_at', '<', now()->subDays(1))
            ->orderBy('last_synced_at', 'asc')
            ->limit($limit)
            ->get();

        if ($objeks->isEmpty()) {
            $this->info("Semua NOP sudah mutakhir.");
            return;
        }

        foreach ($objeks as $objek) {
            $this->line("Dispatching job untuk NOP: {$objek->nop} ...");
            // dispatch_sync agar langsung dieksekusi saat cron jalan (tidak bergantung pada queue worker)
            dispatch_sync(new SyncPbbMapagbumiJob($objek, 5));
        }

        $this->info("Berhasil mendaftarkan {$objeks->count()} NOP ke antrean sinkronisasi.");
    }
}
