<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:admin_sistem']);
    }

    /**
     * Get backup directory path relative to local disk
     */
    private function getBackupDir()
    {
        return config('backup.backup.name');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
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

        $diskSpace = $this->getDiskSpaceInfo();

        $stats = [
            'total_files' => $backupFiles->count(),
            'total_size' => $backupFiles->sum('size'),
            'last_backup' => $backupFiles->first()['created_at'] ?? null,
            'oldest_backup' => $backupFiles->last()['created_at'] ?? null,
        ];

        return \Inertia\Inertia::render('Tenant/Backup/Index', [
            'backupFiles' => $backupFiles,
            'diskSpace' => $diskSpace,
            'stats' => $stats
        ]);
    }

    /**
     * Create a new backup
     */
    public function create(Request $request)
    {
        $request->validate([
            'type' => 'required|in:database,files,full',
            'name' => 'nullable|string|max:255'
        ]);

        try {
            $backupName = $request->name ?: 'backup_' . now()->format('Y-m-d_H-i-s');

            switch ($request->type) {
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
            }

            return back()->with('success', 'Backup berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file
     */
    public function download($filename)
    {
        $file = $this->getBackupDir() . '/' . $filename;

        if (!Storage::disk('local')->exists($file)) {
            abort(404, 'File backup tidak ditemukan');
        }

        $filePath = Storage::disk('local')->path($file);

        $headers = [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => filesize($filePath),
        ];

        return response()->file($filePath, $headers);
    }

    /**
     * Delete a backup file
     */
    public function destroy($filename)
    {
        $file = $this->getBackupDir() . '/' . $filename;

        if (!Storage::disk('local')->exists($file)) {
            return back()->with('error', 'File backup tidak ditemukan');
        }

        Storage::disk('local')->delete($file);

        return back()->with('success', 'File backup berhasil dihapus');
    }

    /**
     * Delete a backup file (alias for destroy)
     */
    public function delete($filename)
    {
        return $this->destroy($filename);
    }

    /**
     * Restore from backup (Database Only)
     */
    public function restore(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
            'confirm' => 'required|accepted'
        ]);

        try {
            $file = $this->getBackupDir() . '/' . $request->filename;

            if (!Storage::disk('local')->exists($file)) {
                return back()->with('error', 'File backup tidak ditemukan');
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

            // Find the SQL file
            $sqlFiles = glob($extractPath . '/db-dumps/*.sql');
            
            if (empty($sqlFiles)) {
                // Clean up
                Storage::disk('local')->deleteDirectory(basename($extractPath));
                throw new \Exception('File SQL tidak ditemukan di dalam backup. Apakah ini backup database?');
            }

            $sqlPath = $sqlFiles[0];

            // Execute the SQL
            // Warning: This will drop tables and recreate them if it's a full dump
            \Illuminate\Support\Facades\DB::unprepared(file_get_contents($sqlPath));

            // Clean up
            Storage::disk('local')->deleteDirectory(basename($extractPath));

            return back()->with('success', 'Database berhasil direstore dari backup!');
        } catch (\Exception $e) {
            // Attempt to clean up on error
            if (isset($extractPath) && Storage::disk('local')->exists(basename($extractPath))) {
                Storage::disk('local')->deleteDirectory(basename($extractPath));
            }
            return back()->with('error', 'Gagal melakukan restore: ' . $e->getMessage());
        }
    }

    /**
     * Clean old backups
     */
    public function clean(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:7|max:365'
        ]);

        try {
            Artisan::call('backup:clean');
            return back()->with('success', "Proses pembersihan file backup lama berhasil dijalankan sesuai konfigurasi (spatie/laravel-backup).");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membersihkan backup: ' . $e->getMessage());
        }
    }

    /**
     * Export data to Excel
     */
    public function exportData()
    {
        // Not implemented in backup controller anymore, moved to ExportController
        return back()->with('error', 'Fitur export data dipindahkan ke menu Export Data.');
    }

    /**
     * Get backup statistics
     */
    public function statistics()
    {
        $backupDir = $this->getBackupDir();
        $backupFiles = collect();

        if (Storage::disk('local')->exists($backupDir)) {
            $files = Storage::disk('local')->files($backupDir);

            $backupFiles = collect($files)->map(function ($file) {
                $path = Storage::disk('local')->path($file);
                return [
                    'name' => basename($file),
                    'size' => file_exists($path) ? filesize($path) : 0,
                    'created_at' => file_exists($path) ? Carbon::createFromTimestamp(filemtime($path)) : null,
                ];
            })->sortByDesc('created_at');
        }

        $stats = [
            'total_files' => $backupFiles->count(),
            'total_size' => $backupFiles->sum('size'),
            'last_backup' => $backupFiles->first()['created_at'] ?? null,
            'disk_space' => $this->getDiskSpaceInfo(),
        ];

        return response()->json($stats);
    }

    /**
     * Get disk space information
     */
    private function getDiskSpaceInfo()
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
}
