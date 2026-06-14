<?php
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('app/Http/Controllers'));
$count = 0;
foreach ($files as $file) {
    if ($file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $original = $content;
        
        // Replace ->store('path', 'public') or ->store("path", "public")
        $content = preg_replace("/->store\((['\"])([^'\"]+)\\1\s*,\s*(['\"])(public|local)\\3\)/", "->store('$2')", $content);
        
        // Replace ->storeAs('path', $filename, 'public')
        $content = preg_replace("/->storeAs\((['\"])([^'\"]+)\\1\s*,\s*([^,]+?)\s*,\s*(['\"])(public|local)\\4\)/", "->storeAs('$2', $3)", $content);
        
        if ($content !== $original) {
            file_put_contents($file->getPathname(), $content);
            echo "Updated: " . $file->getPathname() . "\n";
            $count++;
        }
    }
}
echo "Total files updated: $count\n";
