<?php

$dir = 'app/Policies';
$replacements = [
    'penduduk' => 'kependudukan',
    'mutasi' => 'kependudukan',
    'kartu-keluarga' => 'kependudukan',
    'surat' => 'pelayanan_informasi',
    'bantuan-sosial' => 'pelayanan_informasi',
    'pengaduan' => 'pelayanan_informasi',
    'settings' => 'admin_sistem',
];

if (is_dir($dir)) {
    $it = new RecursiveDirectoryIterator($dir);
    foreach (new RecursiveIteratorIterator($it) as $file) {
        if ($file->getExtension() == 'php') {
            $path = $file->getPathname();
            $content = file_get_contents($path);
            $newContent = $content;
            
            foreach ($replacements as $old => $new) {
                // Match $user->can('old.action') or $user->hasPermissionTo('old.action')
                $newContent = preg_replace("/\'{$old}(\..*?)?\'/", "'{$new}'", $newContent);
                $newContent = preg_replace("/\"{$old}(\..*?)?\"/", "\"{$new}\"", $newContent);
            }
            
            if ($newContent !== $content) {
                file_put_contents($path, $newContent);
                echo "Updated Policy: $path\n";
            }
        }
    }
}
echo "Policy refactor completed!\n";
