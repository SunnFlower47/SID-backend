<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateBantuanSosialPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:create-bantuan-sosial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create permissions for Bantuan Sosial module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating Bantuan Sosial permissions...');

        // Create permissions
        $permissions = [
            'bantuan_sosial.view',
            'bantuan_sosial.create',
            'bantuan_sosial.edit',
            'bantuan_sosial.delete',
            'bantuan_sosial.manage_penerima'
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
            $userRole->givePermissionTo(['bantuan_sosial.view']);
            $this->line("✓ Assigned view permission to user role");
        }

        $this->info('Bantuan Sosial permissions created successfully!');
    }
}
