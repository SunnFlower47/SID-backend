<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\System\BackupService;
use Illuminate\Support\Facades\Gate;

class BackupController extends Controller
{
    protected $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->middleware(['auth', 'can:admin_sistem']);
        $this->backupService = $backupService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $backupFiles = $this->backupService->getBackupFiles();
        $diskSpace = $this->backupService->getDiskSpaceInfo();
        $stats = $this->backupService->getBackupStats();

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
            $this->backupService->createBackup($request->type, $request->name);
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
        try {
            $filePath = $this->backupService->getBackupFilePath($filename);
            
            $headers = [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => filesize($filePath),
            ];

            return response()->file($filePath, $headers);
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            abort(404, $e->getMessage());
        }
    }

    /**
     * Delete a backup file
     */
    public function destroy($filename)
    {
        try {
            $this->backupService->deleteBackup($filename);
            return back()->with('success', 'File backup berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus backup: ' . $e->getMessage());
        }
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
            $this->backupService->restoreBackup($request->filename);
            return back()->with('success', 'Database berhasil direstore dari backup!');
        } catch (\Exception $e) {
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
            $this->backupService->cleanBackups();
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
        return back()->with('error', 'Fitur export data dipindahkan ke menu Export Data.');
    }

    /**
     * Get backup statistics
     */
    public function statistics()
    {
        $stats = $this->backupService->getBackupStats();
        $stats['disk_space'] = $this->backupService->getDiskSpaceInfo();

        return response()->json($stats);
    }
}
