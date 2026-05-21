<?php

namespace App\Services\System;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BackupService
{
    /**
     * Get backup directory path relative to local disk
     */
    public function getBackupDir(): string
    {
        return config('backup.backup.name') ?? 'Laravel';
    }

    /**
     * Get all zip backups with metadata
     */
    public function getBackupFiles(): \Illuminate\Support\Collection
    {
        $backupDir = $this->getBackupDir();
        $backupFiles = collect();

        if (Storage::disk('local')->exists($backupDir)) {
            $files = Storage::disk('local')->files($backupDir);

            $backupFiles = collect($files)
                ->filter(function ($file) {
                    return str_ends_with($file, '.zip');
                })
                ->map(function ($file) {
                    $path = Storage::disk('local')->path($file);
                    return [
                        'name' => basename($file),
                        'path' => $file,
                        'size' => file_exists($path) ? filesize($path) : 0,
                        'created_at' => file_exists($path) ? Carbon::createFromTimestamp(filemtime($path)) : null,
                    ];
                })->sortByDesc('created_at')->values();
        }

        return $backupFiles;
    }

    /**
     * Get disk space information for backup summary
     */
    public function getDiskSpaceInfo(): array
    {
        $totalBytes = disk_total_space(storage_path());
        $freeBytes = disk_free_space(storage_path());
        $usedBytes = $totalBytes - $freeBytes;

        return [
            'total' => $totalBytes,
            'used' => $usedBytes,
            'free' => $freeBytes,
            'percentage' => $totalBytes > 0 ? round(($usedBytes / $totalBytes) * 100, 2) : 0,
        ];
    }

    /**
     * Get aggregated backup statistics
     */
    public function getBackupStats(): array
    {
        $backupFiles = $this->getBackupFiles();
        return [
            'total_files' => $backupFiles->count(),
            'total_size' => $backupFiles->sum('size'),
            'last_backup' => $backupFiles->first()['created_at'] ?? null,
            'oldest_backup' => $backupFiles->last()['created_at'] ?? null,
        ];
    }

    /**
     * Create a backup using Artisan command
     */
    public function createBackup(string $type, ?string $customName = null): void
    {
        $backupName = $customName ?: 'backup_' . now()->format('Y-m-d_H-i-s');

        switch ($type) {
            case 'database':
                Artisan::call('backup:run', [
                    '--only-db' => true,
                    '--filename' => $backupName . '_database.zip'
                ]);
                break;

            case 'files':
                Artisan::call('backup:run', [
                    '--only-files' => true,
                    '--filename' => $backupName . '_files.zip'
                ]);
                break;

            case 'full':
                Artisan::call('backup:run', [
                    '--filename' => $backupName . '_full.zip'
                ]);
                break;
            default:
                throw new \InvalidArgumentException("Tipe backup tidak valid.");
        }
    }

    /**
     * Get the absolute path of a backup file for downloading
     */
    public function getBackupFilePath(string $filename): string
    {
        $file = $this->getBackupDir() . '/' . $filename;

        if (!Storage::disk('local')->exists($file)) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('File backup tidak ditemukan');
        }

        return Storage::disk('local')->path($file);
    }

    /**
     * Delete a backup file
     */
    public function deleteBackup(string $filename): void
    {
        $file = $this->getBackupDir() . '/' . $filename;

        if (!Storage::disk('local')->exists($file)) {
            throw new \Exception('File backup tidak ditemukan');
        }

        Storage::disk('local')->delete($file);
    }

    /**
     * Restore database from backup zip file
     */
    public function restoreBackup(string $filename): void
    {
        $file = $this->getBackupDir() . '/' . $filename;

        if (!Storage::disk('local')->exists($file)) {
            throw new \Exception('File backup tidak ditemukan');
        }

        $zipPath = Storage::disk('local')->path($file);
        $extractPath = Storage::disk('local')->path('temp_restore_' . time());

        // Extract the zip file
        $zip = new \ZipArchive;
        if ($zip->open($zipPath) === TRUE) {
            $zip->extractTo($extractPath);
            $zip->close();
        } else {
            throw new \Exception('Gagal membuka file backup .zip');
        }

        try {
            // Find the SQL file
            $sqlFiles = glob($extractPath . '/db-dumps/*.sql');
            
            if (empty($sqlFiles)) {
                throw new \Exception('File SQL tidak ditemukan di dalam backup. Apakah ini backup database?');
            }

            $sqlPath = $sqlFiles[0];

            // Execute the SQL
            DB::unprepared(file_get_contents($sqlPath));
        } finally {
            // Always clean up temp folder
            if (file_exists($extractPath)) {
                Storage::disk('local')->deleteDirectory(basename($extractPath));
            }
        }
    }

    /**
     * Run Spatie clean backup routine
     */
    public function cleanBackups(): void
    {
        Artisan::call('backup:clean');
    }
}
