<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $request = Illuminate\Http\Request::create('/dashboard', 'GET');
    $request->headers->set('HOST', 'admin.sistem-desa-cibatu.test');

    $session = app('session')->driver();
    $session->setId('4beRTRBTwMOszCw6zUmVzdcjmyeZOoQUqSLKa2QS');
    $session->start(); // load the session data
    
    echo "Session data loaded:\n";
    print_r($session->all());
    echo "\n";

    $request->setLaravelSession($session);

    \Illuminate\Support\Facades\Log::info('TEST LOG ENTRY FROM SCRIPT');
    echo "Handling request...\n";
    $response = app()->handle($request);
    
    echo 'Response Status: ' . $response->getStatusCode() . "\n";
    if ($response->isRedirection()) {
        echo 'Redirect Target: ' . $response->headers->get('Location') . "\n";
    } else {
        echo 'Content snippet: ' . substr($response->getContent(), 0, 500) . "\n";
    }
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
