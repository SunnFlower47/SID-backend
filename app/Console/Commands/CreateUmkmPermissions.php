<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateUmkmPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:create-umkm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create UMKM permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $permissions = [
            'umkm.view',
            'umkm.create',
            'umkm.edit',
            'umkm.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
            $this->info("Permission '{$permission}' created");
        }

        // Assign permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
            $this->info('Permissions assigned to admin role');
        }

        $this->info('UMKM permissions created successfully!');
    }
}
