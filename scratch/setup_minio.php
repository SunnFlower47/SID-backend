<?php

chdir(__DIR__ . '/..');
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Storage;

$bucket = env('AWS_BUCKET', 'cibatu-storage');

try {
    $client = Storage::disk('s3')->getClient();

    // Daftar bucket yang ada
    $buckets = $client->listBuckets();
    $names = array_column($buckets['Buckets'], 'Name');
    echo "Bucket saat ini: " . implode(', ', $names ?: ['(kosong)']) . "\n";

    // Buat bucket jika belum ada
    if (!in_array($bucket, $names)) {
        $client->createBucket(['Bucket' => $bucket]);
        echo "Bucket '{$bucket}' berhasil dibuat!\n";
    } else {
        echo "Bucket '{$bucket}' sudah ada.\n";
    }

    // Set public-read policy
    $policy = json_encode([
        'Version' => '2012-10-17',
        'Statement' => [[
            'Effect'    => 'Allow',
            'Principal' => ['AWS' => ['*']],
            'Action'    => ['s3:GetObject'],
            'Resource'  => ["arn:aws:s3:::{$bucket}/*"],
        ]]
    ]);
    $client->putBucketPolicy(['Bucket' => $bucket, 'Policy' => $policy]);
    echo "Public-read policy diterapkan!\n";

    // Test upload
    Storage::disk('s3')->put('test/ping.txt', 'MinIO OK!');
    $url = Storage::disk('s3')->url('test/ping.txt');
    echo "Test upload OK! URL: {$url}\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
