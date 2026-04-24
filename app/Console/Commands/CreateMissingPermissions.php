<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateMissingPermissions extends Command
{
    protected $signature = 'permission:create-missing';
    protected $description = 'Create missing permissions for sidebar menu';

    public function handle()
    {
        $permissions = [
            // Struktur Desa
            'struktur-desa.view',
            'struktur-desa.create',
            'struktur-desa.edit',
            'struktur-desa.delete',

            // Kontak Desa
            'kontak-desa.view',
            'kontak-desa.create',
            'kontak-desa.edit',
            'kontak-desa.delete',

            // Transparansi Desa
            'transparansi-desa.view',
            'transparansi-desa.create',
            'transparansi-desa.edit',
            'transparansi-desa.delete',

            // Fasilitas Desa
            'fasilitas-desa.view',
            'fasilitas-desa.create',
            'fasilitas-desa.edit',
            'fasilitas-desa.delete',

            // Berita
            'berita.view',
            'berita.create',
            'berita.edit',
            'berita.delete',

            // Comparison
            'comparison.view',

            // Pisah KK
            'pisah-kk.view',
            'pisah-kk.create',
            'pisah-kk.process',

            // UMKM
            'umkm.view',
            'umkm.create',
            'umkm.edit',
            'umkm.delete',
        ];

        $this->info('Creating missing permissions...');

        foreach ($permissions as $permission) {
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission, 'guard_name' => 'web']);
                $this->line("Created permission: {$permission}");
            } else {
                $this->line("Permission already exists: {$permission}");
            }
        }

        // Assign permissions to roles
        $adminRole = Role::where('name', 'admin')->first();
        $superAdminRole = Role::where('name', 'Super Admin')->first();

        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
            $this->info('Assigned permissions to admin role');
        }

        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
            $this->info('Assigned permissions to super admin role');
        }

        $this->info('Missing permissions created successfully!');
    }
}
