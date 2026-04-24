<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class StorageHelper
{
    /**
     * Get file URL yang kompatibel dengan shared hosting
     */
    public static function getFileUrl($path, $disk = 'local')
    {
        // Cek apakah storage link ada
        if (is_link(public_path('storage'))) {
            return asset('storage/' . $path);
        }

        // Fallback: gunakan route untuk file yang tidak bisa diakses langsung
        return route('file.download', ['path' => base64_encode($path)]);
    }

    /**
     * Cek apakah file ada di storage
     */
    public static function fileExists($path, $disk = 'local')
    {
        // Coba dengan Storage facade dulu
        if (Storage::disk($disk)->exists($path)) {
            return true;
        }

        // Fallback: cek dengan path langsung
        $fullPath = storage_path('app/' . $path);
        return file_exists($fullPath);
    }

    /**
     * Download file dengan headers yang benar
     */
    public static function downloadFile($path, $filename = null)
    {
        $storagePath = storage_path('app');
        $fullPath = realpath($storagePath . '/' . $path);

        // Security Check: Ensure file is inside storage/app
        if (!$fullPath || !str_starts_with($fullPath, $storagePath)) {
            abort(403, 'Access denied');
        }

        if (!file_exists($fullPath)) {
            abort(404, 'File tidak ditemukan');
        }

        $filename = $filename ?: basename($path);
        $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';

        $headers = [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => filesize($fullPath),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return response()->file($fullPath, $headers);
    }

    /**
     * Cek status storage link
     */
    public static function isStorageLinked()
    {
        return is_link(public_path('storage'));
    }

    /**
     * Buat storage link jika belum ada (untuk VPS)
     */
    public static function createStorageLink()
    {
        if (!self::isStorageLinked()) {
            try {
                \Artisan::call('storage:link');
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        return true;
    }
}





