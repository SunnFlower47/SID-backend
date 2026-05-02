<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\KartuKeluarga;
use App\Services\KartuKeluargaService;

$kkService = app(KartuKeluargaService::class);
$kkIds = KartuKeluarga::pluck('id');
$total = count($kkIds);

echo "Starting recalculation for $total records...\n";

foreach ($kkIds as $index => $id) {
    $kkService->recalculate($id);
    if (($index + 1) % 10 === 0) {
        echo "Processed " . ($index + 1) . " / $total\n";
    }
}

echo "Recalculation complete!\n";
