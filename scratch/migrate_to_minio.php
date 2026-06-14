<?php
/**
 * Migrasikan file lama dari storage/app/public ke MinIO bucket
 * File private (template, generated_surat, qr_codes) TETAP di lokal.
 */

chdir(__DIR__ . '/..');
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Storage;

// Folder-folder yang perlu dipindah ke MinIO (semua file dari storage/app/public)
$publicBase = storage_path('app/public');
$folders = [
    'berita',
    'logos',
    'struktur-desa',
    'fasilitas-desa',
    'kontak-desa',
    'umkm/fotos',
    'geojson',
    'pengaduan',
];

$successCount = 0;
$failCount = 0;

foreach ($folders as $folder) {
    $localDir = $publicBase . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $folder);
    
    if (!is_dir($localDir)) {
        echo "[SKIP] Folder tidak ada: {$folder}\n";
        continue;
    }

    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($localDir, RecursiveDirectoryIterator::SKIP_DOTS));
    
    foreach ($files as $file) {
        if ($file->isDir()) continue;
        
        $localPath = $file->getPathname();
        // Relative path yang akan disimpan di MinIO (e.g. "berita/photo.jpg")
        $relativePath = $folder . '/' . $file->getFilename();
        
        // Cek jika dalam subfolder
        $subPath = str_replace($localDir . DIRECTORY_SEPARATOR, '', $localPath);
        $remotePath = $folder . '/' . str_replace(DIRECTORY_SEPARATOR, '/', $subPath);
        
        try {
            // Upload ke MinIO
            $contents = file_get_contents($localPath);
            Storage::disk('s3')->put($remotePath, $contents);
            echo "[OK] {$remotePath}\n";
            $successCount++;
        } catch (Exception $e) {
            echo "[FAIL] {$remotePath}: " . $e->getMessage() . "\n";
            $failCount++;
        }
    }
}

echo "\n=== SELESAI ===\n";
echo "Berhasil: {$successCount} file\n";
echo "Gagal   : {$failCount} file\n";
