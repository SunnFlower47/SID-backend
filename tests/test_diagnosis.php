<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DIAGNOSIS LENGKAP: Kenapa stuck di login ===\n\n";

// 1. Redis - apakah session driver berfungsi?
echo "--- 1. SESSION DRIVER ---\n";
echo "Driver: " . config('session.driver') . "\n";
echo "Encrypt: " . (config('session.encrypt') ? 'YES' : 'NO') . "\n";
echo "Secure cookie: " . (config('session.secure') ? 'YES (HTTPS only)' : 'NO') . "\n";
echo "Same-site: " . config('session.same_site') . "\n";
echo "Domain: " . (config('session.domain') ?: '(null = current domain)') . "\n";
echo "Cookie name: " . config('session.cookie') . "\n\n";

// 2. Test Redis
echo "--- 2. REDIS CONNECTION ---\n";
try {
    $redis = Illuminate\Support\Facades\Redis::connection();
    $redis->ping();
    echo "Redis: CONNECTED\n";
    // Check session keys
    $keys = $redis->keys('*laravel*');
    echo "Session keys in Redis: " . count($keys) . "\n";
    if (count($keys) > 0) {
        echo "Sample keys: " . implode(', ', array_slice($keys, 0, 3)) . "\n";
    }
} catch (\Exception $e) {
    echo "Redis ERROR: " . $e->getMessage() . "\n";
}
echo "\n";

// 3. Cek middleware order pada route dashboard
echo "--- 3. ROUTE MIDDLEWARE CHECK ---\n";
$router = app('router');
$routes = $router->getRoutes();
foreach ($routes as $route) {
    if ($route->getName() === 'dashboard') {
        echo "Route: " . implode('|', $route->methods()) . " /dashboard\n";
        echo "Middleware: " . implode(', ', $route->gatherMiddleware()) . "\n";
        echo "Domain: " . ($route->getDomain() ?: 'any') . "\n";
    }
}
echo "\n";

// 4. Cek AdminSecurityMiddleware - apakah ada redirect aneh?
echo "--- 4. ADMIN SECURITY MIDDLEWARE ---\n";
$file = app_path('Http/Middleware/AdminSecurityMiddleware.php');
if (file_exists($file)) {
    $content = file_get_contents($file);
    if (strpos($content, 'redirect') !== false || strpos($content, 'auth') !== false) {
        echo "WARNING: AdminSecurityMiddleware ada redirect/auth logic!\n";
        // Print relevant lines
        $lines = explode("\n", $content);
        foreach ($lines as $i => $line) {
            if (stripos($line, 'redirect') !== false || stripos($line, 'auth') !== false) {
                echo "  Line " . ($i+1) . ": " . trim($line) . "\n";
            }
        }
    } else {
        echo "OK - no redirect logic\n";
    }
} else {
    echo "File not found\n";
}
echo "\n";

// 5. Cek apakah web middleware global ada 'auth' tersembunyi
echo "--- 5. GLOBAL WEB MIDDLEWARE ---\n";
$webMiddleware = app(\Illuminate\Foundation\Http\Kernel::class)->getMiddlewareGroups()['web'] ?? [];
foreach ($webMiddleware as $mw) {
    echo "  - " . (is_string($mw) ? $mw : get_class($mw)) . "\n";
}
echo "\n";

// 6. Cek apakah ada middleware yang auto-redirect
echo "--- 6. MIDDLEWARE ALIASES ---\n";
$aliases = app(\Illuminate\Foundation\Http\Kernel::class)->getRouteMiddleware();
foreach (['auth', 'tenant.auth', 'verified', 'admin.security'] as $key) {
    echo "  $key => " . ($aliases[$key] ?? 'NOT FOUND') . "\n";
}

echo "\n=== END DIAGNOSIS ===\n";
