<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Force clear Spatie cache
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

use App\Models\User;
use Spatie\Permission\Models\Role;

$email = 'admin@desacibatu.com';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "User with email $email not found.\n";
} else {
    echo "User: " . $user->name . "\n";
    echo "Roles: " . json_encode($user->getRoleNames()) . "\n";
    echo "Permissions from roles: " . json_encode($user->getPermissionsViaRoles()->pluck('name')) . "\n";
}

$role = Role::where('name', 'Admin Desa')->first();
if ($role) {
    echo "Role 'Admin Desa' Permissions: " . json_encode($role->permissions->pluck('name')) . "\n";
}
