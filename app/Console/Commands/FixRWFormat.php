<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Penduduk;
use Illuminate\Support\Facades\DB;

class FixRWFormat extends Command
{
    protected $signature = 'fix:rw-format {--dry-run : Run the command without making any changes}';
    protected $description = 'Fix RW format to 3 digits (001, 002, etc.)';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $this->info(($dryRun ? 'DRY RUN MODE - No changes will be made' : '') . "\nMemperbaiki format RW...");

        // Get all unique RW values
        $rwValues = Penduduk::select('rw')
            ->distinct()
            ->orderBy('rw')
            ->pluck('rw');

        $this->info("Ditemukan " . $rwValues->count() . " format RW yang berbeda:");
        foreach ($rwValues as $rw) {
            $count = Penduduk::where('rw', $rw)->count();
            $this->line("- '{$rw}': {$count} penduduk");
        }

        $this->info("\nMemperbaiki format RW...");
        $updatedCount = 0;

        foreach ($rwValues as $rw) {
            $normalizedRW = $this->normalizeRW($rw);

            if ($rw !== $normalizedRW) {
                $count = Penduduk::where('rw', $rw)->count();
                $this->info("Mengubah '{$rw}' menjadi '{$normalizedRW}' ({$count} penduduk)");

                if (!$dryRun) {
                    Penduduk::where('rw', $rw)->update(['rw' => $normalizedRW]);
                    $updatedCount += $count;
                }
            }
        }

        if (!$dryRun) {
            $this->info("\n✅ Format RW berhasil diperbaiki. Total {$updatedCount} penduduk diupdate.");
        } else {
            $this->info("\nDry run complete. No changes were made.");
        }

        // Show final RW distribution
        $this->info("\nDistribusi RW setelah perbaikan:");
        $finalRWs = Penduduk::selectRaw('rw, COUNT(*) as count')
            ->groupBy('rw')
            ->orderBy('rw')
            ->get();

        foreach ($finalRWs as $rw) {
            $this->line("- RW {$rw->rw}: {$rw->count} penduduk");
        }

        return 0;
    }

    private function normalizeRW($rw)
    {
        // Remove any non-digit characters and pad with leading zeros
        $digits = preg_replace('/\D/', '', $rw);

        if (empty($digits)) {
            return '001'; // Default if no digits found
        }

        // Pad to 3 digits
        return str_pad($digits, 3, '0', STR_PAD_LEFT);
    }
}

