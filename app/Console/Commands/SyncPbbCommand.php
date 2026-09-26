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

        // Ambil NOP yang belum pernah disinkronisasi atau disinkronkan berdasarkan prioritas status pembayaran
        $currentYear = date('Y');
        
        $objeks = PajakPbbObjek::where(function ($query) use ($currentYear) {
            $query->whereNull('last_synced_at') // Prioritas 1: Belum pernah disinkronisasi
                ->orWhere(function ($q) use ($currentYear) {
                    // Prioritas 2: Jika tagihan tahun ini BELUM LUNAS, sinkronkan ulang setelah 7 hari
                    $q->where('last_synced_at', '<', now()->subDays(7))
                      ->whereHas('tagihans', function ($t) use ($currentYear) {
                          $t->where('tahun', $currentYear)->where('status', 'BELUM LUNAS');
                      });
                })
                ->orWhere(function ($q) use ($currentYear) {
                    // Prioritas 3: Jika sudah LUNAS tahun ini, cukup sinkronkan setelah 30 hari
                    $q->where('last_synced_at', '<', now()->subDays(30))
                      ->whereHas('tagihans', function ($t) use ($currentYear) {
                          $t->where('tahun', $currentYear)->where('status', 'LUNAS');
                      });
                })
                ->orWhere(function ($q) use ($currentYear) {
                    // Prioritas 4: Fallback jika belum memiliki data tagihan tahun ini, cek setelah 14 hari
                    $q->where('last_synced_at', '<', now()->subDays(14))
                      ->whereDoesntHave('tagihans', function ($t) use ($currentYear) {
                          $t->where('tahun', $currentYear);
                      });
                });
        })
        ->orderByRaw('last_synced_at IS NULL DESC') // Prioritaskan yang belum pernah disinkronisasi
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
            dispatch_sync(new SyncPbbMapagbumiJob($objek, 3));
        }

        $this->info("Berhasil mendaftarkan {$objeks->count()} NOP ke antrean sinkronisasi.");
    }
}
