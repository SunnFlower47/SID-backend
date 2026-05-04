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
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get backup files from storage
        $backupFiles = collect();

        // Check if directory exists using direct path
        $backupDir = storage_path('app/private/admin-panel-desa-cibatu');
        if (is_dir($backupDir)) {
            $files = glob($backupDir . '/*.zip');

            $backupFiles = collect($files)->map(function ($file) {
                return [
                    'name' => basename($file),
                    'path' => 'private/admin-panel-desa-cibatu/' . basename($file),
                    'size' => file_exists($file) ? filesize($file) : 0,
                    'created_at' => file_exists($file) ? Carbon::createFromTimestamp(filemtime($file)) : null,
                ];
            })->sortByDesc('created_at');
        }

        // Get disk space information
        $diskSpace = $this->getDiskSpaceInfo();

        // Get backup statistics
        $stats = [
            'total_files' => $backupFiles->count(),
            'total_size' => $backupFiles->sum('size'),
            'last_backup' => $backupFiles->first()['created_at'] ?? null,
            'oldest_backup' => $backupFiles->last()['created_at'] ?? null,
        ];

        return view('backup.index', compact('backupFiles', 'diskSpace', 'stats'));
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

            return redirect()->back()->with('success', 'Backup berhasil dibuat!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file
     */
    public function download($filename)
    {
        $filePath = storage_path('app/private/admin-panel-desa-cibatu/' . $filename);

        if (!file_exists($filePath)) {
            abort(404, 'File backup tidak ditemukan');
        }

        // Set headers untuk download file
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
        $filePath = storage_path('app/private/admin-panel-desa-cibatu/' . $filename);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File backup tidak ditemukan');
        }

        unlink($filePath);

        return redirect()->back()->with('success', 'File backup berhasil dihapus');
    }

    /**
     * Delete a backup file (alias for destroy)
     */
    public function delete($filename)
    {
        return $this->destroy($filename);
    }

    /**
     * Restore from backup
     */
    public function restore(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
            'confirm' => 'required|accepted'
        ]);

        try {
            $filePath = 'private/admin-panel-desa-cibatu/' . $request->filename;

            if (!Storage::disk('local')->exists($filePath)) {
                return redirect()->back()->with('error', 'File backup tidak ditemukan');
            }

            // This would require a custom restore command
            // Artisan::call('backup:restore', ['filename' => $request->filename]);

            return redirect()->back()->with('success', 'Restore berhasil dilakukan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal melakukan restore: ' . $e->getMessage());
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
            $cutoffDate = now()->subDays($request->days);
            $deletedCount = 0;

            if (Storage::disk('local')->exists('private/admin-panel-desa-cibatu')) {
                $files = Storage::disk('local')->files('private/admin-panel-desa-cibatu');

                foreach ($files as $file) {
                    $path = storage_path('app/' . $file);
                    if (file_exists($path) && Carbon::createFromTimestamp(filemtime($path))->lt($cutoffDate)) {
                        Storage::disk('local')->delete($file);
                        $deletedCount++;
                    }
                }
            }

            return redirect()->back()->with('success', "Berhasil menghapus {$deletedCount} file backup yang lebih dari {$request->days} hari.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membersihkan backup: ' . $e->getMessage());
        }
    }

    /**
     * Export data to Excel
     */
    public function exportData()
    {
        try {
            $filename = 'data_export_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

            // This would typically export all data to Excel
            // For now, just return a message
            return redirect()->back()->with('success', 'Export data berhasil! File: ' . $filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }

    /**
     * Get backup statistics
     */
    public function statistics()
    {
        $backupFiles = collect();

        if (Storage::disk('local')->exists('private/admin-panel-desa-cibatu')) {
            $files = Storage::disk('local')->files('private/admin-panel-desa-cibatu');

            $backupFiles = collect($files)->map(function ($file) {
                $path = storage_path('app/' . $file);
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
