<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateSuratPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:create-surat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create permissions for surat and settings modules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating surat and settings permissions...');

        // Create surat permissions
        $suratPermissions = [
            'surat.view',
            'surat.create',
            'surat.edit',
            'surat.delete'
        ];

        foreach ($suratPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
            $this->line("Created permission: {$permission}");
        }

        // Create settings permissions
        $settingsPermissions = [
            'settings.view',
            'settings.edit',
            'settings.export',
            'settings.import'
        ];

        foreach ($settingsPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
            $this->line("Created permission: {$permission}");
        }

        // Assign permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $allPermissions = array_merge($suratPermissions, $settingsPermissions);
            $adminRole->givePermissionTo($allPermissions);
            $this->info('Assigned all permissions to admin role');
        } else {
            $this->warn('Admin role not found');
        }

        $this->info('Permissions created successfully!');
    }
}
