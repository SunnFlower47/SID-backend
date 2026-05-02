<?php

$dirs = ['app/Http/Controllers', 'routes'];
$replacements = [
    // Kependudukan
    'penduduk' => 'kependudukan',
    'mutasi' => 'kependudukan',
    'kartu[-_]keluarga' => 'kependudukan',
    'pisah-kk' => 'kependudukan',
    
    // Pelayanan Informasi
    'surat' => 'pelayanan_informasi',
    'bantuan_sosial' => 'pelayanan_informasi',
    'pengaduan' => 'pelayanan_informasi',
    'testimoni' => 'pelayanan_informasi',
    'berita' => 'pelayanan_informasi',
    'umkm' => 'pelayanan_informasi',
    'kontak-desa' => 'pelayanan_informasi',
    'fasilitas-desa' => 'pelayanan_informasi',
    'struktur-desa' => 'pelayanan_informasi',
    'contact[-_]messages' => 'pelayanan_informasi',
    
    // Keuangan
    'transparansi-desa' => 'keuangan',
    'anggaran' => 'keuangan',
    
    // Laporan & Statistik
    'laporan' => 'laporan_statistik',
    'statistics' => 'laporan_statistik',
    'comparison' => 'laporan_statistik',
    
    // Admin Sistem
    'settings' => 'admin_sistem',
    'wilayah' => 'admin_sistem',
    'audit[-_]log' => 'admin_sistem',
    'backup' => 'admin_sistem',
    'import' => 'admin_sistem',
    'export' => 'admin_sistem',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    
    $it = new RecursiveDirectoryIterator($dir);
    foreach (new RecursiveIteratorIterator($it) as $file) {
        if ($file->getExtension() == 'php') {
            $path = $file->getPathname();
            $content = file_get_contents($path);
            $newContent = $content;
            
            foreach ($replacements as $old => $new) {
                // Handle Gate::authorize('old.action') or Gate::authorize('old')
                $newContent = preg_replace("/Gate::authorize\(\'{$old}(\..*?)?\'\)/", "Gate::authorize('{$new}')", $newContent);
                
                // Handle $this->middleware('can:old.action') or $this->middleware('can:old')
                // Case 1: single string
                $newContent = preg_replace("/\'can:{$old}(\..*?)?\'/", "'can:{$new}'", $newContent);
                // Case 2: double quotes
                $newContent = preg_replace("/\"can:{$old}(\..*?)?\"/", "\"can:{$new}\"", $newContent);
            }
            
            if ($newContent !== $content) {
                file_put_contents($path, $newContent);
                echo "Updated: $path\n";
            }
        }
    }
}
echo "Full refactor completed!\n";
