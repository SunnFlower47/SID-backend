<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Penduduk;
use App\Models\Mutasi;
use Illuminate\Support\Facades\DB;

class SoftDeleteMutatedPenduduk extends Command
{
    protected $signature = 'soft-delete:mutated-penduduk';
    protected $description = 'Soft delete penduduk yang meninggal atau pindah keluar';

    public function handle()
    {
        $this->info('Soft deleting penduduk yang meninggal atau pindah keluar...');

        // Get all penduduk IDs that have kematian or pindah_keluar mutations
        $mutasiIds = Mutasi::whereIn('jenis_mutasi', ['kematian', 'pindah_keluar'])
            ->pluck('penduduk_id')
            ->toArray();

        $this->info("Found " . count($mutasiIds) . " penduduk to soft delete");

        // Soft delete all penduduk with kematian or pindah_keluar mutations
        $deleted = Penduduk::whereIn('id', $mutasiIds)->delete();

        // Final statistics
        $activePenduduk = Penduduk::count(); // Only counts non-deleted records
        $deletedPenduduk = Penduduk::onlyTrashed()->count();
        $totalPenduduk = $activePenduduk + $deletedPenduduk;

        $this->info("");
        $this->info("=== SOFT DELETE COMPLETED ===");
        $this->info("Soft deleted: {$deleted} penduduk");
        $this->info("Penduduk Aktif: {$activePenduduk}");
        $this->info("Penduduk Soft Deleted: {$deletedPenduduk}");
        $this->info("Total Penduduk: {$totalPenduduk}");

        // Show breakdown by mutation type
        $kematian = Mutasi::where('jenis_mutasi', 'kematian')->count();
        $pindahKeluar = Mutasi::where('jenis_mutasi', 'pindah_keluar')->count();
        $pindahRT = Mutasi::where('jenis_mutasi', 'pindah_rt_rw')->count();

        $this->info("");
        $this->info("=== MUTATION BREAKDOWN ===");
        $this->info("- Kematian: {$kematian} (soft deleted)");
        $this->info("- Pindah Keluar: {$pindahKeluar} (soft deleted)");
        $this->info("- Pindah RT/RW: {$pindahRT} (tetap aktif)");

        $this->info("✅ Soft delete completed!");
    }
}
