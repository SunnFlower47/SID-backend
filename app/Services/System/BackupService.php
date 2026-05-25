<?php

namespace App\Services\System;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BackupService
{
    /**
     * Get backup directory name (relative to local disk root)
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
        $backupDir   = $this->getBackupDir();
        $backupFiles = collect();

        if (Storage::disk('local')->exists($backupDir)) {
            $files = Storage::disk('local')->files($backupDir);

            $backupFiles = collect($files)
                ->filter(fn($file) => str_ends_with($file, '.zip'))
                ->map(function ($file) {
                    $path = Storage::disk('local')->path($file);
                    return [
                        'name'       => basename($file),
                        'path'       => $file,
                        'size'       => file_exists($path) ? filesize($path) : 0,
                        'created_at' => file_exists($path) ? Carbon::createFromTimestamp(filemtime($path)) : null,
                    ];
                })
                ->sortByDesc('created_at')
                ->values();
        }

        return $backupFiles;
    }

    /**
     * Get disk space information
     */
    public function getDiskSpaceInfo(): array
    {
        $totalBytes = disk_total_space(storage_path());
        $freeBytes  = disk_free_space(storage_path());
        $usedBytes  = $totalBytes - $freeBytes;

        return [
            'total'      => $totalBytes,
            'used'       => $usedBytes,
            'free'       => $freeBytes,
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
            'total_files'    => $backupFiles->count(),
            'total_size'     => $backupFiles->sum('size'),
            'last_backup'    => $backupFiles->first()['created_at'] ?? null,
            'oldest_backup'  => $backupFiles->last()['created_at'] ?? null,
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
                Artisan::call('backup:run', ['--only-db' => true, '--filename' => $backupName . '_database.zip']);
                break;
            case 'files':
                Artisan::call('backup:run', ['--only-files' => true, '--filename' => $backupName . '_files.zip']);
                break;
            case 'full':
                Artisan::call('backup:run', ['--filename' => $backupName . '_full.zip']);
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
     * Restore database from a backup zip file.
     * Strategy: try mysql CLI (most reliable), fallback to PDO exec().
     */
    public function restoreBackup(string $filename): void
    {
        $file = $this->getBackupDir() . '/' . $filename;

        if (!Storage::disk('local')->exists($file)) {
            throw new \Exception('File backup tidak ditemukan: ' . $filename);
        }

        $zipPath     = Storage::disk('local')->path($file);
        $extractPath = storage_path('app/temp_restore_' . time());

        Log::info('[Restore] Starting restore from: ' . $filename);

        // ── 1. Extract zip ───────────────────────────────────────────────────
        $zip = new \ZipArchive;
        if ($zip->open($zipPath) !== true) {
            throw new \Exception('Gagal membuka file .zip. File mungkin rusak.');
        }
        $zip->extractTo($extractPath);
        $zip->close();

        Log::info('[Restore] Extracted to: ' . $extractPath);

        try {
            // ── 2. Find .sql or .sql.gz file (recursive, cross-platform) ────
            $sqlPath   = null;
            $sqlGzPath = null;

            $iter = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($extractPath));
            foreach ($iter as $f) {
                if (!$f->isFile()) continue;
                $lower = strtolower($f->getFilename());
                if (!$sqlPath && str_ends_with($lower, '.sql')) {
                    $sqlPath = $f->getPathname();
                }
                if (!$sqlGzPath && str_ends_with($lower, '.sql.gz')) {
                    $sqlGzPath = $f->getPathname();
                }
                if ($sqlPath) break; // prefer .sql over .gz
            }

            if (!$sqlPath && !$sqlGzPath) {
                throw new \Exception('File SQL tidak ditemukan di dalam backup. Pastikan ini adalah file backup database.');
            }

            // Decompress .sql.gz jika tidak ada .sql
            if (!$sqlPath && $sqlGzPath) {
                $sqlPath     = $extractPath . '/dump.sql';
                $decompressed = gzdecode(file_get_contents($sqlGzPath));
                if ($decompressed === false) {
                    throw new \Exception('Gagal decompress file .sql.gz dari backup.');
                }
                file_put_contents($sqlPath, $decompressed);
            }

            Log::info('[Restore] SQL file: ' . $sqlPath . ' (' . number_format(filesize($sqlPath)) . ' bytes)');

            // ── 3. Try mysql CLI (most reliable for large dumps) ─────────────
            $mysqlBin = $this->findMysqlBinary();

            if ($mysqlBin) {
                Log::info('[Restore] Using mysql CLI binary: ' . $mysqlBin);
                $this->restoreViaCli($mysqlBin, $sqlPath, $extractPath);
            } else {
                Log::info('[Restore] mysql CLI not found, falling back to PDO.');
                $this->restoreViaPdo($sqlPath);
            }

            Log::info('[Restore] Restore completed successfully.');

        } finally {
            // Always clean temp dir
            if (is_dir($extractPath)) {
                $this->deleteDirectory($extractPath);
                Log::info('[Restore] Cleaned up temp dir: ' . $extractPath);
            }
        }
    }

    // ── Private Helpers ───────────────────────────────────────────────────────

    private function restoreViaCli(string $mysqlBin, string $sqlPath, string $tmpDir): void
    {
        $cfg  = config('database.connections.' . config('database.default'));
        $host = $cfg['host'];
        $port = $cfg['port'] ?? 3306;
        $db   = $cfg['database'];
        $user = $cfg['username'];
        $pass = $cfg['password'];

        // Write password to temp file (avoid password in shell command)
        $myCnf = $tmpDir . DIRECTORY_SEPARATOR . '.my.cnf';
        file_put_contents($myCnf, "[client]\npassword=" . addslashes($pass) . "\n");
        @chmod($myCnf, 0600);

        $cmd = sprintf(
            '%s --defaults-extra-file=%s -h%s -P%s -u%s %s < %s 2>&1',
            escapeshellarg($mysqlBin),
            escapeshellarg($myCnf),
            escapeshellarg($host),
            escapeshellarg((string) $port),
            escapeshellarg($user),
            escapeshellarg($db),
            escapeshellarg($sqlPath)
        );

        $output     = [];
        $returnCode = 0;
        exec($cmd, $output, $returnCode);

        @unlink($myCnf);

        if ($returnCode !== 0) {
            $err = implode("\n", $output);
            Log::error('[Restore] CLI failed (exit ' . $returnCode . '): ' . $err);
            throw new \Exception('Restore via mysql CLI gagal: ' . $err);
        }

        Log::info('[Restore] CLI restore completed, exit code: ' . $returnCode);
    }

    private function restoreViaPdo(string $sqlPath): void
    {
        $sqlContent = file_get_contents($sqlPath);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            // Drop all existing tables
            $dbName   = DB::connection()->getDatabaseName();
            $tables   = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
            $tableKey = 'Tables_in_' . $dbName;
            foreach ($tables as $tbl) {
                DB::statement('DROP TABLE IF EXISTS `' . $tbl->$tableKey . '`');
            }
            Log::info('[Restore] Dropped all existing tables.');

            // Use PDO::exec() — supports multi-statement for full SQL dumps
            $pdo = DB::connection()->getPdo();
            $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);

            $result = $pdo->exec($sqlContent);
            if ($result === false) {
                $err = $pdo->errorInfo();
                throw new \Exception('PDO exec gagal: ' . ($err[2] ?? 'Unknown PDO error'));
            }

            Log::info('[Restore] PDO exec completed. Affected rows/result: ' . $result);
        } finally {
            try { DB::statement('SET FOREIGN_KEY_CHECKS=1'); } catch (\Exception $e) {}
        }
    }

    public function findMysqlBinary(): ?string
    {
        $candidates = [
            'mysql',
            '/usr/bin/mysql',
            '/usr/local/bin/mysql',
            'C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe',
            'C:\Program Files\MySQL\MySQL Server 8.4\bin\mysql.exe',
            'C:\xampp\mysql\bin\mysql.exe',
            'C:\laragon\bin\mysql\mysql-8.0\bin\mysql.exe',
            'C:\laragon\bin\mysql\mysql-8.4\bin\mysql.exe',
        ];

        foreach ($candidates as $candidate) {
            $test = @shell_exec(escapeshellarg($candidate) . ' --version 2>&1');
            if ($test && str_contains(strtolower($test), 'mysql')) {
                return $candidate;
            }
        }

        return null;
    }

    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }
        rmdir($dir);
    }

    /**
     * Run Spatie clean backup routine
     */
    public function cleanBackups(): void
    {
        Artisan::call('backup:clean');
    }
}
