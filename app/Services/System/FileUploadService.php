<?php

namespace App\Services\System;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FileUploadService
{
    /**
     * Store an uploaded file.
     *
     * @param UploadedFile $file The file to upload.
     * @param string $path The directory path where the file should be stored.
     * @param string $disk The storage disk to use (default: 'public').
     * @return string|false The path to the stored file, or false on failure.
     */
    public function upload(UploadedFile $file, string $path, string $disk = 's3')
    {
        try {
            // Check storage limit if inside tenant context
            if (function_exists('tenant') && tenant() !== null) {
                if (!(app()->runningInConsole() && !app()->runningUnitTests())) {
                    $allocation = \App\Models\Central\TenantAllocation::where('tenant_id', tenant('id'))->first();
                    if ($allocation) {
                        $fileSizeMb = $file->getSize() / (1024 * 1024);
                        $storageUsedMb = $allocation->getStorageUsedMb();
                        if (($storageUsedMb + $fileSizeMb) > $allocation->storage_limit_mb) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                'file' => ["Kapasitas penyimpanan desa telah mencapai batas maksimum ({$allocation->storage_limit_mb} MB). Unggah file dibatalkan."]
                            ]);
                        }
                    }
                }
            }

            $storedPath = $file->store($path, $disk);
            
            // Log file upload activity
            if ($storedPath) {
                $fileName = basename($storedPath);
                \App\Models\Central\TenantActivityLog::log('file_uploaded', "Berhasil mengunggah file {$fileName} ke folder {$path}.");
            }

            return $storedPath;
        } catch (\Illuminate\Validation\ValidationException $valEx) {
            throw $valEx;
        } catch (\Exception $e) {
            Log::error("Failed to upload file to path '{$path}': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Replace an existing file with a new one.
     *
     * @param UploadedFile $file The new file to upload.
     * @param string|null $oldFilePath The path of the old file to delete.
     * @param string $path The directory path where the new file should be stored.
     * @param string $disk The storage disk to use (default: 'public').
     * @return string|false The path to the newly stored file, or false on failure.
     */
    public function replace(UploadedFile $file, ?string $oldFilePath, string $path, string $disk = 's3')
    {
        if ($oldFilePath) {
            $this->delete($oldFilePath, $disk);
        }

        return $this->upload($file, $path, $disk);
    }

    /**
     * Delete a file from storage.
     *
     * @param string $filePath The path of the file to delete.
     * @param string $disk The storage disk to use (default: 'public').
     * @return bool True if successful, false otherwise.
     */
    public function delete(string $filePath, string $disk = 's3'): bool
    {
        try {
            if (Storage::disk($disk)->exists($filePath)) {
                return Storage::disk($disk)->delete($filePath);
            }
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to delete file '{$filePath}': " . $e->getMessage());
            return false;
        }
    }
}
