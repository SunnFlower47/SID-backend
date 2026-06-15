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
            'private/generated_surat',
            'private/qr_codes',
            'private/surat-pdf',
        ];

        $deletedCount = 0;
        $disk = \Illuminate\Support\Facades\Storage::disk('local');

        foreach ($directories as $dir) {
            if ($disk->exists($dir)) {
                $files = $disk->files($dir);
                foreach ($files as $file) {
                    try {
                        $lastModified = $disk->lastModified($file);
                        // Cek jika file lebih tua dari 24 jam
                        if (\Illuminate\Support\Carbon::createFromTimestamp($lastModified)->lessThan(\Illuminate\Support\Carbon::now()->subHours(24))) {
                            $disk->delete($file);
                            $deletedCount++;
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error("Gagal menghapus file temp {$file}: " . $e->getMessage());
                    }
                }
            }
        }

        $this->info("Berhasil membersihkan {$deletedCount} file temporary yang sudah kedaluwarsa.");
    }
}
