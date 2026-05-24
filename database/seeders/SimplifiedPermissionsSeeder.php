<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

/**
 * @deprecated This seeder is deprecated in favor of RolesAndPermissionsSeeder, which handles granular permissions.
 */
class SimplifiedPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Clear existing permissions and roles safely
        // Use DB truncate to ensure a clean start for this simplification
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 3. Define the 5 MEGA-PERMISSIONS
        $permissions = [
            'kependudukan',        // Penduduk, KK, Mutasi, KK Bermasalah, Pisah KK
            'pelayanan_informasi', // Surat, Bansos, Pengaduan, Pesan, Berita, UMKM, Fasilitas, Struktur, Kontak, Testimoni
            'keuangan',            // APBDes, Pengeluaran, Proyek, Transparansi
            'laporan_statistik',   // Laporan PDF, Statistik, Perbandingan
            'admin_sistem',        // Users, Roles, Audit, Backup, Wilayah, Export/Import, Settings
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // 4. Create Standard Roles
        $superAdmin = Role::create(['name' => 'Super Admin']);
        $adminDesa  = Role::create(['name' => 'Admin Desa']);
        $stafDesa   = Role::create(['name' => 'Staf Desa']);
        $viewer     = Role::create(['name' => 'Viewer']);

        // 5. Assign Permissions
        $superAdmin->givePermissionTo(Permission::all());
        
        $adminDesa->givePermissionTo([
            'kependudukan',
            'pelayanan_informasi',
            'keuangan',
            'laporan_statistik',
            'admin_sistem'
        ]);

        $stafDesa->givePermissionTo([
            'pelayanan_informasi',
            'laporan_statistik'
        ]);

        $viewer->givePermissionTo([
            'laporan_statistik'
        ]);

        // 6. Ensure default users exist and have the right roles
        $this->assignUserRole('superadmin@desacibatu.com', 'Super Admin', 'Super Administrator');
        $this->assignUserRole('admin@desacibatu.com', 'Admin Desa', 'Administrator');
        $this->assignUserRole('sekretaris@desacibatu.com', 'Admin Desa', 'Sekretaris Desa');
        $this->assignUserRole('kepaladesa@desacibatu.com', 'Viewer', 'Kepala Desa');
        
        echo "Simplified Permissions System Initialized Successfully!\n";
    }

    private function assignUserRole($email, $roleName, $defaultName)
    {
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->syncRoles([$roleName]);
        } else {
            // Create if doesn't exist for fresh install
            $user = User::create([
                'name' => $defaultName,
                'email' => $email,
                'password' => Hash::make('password'),
            ]);
            $user->assignRole($roleName);
        }
    }
}
