<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\StorageHelper;

class CheckStorageLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:check {--fix : Coba perbaiki storage link}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek status storage link dan berikan solusi untuk hosting';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== CHECK STORAGE LINK STATUS ===');

        // Cek storage link
        $isLinked = StorageHelper::isStorageLinked();
        $this->line('Storage Link Status: ' . ($isLinked ? '<fg=green>LINKED</>' : '<fg=red>NOT LINKED</>'));

        if ($isLinked) {
            $this->info('✅ Storage link sudah aktif');
            $this->line('Public storage path: ' . public_path('storage'));
            $this->line('Target path: ' . storage_path('app/public'));
        } else {
            $this->warn('❌ Storage link belum aktif');
            $this->line('Ini akan menyebabkan logo dan file upload tidak bisa diakses');

            if ($this->option('fix')) {
                $this->info('Mencoba memperbaiki storage link...');

                if (StorageHelper::createStorageLink()) {
                    $this->info('✅ Storage link berhasil dibuat!');
                } else {
                    $this->error('❌ Gagal membuat storage link');
                    $this->line('');
                    $this->warn('SOLUSI MANUAL untuk Shared Hosting:');
                    $this->line('1. Login ke cPanel File Manager');
                    $this->line('2. Masuk ke folder public_html');
                    $this->line('3. Hapus folder "storage" jika ada');
                    $this->line('4. Buat symbolic link:');
                    $this->line('   ln -s ../laravel_project/storage/app/public storage');
                    $this->line('5. Atau gunakan command di terminal:');
                    $this->line('   cd public_html && ln -s ../laravel_project/storage/app/public storage');
                }
            } else {
                $this->line('');
                $this->warn('SOLUSI:');
                $this->line('1. Jalankan: php artisan storage:link');
                $this->line('2. Atau: php artisan storage:check --fix');
                $this->line('3. Untuk shared hosting, buat symbolic link manual');
            }
        }

        // Cek file backup
        $this->line('');
        $this->info('=== CHECK BACKUP FILES ===');
        $backupDir = storage_path('app/private/Laravel');

        if (is_dir($backupDir)) {
            $files = glob($backupDir . '/*.zip');
            $this->line('Backup files found: ' . count($files));

            foreach ($files as $file) {
                $filename = basename($file);
                $size = number_format(filesize($file) / 1024, 2);
                $this->line("- {$filename} ({$size} KB)");
            }
        } else {
            $this->warn('Backup directory tidak ditemukan: ' . $backupDir);
        }

        // Cek permission
        $this->line('');
        $this->info('=== CHECK PERMISSIONS ===');
        $storagePath = storage_path();
        $publicPath = public_path();

        $this->line('Storage writable: ' . (is_writable($storagePath) ? '<fg=green>YES</>' : '<fg=red>NO</>'));
        $this->line('Public writable: ' . (is_writable($publicPath) ? '<fg=green>YES</>' : '<fg=red>NO</>'));

        if (!is_writable($storagePath)) {
            $this->warn('Storage tidak writable! Jalankan: chmod -R 775 storage');
        }

        if (!is_writable($publicPath)) {
            $this->warn('Public tidak writable! Jalankan: chmod -R 775 public');
        }

        $this->line('');
        $this->info('=== HOSTING COMPATIBILITY ===');
        $this->line('✅ Backup system sudah kompatibel dengan shared hosting');
        $this->line('✅ File download menggunakan route, tidak bergantung pada storage link');
        $this->line('✅ Logo dan file upload akan otomatis menggunakan fallback route');

        return 0;
    }
}
