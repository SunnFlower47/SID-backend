<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateAllPermissions extends Command
{
    protected $signature = 'permissions:create-all';
    protected $description = 'Create all system permissions';

    public function handle()
    {
        $this->info('Creating all system permissions...');

        // Define all permissions
        $permissions = [
            // Penduduk permissions
            'penduduk.view', 'penduduk.create', 'penduduk.edit', 'penduduk.delete', 'penduduk.import', 'penduduk.export',
            
            // Mutasi permissions
            'mutasi.view', 'mutasi.create', 'mutasi.edit', 'mutasi.delete',
            
            // Laporan permissions
            'laporan.view', 'statistics.view',
            
            // Admin permissions
            'audit_log.view', 'backup.manage',
            
            // Surat permissions (already created)
            'surat.view', 'surat.create', 'surat.edit', 'surat.delete',
            
            // Settings permissions (already created)
            'settings.view', 'settings.edit', 'settings.export', 'settings.import',
            
            // Bantuan Sosial permissions (already created)
            'bantuan_sosial.view', 'bantuan_sosial.create', 'bantuan_sosial.edit', 'bantuan_sosial.delete', 'bantuan_sosial.manage_penerima',
            
            // Pengaduan permissions (already created)
            'pengaduan.view', 'pengaduan.create', 'pengaduan.edit', 'pengaduan.delete', 'pengaduan.manage'
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
            $this->line("✓ Created permission: {$permission}");
        }

        // Assign to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
            $this->info('✓ Assigned all permissions to admin role');
        }

        // Assign basic permissions to user role
        $userRole = Role::where('name', 'user')->first();
        if ($userRole) {
            $basicPermissions = [
                'penduduk.view', 'mutasi.view', 'laporan.view', 'statistics.view',
                'surat.view', 'bantuan_sosial.view', 'pengaduan.view'
            ];
            $userRole->givePermissionTo($basicPermissions);
            $this->info('✓ Assigned basic permissions to user role');
        }

        $this->info('All permissions created successfully!');
    }
}
