<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Penduduk;
use Illuminate\Support\Facades\DB;

class FixRTFormat extends Command
{
    protected $signature = 'fix:rt-format {--dry-run : Run the command without making any changes}';
    protected $description = 'Fix RT format to 3 digits (001, 002, etc.)';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $this->info(($dryRun ? 'DRY RUN MODE - No changes will be made' : '') . "\nMemperbaiki format RT...");

        // Get all unique RT values
        $rtValues = Penduduk::select('rt')
            ->distinct()
            ->orderBy('rt')
            ->pluck('rt');

        $this->info("Ditemukan " . $rtValues->count() . " format RT yang berbeda:");
        foreach ($rtValues as $rt) {
            $count = Penduduk::where('rt', $rt)->count();
            $this->line("- '{$rt}': {$count} penduduk");
        }

        $this->info("\nMemperbaiki format RT...");
        $updatedCount = 0;

        foreach ($rtValues as $rt) {
            $normalizedRT = $this->normalizeRT($rt);

            if ($rt !== $normalizedRT) {
                $count = Penduduk::where('rt', $rt)->count();
                $this->info("Mengubah '{$rt}' menjadi '{$normalizedRT}' ({$count} penduduk)");

                if (!$dryRun) {
                    Penduduk::where('rt', $rt)->update(['rt' => $normalizedRT]);
                    $updatedCount += $count;
                }
            }
        }

        if (!$dryRun) {
            $this->info("\n✅ Format RT berhasil diperbaiki. Total {$updatedCount} penduduk diupdate.");
        } else {
            $this->info("\nDry run complete. No changes were made.");
        }

        // Show final RT distribution
        $this->info("\nDistribusi RT setelah perbaikan:");
        $finalRTs = Penduduk::selectRaw('rt, COUNT(*) as count')
            ->groupBy('rt')
            ->orderBy('rt')
            ->get();

        foreach ($finalRTs as $rt) {
            $this->line("- RT {$rt->rt}: {$rt->count} penduduk");
        }

        return 0;
    }

    private function normalizeRT($rt)
    {
        // Remove any non-digit characters and pad with leading zeros
        $digits = preg_replace('/\D/', '', $rt);

        if (empty($digits)) {
            return '001'; // Default if no digits found
        }

        // Pad to 3 digits
        return str_pad($digits, 3, '0', STR_PAD_LEFT);
    }
}

