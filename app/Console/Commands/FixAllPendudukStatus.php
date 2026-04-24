<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Penduduk;
use App\Models\Mutasi;

class FixAllPendudukStatus extends Command
{
    protected $signature = 'fix:all-penduduk-status';
    protected $description = 'Fix all penduduk status based on mutations';

    public function handle()
    {
        $this->info('Fixing all penduduk status...');

        // Get all penduduk IDs that have mutations
        $mutasiIds = Mutasi::pluck('penduduk_id')->toArray();

        $this->info("Found " . count($mutasiIds) . " penduduk with mutations");

        // Update all penduduk with mutations to tidak_aktif
        $updated = Penduduk::whereIn('id', $mutasiIds)->update(['status' => 'tidak_aktif']);

        // Final statistics
        $activePenduduk = Penduduk::where('status', 'aktif')->count();
        $inactivePenduduk = Penduduk::where('status', 'tidak_aktif')->count();
        $totalPenduduk = Penduduk::count();

        $this->info("");
        $this->info("=== STATUS FIX COMPLETED ===");
        $this->info("Updated: {$updated} penduduk");
        $this->info("Penduduk Aktif: {$activePenduduk}");
        $this->info("Penduduk Tidak Aktif: {$inactivePenduduk}");
        $this->info("Total Penduduk: {$totalPenduduk}");

        // Show breakdown by mutation type
        $kematian = Mutasi::where('jenis_mutasi', 'kematian')->count();
        $pindahKeluar = Mutasi::where('jenis_mutasi', 'pindah_keluar')->count();
        $pindahRT = Mutasi::where('jenis_mutasi', 'pindah_rt_rw')->count();

        $this->info("");
        $this->info("=== MUTATION BREAKDOWN ===");
        $this->info("- Kematian: {$kematian}");
        $this->info("- Pindah Keluar: {$pindahKeluar}");
        $this->info("- Pindah RT/RW: {$pindahRT}");

        $this->info("✅ Status fix completed!");
    }
}
