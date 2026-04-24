<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddKKPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:add-kk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Kartu Keluarga permissions to the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Adding Kartu Keluarga permissions...');

        // Create permissions
        $permissions = [
            'kartu-keluarga.view',
            'kartu-keluarga.create',
            'kartu-keluarga.edit',
            'kartu-keluarga.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
            $this->line("✓ Created permission: {$permission}");
        }

        // Assign permissions to roles
        $roles = [
            'Super Admin' => ['kartu-keluarga.view', 'kartu-keluarga.create', 'kartu-keluarga.edit', 'kartu-keluarga.delete'],
            'Admin' => ['kartu-keluarga.view', 'kartu-keluarga.create', 'kartu-keluarga.edit', 'kartu-keluarga.delete'],
            'Viewer' => ['kartu-keluarga.view'],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo($rolePermissions);
                $this->line("✓ Assigned permissions to role: {$roleName}");
            }
        }

        $this->info('Kartu Keluarga permissions added successfully!');

        return Command::SUCCESS;
    }
}
