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
                Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
                $allPermissions[] = $permission;
            }
        }

        // Create standard roles
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin Desa', 'guard_name' => 'web']);
        $stafRole = Role::firstOrCreate(['name' => 'Staf Desa', 'guard_name' => 'web']);
        $viewerRole = Role::firstOrCreate(['name' => 'Viewer', 'guard_name' => 'web']);

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

        // Dynamic domain based on tenant ID to prevent email collision across tenants
        $tenantId = tenant('id') ?? 'cibatu';
        $domain = ($tenantId === 'cibatu') ? 'desacibatu.com' : "{$tenantId}.desacibatu.com";

        // Read operator details from tenant metadata if defined (during Landlord onboarding)
        $tenantModel = tenant();
        $opName = $tenantModel->operator_name ?? 'Super Administrator';
        $opEmail = $tenantModel->operator_email ?? "superadmin@{$domain}";
        $opPassword = $tenantModel->operator_password ? Hash::make($tenantModel->operator_password) : Hash::make('password');

        // Create or update default users
        if ($tenantId === 'cibatu') {
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
        } else {
            $users = [
                [
                    'name' => $opName,
                    'email' => $opEmail,
                    'password' => $opPassword,
                    'role' => 'Super Admin'
                ]
            ];
        }

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password'],
                    'email_verified_at' => now(), // [SaaS] Auto-verified: akun dibuat oleh sistem, bukan self-register
                ]
            );

            // Pastikan akun lama yang belum verified juga di-update
            if (!$user->email_verified_at) {
                $user->update(['email_verified_at' => now()]);
            }


            // Assign the role
            $user->syncRoles([$userData['role']]);

            // [SaaS] Insert mapping ke database central agar user bisa login dari admin.sistem-desa-cibatu.test
            if (tenant('id')) {
                \App\Models\Central\UserTenantMap::firstOrCreate([
                    'email' => $userData['email'],
                    'tenant_id' => tenant('id')
                ]);
            }

            echo "User {$user->name} assigned role: {$userData['role']}\n";
        }
    }
}
