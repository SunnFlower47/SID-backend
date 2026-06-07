<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissionGroups = config('permissions');
        
        $allPermissions = [];
        // Create permissions from config
        foreach ($permissionGroups as $group => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission]);
                $allPermissions[] = $permission;
            }
        }

        // Create standard roles
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin Desa']);
        $stafRole = Role::firstOrCreate(['name' => 'Staf Desa']);
        $viewerRole = Role::firstOrCreate(['name' => 'Viewer']);

        // Assign permissions to roles
        $superAdminRole->givePermissionTo($allPermissions);

        // Define Admin Desa permissions (mostly everything except sensitive settings)
        $adminPermissions = array_filter($allPermissions, function($p) {
            return !in_array($p, ['settings.roles.manage', 'settings.roles.create', 'settings.roles.edit', 'settings.roles.delete', 'backup.delete']);
        });
        $adminRole->syncPermissions($adminPermissions);

        // Define Staf Desa permissions (Pelayanan & Laporan)
        $stafPermissions = array_filter($allPermissions, function($p) {
            return str_starts_with($p, 'surat.') || str_starts_with($p, 'pengaduan.') || str_starts_with($p, 'bantuan_sosial.') || str_starts_with($p, 'laporan.');
        });
        $stafRole->syncPermissions($stafPermissions);

        // Define Viewer permissions (Only view)
        $viewerPermissions = array_filter($allPermissions, function($p) {
            return str_ends_with($p, '.view') || str_ends_with($p, '.index') || str_ends_with($p, '.show');
        });
        $viewerRole->syncPermissions($viewerPermissions);

        // Create or update default users
        $users = [
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@desacibatu.com',
                'password' => Hash::make('password'),
                'role' => 'Super Admin'
            ],
            [
                'name' => 'Administrator',
                'email' => 'admin@desacibatu.com',
                'password' => Hash::make('password'),
                'role' => 'Admin Desa'
            ],
            [
                'name' => 'Sekretaris Desa',
                'email' => 'sekretaris@desacibatu.com',
                'password' => Hash::make('password'),
                'role' => 'Admin Desa'
            ],
            [
                'name' => 'Staf Pelayanan',
                'email' => 'staf@desacibatu.com',
                'password' => Hash::make('password'),
                'role' => 'Staf Desa'
            ],
            [
                'name' => 'Kepala Desa',
                'email' => 'kepaladesa@desacibatu.com',
                'password' => Hash::make('password'),
                'role' => 'Viewer'
            ],
            [
                'name' => 'Viewer',
                'email' => 'viewer@desacibatu.com',
                'password' => Hash::make('password'),
                'role' => 'Viewer'
            ]
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password']
                ]
            );

            // Assign the role
            $user->syncRoles([$userData['role']]);

            echo "User {$user->name} assigned role: {$userData['role']}\n";
        }
    }
}
