<?php

namespace App\Http\Controllers\ApiAdminPanel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class BackupController extends Controller
{
    public function index(): JsonResponse
    {
        Gate::authorize('admin_sistem');

        $backupFiles = collect();
        $backupDir = storage_path('app/private/admin-panel-desa-cibatu');
        
        if (is_dir($backupDir)) {
            $files = glob($backupDir . '/*.zip');
            $backupFiles = collect($files)->map(fn($file) => [
                'name' => basename($file),
                'size' => filesize($file),
                'created_at' => Carbon::createFromTimestamp(filemtime($file)),
            ])->sortByDesc('created_at')->values();
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'files' => $backupFiles,
                'disk' => $this->getDiskSpaceInfo(),
                'stats' => [
                    'total_files' => $backupFiles->count(),
                    'total_size' => $backupFiles->sum('size'),
                ]
            ]
        ]);
    }

    public function create(Request $request): JsonResponse
    {
        Gate::authorize('admin_sistem');
        $request->validate(['type' => 'required|in:database,files,full']);

        try {
            $filename = 'backup_' . now()->format('Y-m-d_H-i-s');
            $params = ['--filename' => $filename . '_' . $request->type . '.zip'];
            
            if ($request->type === 'database') $params['--only-db'] = true;
            if ($request->type === 'files') $params['--only-files'] = true;

            Artisan::call('backup:run', $params);

            return response()->json(['status' => 'success', 'message' => 'Backup sedang diproses']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function download($filename)
    {
        Gate::authorize('admin_sistem');
        $path = storage_path('app/private/admin-panel-desa-cibatu/' . $filename);
        if (!file_exists($path)) return response()->json(['status' => 'error', 'message' => 'File tidak ditemukan'], 404);
        return response()->download($path);
    }

    public function destroy($filename): JsonResponse
    {
        Gate::authorize('admin_sistem');
        $path = storage_path('app/private/admin-panel-desa-cibatu/' . $filename);
        if (file_exists($path)) {
            unlink($path);
            return response()->json(['status' => 'success', 'message' => 'Backup berhasil dihapus']);
        }
        return response()->json(['status' => 'error', 'message' => 'File tidak ditemukan'], 404);
    }

    private function getDiskSpaceInfo()
    {
        $total = disk_total_space(storage_path());
        $free = disk_free_space(storage_path());
        return [
            'total' => $total,
            'used' => $total - $free,
            'free' => $free,
            'percentage' => round((($total - $free) / $total) * 100, 2)
        ];
    }
}
