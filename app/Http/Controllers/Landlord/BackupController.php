<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\System\BackupService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    protected $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    /**
     * Create a new landlord backup
     */
    public function create(Request $request)
    {
        Gate::authorize('manage-central-users');

        $request->validate([
            'type' => 'required|in:database,files,full',
            'name' => 'nullable|string|max:255'
        ]);

        try {
            $type = $request->type;

            if ($type === 'database') {
                Artisan::call('app:backup-run', ['--only-db' => true]);
            } elseif ($type === 'files') {
                Artisan::call('app:backup-run', ['--only-files' => true]);
            } else {
                Artisan::call('app:backup-run');
            }

            return redirect()->back()->with('success', 'Backup SaaS berhasil dipicu di background!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal membuat backup: ' . $e->getMessage()]);
        }
    }

    /**
     * Download a backup file
     */
    public function download($filename)
    {
        Gate::authorize('manage-central-users');

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
        Gate::authorize('manage-central-users');

        try {
            $this->backupService->deleteBackup($filename);
            return redirect()->back()->with('success', 'File backup SaaS berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus backup: ' . $e->getMessage()]);
        }
    }
}
