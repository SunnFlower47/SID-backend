<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    
    $tenant = App\Models\Tenant::create(['id' => 'cibatu']);
    $tenant->domains()->create(['domain' => 'cibatu.sistem-desa-cibatu.test']);
    $tenant->domains()->create(['domain' => 'sistem-desa-cibatu.test']);
    echo "Tenant Cibatu berhasil dibuat dan di-seed!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
