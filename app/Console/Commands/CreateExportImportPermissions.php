<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateExportImportPermissions extends Command
{
    protected $signature = 'permissions:create-export-import';
    protected $description = 'Create export/import permissions';

    public function handle()
    {
        $permissions = [
            'export.view',
            'export.penduduk',
            'export.kartu-keluarga',
            'export.bantuan-sosial',
            'export.penerima-bantuan-sosial',
            'export.pengaduan',
            'export.umkm',
            'export.surat-pengajuan',
            'import.penduduk',
            'import.bantuan-sosial',
            'import.umkm',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
            $this->info("Created permission: {$permission}");
        }

        // Assign permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
            $this->info('Assigned export/import permissions to admin role');
        }

        $this->info('Export/Import permissions created successfully!');
    }
}