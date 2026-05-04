<?php

namespace App\Console\Commands;

use App\Models\PendudukDomisili;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckDomisiliExpiry extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'domisili:check-expiry';

    /**
     * The console command description.
     */
    protected $description = 'Auto-update status domisili menjadi expired jika tanggal_berlaku sudah lewat.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = PendudukDomisili::where('status', 'aktif')
            ->where('tanggal_berlaku', '<', now()->toDateString())
            ->update(['status' => 'expired']);

        $message = "Domisili Check Expiry: {$count} data diubah menjadi expired.";
        Log::info($message);
        $this->info($message);

        return Command::SUCCESS;
    }
}
