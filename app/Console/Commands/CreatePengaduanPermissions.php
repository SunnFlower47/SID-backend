<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreatePengaduanPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:create-pengaduan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create permissions for Pengaduan module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating Pengaduan permissions...');

        // Create permissions
        $permissions = [
            'pengaduan.view',
            'pengaduan.create',
            'pengaduan.edit',
            'pengaduan.delete',
            'pengaduan.manage'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
            $this->line("✓ Created permission: {$permission}");
        }

        // Assign permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
            $this->line("✓ Assigned all permissions to admin role");
        }

        // Assign view permission to user role
        $userRole = Role::where('name', 'user')->first();
        if ($userRole) {
            $userRole->givePermissionTo(['pengaduan.view']);
            $this->line("✓ Assigned view permission to user role");
        }

        $this->info('Pengaduan permissions created successfully!');
    }
}
