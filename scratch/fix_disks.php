<?php
$files = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/../app/Http/Controllers'));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $files[] = $file->getPathname();
    }
}

foreach ($files as $f) {
    $c = file_get_contents($f);
    if (strpos($c, "disk('public')") !== false) {
        file_put_contents($f, str_replace("disk('public')", "disk('s3')", $c));
        echo "Fixed $f\n";
    }
}
