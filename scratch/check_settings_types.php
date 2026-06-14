<?php
chdir(__DIR__ . '/..');
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$raw = DB::table('desa_settings')->get();
foreach ($raw as $row) {
    $type = gettype($row->value);
    if ($type !== 'string' && $type !== 'NULL') {
        echo "NON-STRING: key={$row->key}, type={$type}, val=" . print_r($row->value, true) . "\n";
    }
}

// Also show what getAllAsArray returns type-wise
echo "\n--- getAllAsArray() types ---\n";
$arr = \App\Models\DesaSetting::getAllAsArray();
foreach ($arr as $k => $v) {
    $type = gettype($v);
    if ($type !== 'string' && $type !== 'NULL') {
        echo "NON-STRING: key={$k}, type={$type}, val=" . print_r($v, true) . "\n";
    }
}
echo "Done. Total keys: " . count($arr) . "\n";
