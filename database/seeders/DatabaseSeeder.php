<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * This is the main entry point for resetting the system to a clean initial state.
     */
    public function run(): void
    {
        // 0. Seed Default Central Roles
        $defaultRoles = [
            [
                'name' => 'superadmin',
                'display_name' => 'Super Admin',
                'permissions' => ['manage-central-users', 'manage-allocations', 'manage-tenants', 'broadcast-announcements'],
            ],
            [
                'name' => 'operator_onboarding',
                'display_name' => 'Operator Onboarding',
                'permissions' => ['manage-tenants', 'broadcast-announcements'],
            ],
            [
                'name' => 'operator_monitoring',
                'display_name' => 'Operator Monitoring',
                'permissions' => [],
            ],
        ];

        foreach ($defaultRoles as $roleData) {
            \App\Models\Central\CentralRole::updateOrCreate(
                ['name' => $roleData['name']],
                [
                    'display_name' => $roleData['display_name'],
                    'permissions' => $roleData['permissions'],
                ]
            );
        }

        // 1. Akun Super Admin Central (Landlord)
        \App\Models\Central\CentralUser::firstOrCreate(
            ['email' => 'admin@central.go.id'],
            [
                'name' => 'Super Admin Central',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'role' => 'superadmin',
            ]
        );

        // 2. Default Central Settings
        $defaultSettings = [
            'default_max_users' => '10',
            'default_storage_limit_mb' => '1024',
            'diskominfo_hotline' => '081234567890',
            'diskominfo_email' => 'admin@central.go.id',
            'central_base_domain' => 'sistem-desa-cibatu.test',
            'central_admin_domain' => 'admin.sistem-desa-cibatu.test',
        ];

        foreach ($defaultSettings as $key => $value) {
            \App\Models\Central\CentralSetting::firstOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $this->command->info("Central DB seeded successfully (Landlord Account & Settings created).");
        
        $this->command->warn("NOTE: Data desa (roles, settings, penduduk) tidak di-seed di sini.");
        $this->command->warn("Data desa akan di-seed secara otomatis melalui TenantInitialSeeder saat tenant baru dibuat.");
    }
}
