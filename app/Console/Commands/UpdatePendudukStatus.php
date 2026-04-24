<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Penduduk;
use App\Models\Mutasi;
use Illuminate\Support\Facades\DB;

class UpdatePendudukStatus extends Command
{
    protected $signature = 'update:penduduk-status';
    protected $description = 'Update penduduk status based on mutations';

    public function handle()
    {
        $this->info('Updating penduduk status based on mutations...');

        // Get all penduduk with kematian or pindah_keluar mutations
        $mutations = Mutasi::whereIn('jenis_mutasi', ['kematian', 'pindah_keluar'])->get();

        $this->info("Found {$mutations->count()} mutations to process");

        $updated = 0;

        foreach ($mutations as $mutasi) {
            $penduduk = Penduduk::find($mutasi->penduduk_id);

            if ($penduduk) {
                $penduduk->update(['status' => 'tidak_aktif']);
                $updated++;

                if ($updated % 50 == 0) {
                    $this->info("Updated {$updated} penduduk...");
                }
            }
        }

        // Final statistics
        $activePenduduk = Penduduk::where('status', 'aktif')->count();
        $inactivePenduduk = Penduduk::where('status', 'tidak_aktif')->count();
        $totalPenduduk = Penduduk::count();

        $this->info("");
        $this->info("=== STATUS UPDATE COMPLETED ===");
        $this->info("Updated: {$updated} penduduk");
        $this->info("Penduduk Aktif: {$activePenduduk}");
        $this->info("Penduduk Tidak Aktif: {$inactivePenduduk}");
        $this->info("Total Penduduk: {$totalPenduduk}");

        // Show breakdown by mutation type
        $this->info("");
        $this->info("=== MUTATION BREAKDOWN ===");
        $kematian = Mutasi::where('jenis_mutasi', 'kematian')->count();
        $pindahKeluar = Mutasi::where('jenis_mutasi', 'pindah_keluar')->count();
        $pindahRT = Mutasi::where('jenis_mutasi', 'pindah_rt_rw')->count();

        $this->info("- Kematian: {$kematian}");
        $this->info("- Pindah Keluar: {$pindahKeluar}");
        $this->info("- Pindah RT/RW: {$pindahRT}");

        $this->info("✅ Status update completed!");
    }
}
