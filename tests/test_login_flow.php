<?php
// Test script: simulasi full login + dashboard request flow
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Log;

echo "=== TEST: Tenant Login Flow ===\n\n";

// 1. Cek apakah tabel users ada di db_central (stub)
echo "1. Central DB users table exists: ";
echo (Illuminate\Support\Facades\Schema::hasTable('users') ? 'YES' : 'NO') . "\n";

// 2. Cek user_tenant_map
echo "2. UserTenantMap for superadmin@desacibatu.com: ";
$map = \App\Models\Central\UserTenantMap::where('email', 'superadmin@desacibatu.com')->first();
echo ($map ? "FOUND → tenant: {$map->tenant_id}" : 'NOT FOUND') . "\n";

// 3. Initialize tenancy
echo "3. Initialize tenancy for '{$map->tenant_id}'... ";
tenancy()->initialize($map->tenant_id);
echo (tenancy()->initialized ? "OK (tenant: " . tenant('id') . ")" : "FAILED") . "\n";

// 4. Cek tabel users di tenant DB
echo "4. Tenant DB users table exists: ";
echo (\Illuminate\Support\Facades\Schema::hasTable('users') ? 'YES' : 'NO') . "\n";

// 5. Cari user
echo "5. Find user in tenant DB: ";
$user = \App\Models\User::where('email', 'superadmin@desacibatu.com')->first();
echo ($user ? "FOUND (id={$user->id}, name={$user->name})" : 'NOT FOUND') . "\n";

// 6. Cek password
echo "6. Password check: ";
$ok = \Illuminate\Support\Facades\Hash::check('password', $user->password ?? '');
echo ($ok ? 'VALID' : 'INVALID') . "\n";

// 7. Cek email_verified_at
echo "7. email_verified_at: " . ($user->email_verified_at ?? 'NULL') . "\n";

// 8. Cek roles/permissions di tenant
echo "8. Roles in tenant DB: ";
try {
    $roles = \Spatie\Permission\Models\Role::all()->pluck('name')->implode(', ');
    echo $roles ?: 'none';
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
echo "\n";

// 9. Cek user roles
echo "9. User roles: ";
try {
    $userRoles = $user->getRoleNames()->implode(', ');
    echo $userRoles ?: 'none';
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
echo "\n";

echo "\n=== DONE ===\n";
