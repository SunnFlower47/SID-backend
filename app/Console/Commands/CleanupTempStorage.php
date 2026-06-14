<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Carbon;

class CleanupTempStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-temp-storage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bersihkan file temporary (surat, qr_code) yang umurnya lebih dari 24 jam';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $directories = [
            storage_path('app/private/generated_surat'),
            storage_path('app/private/qr_codes'),
            storage_path('app/private/surat-pdf'),
        ];

        $deletedCount = 0;

        foreach ($directories as $dir) {
            if (File::exists($dir)) {
                $files = File::files($dir);
                foreach ($files as $file) {
                    // Cek jika file lebih tua dari 24 jam
                    if (Carbon::createFromTimestamp($file->getMTime())->lessThan(Carbon::now()->subHours(24))) {
                        File::delete($file->getPathname());
                        $deletedCount++;
                    }
                }
            }
        }

        $this->info("Berhasil membersihkan {$deletedCount} file temporary yang sudah kedaluwarsa.");
    }
}
