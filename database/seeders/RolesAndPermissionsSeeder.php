<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Penduduk permissions
            'penduduk.view',
            'penduduk.create',
            'penduduk.edit',
            'penduduk.delete',
            'penduduk.export',
            'penduduk.import',

            // Kartu Keluarga permissions
            'kartu_keluarga.view',
            'kartu_keluarga.create',
            'kartu_keluarga.edit',
            'kartu_keluarga.delete',
            'kartu_keluarga.export',

            // Mutasi permissions
            'mutasi.view',
            'mutasi.create',
            'mutasi.edit',
            'mutasi.delete',

            // Statistics permissions
            'statistics.view',

            // Laporan permissions
            'laporan.view',
            'laporan.penduduk',
            'laporan.mutasi',
            'laporan.pisah_kk',
            'laporan.export',

            // Audit Log permissions
            'audit_log.view',
            'audit_log.export',

            // Backup permissions
            'backup.view',
            'backup.manage',
            'backup.create',
            'backup.download',
            'backup.restore',
            'backup.delete',
            'backup.export',

            // Surat permissions
            'surat.view',
            'surat.create',
            'surat.edit',
            'surat.delete',

            // Kartu Keluarga permissions (alternative naming)
            'kartu-keluarga.view',
            'kartu-keluarga.create',
            'kartu-keluarga.edit',
            'kartu-keluarga.delete',

            // Settings permissions
            'settings.view',
            'settings.users.manage',
            'settings.users.create',
            'settings.users.edit',
            'settings.users.delete',
            'settings.roles.manage',
            'settings.roles.create',
            'settings.roles.edit',
            'settings.roles.delete',
            'settings.edit',
            'settings.export',
            'settings.import',

            // Wilayah permissions
            'wilayah.view',
            'wilayah.manage',
            'wilayah.import_conflict.manage',

            // Testimoni permissions
            'testimoni.view',
            'testimoni.create',
            'testimoni.update',
            'testimoni.delete',

            // Bantuan Sosial permissions
            'bantuan_sosial.view',
            'bantuan_sosial.create',
            'bantuan_sosial.edit',
            'bantuan_sosial.delete',
            'bantuan_sosial.manage_penerima',

            // Pengaduan permissions
            'pengaduan.view',
            'pengaduan.create',
            'pengaduan.edit',
            'pengaduan.delete',
            'pengaduan.manage',

            // UMKM permissions
            'umkm.view',
            'umkm.create',
            'umkm.edit',
            'umkm.delete',

            // Export permissions
            'export.view',
            'export.penduduk',
            'export.bantuan-sosial',
            'export.penerima-bantuan-sosial',
            'export.pengaduan',
            'export.umkm',
            'export.surat-pengajuan',

            // Import permissions
            'import.penduduk',
            'import.bantuan-sosial',
            'import.umkm',

            // Struktur Desa permissions
            'struktur-desa.view',
            'struktur-desa.create',
            'struktur-desa.edit',
            'struktur-desa.delete',

            // Kontak Desa permissions
            'kontak-desa.view',
            'kontak-desa.create',
            'kontak-desa.edit',
            'kontak-desa.delete',

            // Transparansi Desa permissions
            'transparansi-desa.view',
            'transparansi-desa.create',
            'transparansi-desa.edit',
            'transparansi-desa.delete',

            // Fasilitas Desa permissions
            'fasilitas-desa.view',
            'fasilitas-desa.create',
            'fasilitas-desa.edit',
            'fasilitas-desa.delete',

            // Berita permissions
            'berita.view',
            'berita.create',
            'berita.edit',
            'berita.delete',

            // Comparison permissions
            'comparison.view',

            // Pisah KK permissions
            'pisah-kk.view',
            'pisah-kk.create',
            'pisah-kk.process',

            // Contact Messages permissions
            'contact-messages.index',
            'contact-messages.show',
            'contact-messages.mark-read',
            'contact-messages.mark-replied',
            'contact-messages.archive',
            'contact-messages.destroy',
            'contact-messages.bulk-action',

            // Anggaran permissions
            'anggaran.edit',
            'anggaran.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $viewerRole = Role::firstOrCreate(['name' => 'Viewer']);

        // Assign permissions to roles
        $superAdminRole->givePermissionTo(Permission::all());

        $adminRole->syncPermissions([
            'penduduk.view',
            'penduduk.create',
            'penduduk.edit',
            'penduduk.export',
            'kartu_keluarga.view',
            'kartu_keluarga.create',
            'kartu_keluarga.edit',
            'kartu_keluarga.export',
            'mutasi.view',
            'mutasi.create',
            'mutasi.edit',
            'statistics.view',
            'laporan.view',
            'laporan.penduduk',
            'laporan.mutasi',
            'laporan.pisah_kk',
            'laporan.export',
            'audit_log.view',
            'backup.view',
            'backup.create',
            'backup.download',
            'backup.export',
            'surat.view',
            'surat.create',
            'surat.edit',
            'surat.delete',
            'settings.view',
            'wilayah.view',
            'wilayah.manage',
            'wilayah.import_conflict.manage',
            'testimoni.view',
            'testimoni.create',
            'testimoni.update',
            'testimoni.delete',
            'bantuan_sosial.view',
            'bantuan_sosial.create',
            'bantuan_sosial.edit',
            'bantuan_sosial.delete',
            'bantuan_sosial.manage_penerima',
            'pengaduan.view',
            'pengaduan.create',
            'pengaduan.edit',
            'pengaduan.delete',
            'pengaduan.manage',
            'umkm.view',
            'umkm.create',
            'umkm.edit',
            'umkm.delete',
            'export.view',
            'export.penduduk',
            'export.bantuan-sosial',
            'export.penerima-bantuan-sosial',
            'export.pengaduan',
            'export.umkm',
            'export.surat-pengajuan',
            'import.penduduk',
            'import.bantuan-sosial',
            'import.umkm',
            'struktur-desa.view',
            'struktur-desa.create',
            'struktur-desa.edit',
            'struktur-desa.delete',
            'kontak-desa.view',
            'kontak-desa.create',
            'kontak-desa.edit',
            'kontak-desa.delete',
            'transparansi-desa.view',
            'transparansi-desa.create',
            'transparansi-desa.edit',
            'transparansi-desa.delete',
            'fasilitas-desa.view',
            'fasilitas-desa.create',
            'fasilitas-desa.edit',
            'fasilitas-desa.delete',
            'berita.view',
            'berita.create',
            'berita.edit',
            'berita.delete',
            'comparison.view',
            'pisah-kk.view',
            'pisah-kk.create',
            'pisah-kk.process',
            'contact-messages.index',
            'contact-messages.show',
            'contact-messages.mark-read',
            'contact-messages.mark-replied',
            'contact-messages.archive',
            'contact-messages.destroy',
            'contact-messages.bulk-action',
            'anggaran.edit',
            'anggaran.delete',
        ]);

        $viewerRole->syncPermissions([
            'penduduk.view',
            'kartu_keluarga.view',
            'mutasi.view',
            'statistics.view',
            'laporan.view',
            'laporan.penduduk',
            'laporan.mutasi',
            'laporan.pisah_kk',
            'audit_log.view',
            'testimoni.view',
            'bantuan_sosial.view',
            'pengaduan.view',
            'umkm.view',
            'struktur-desa.view',
            'kontak-desa.view',
            'transparansi-desa.view',
            'fasilitas-desa.view',
            'berita.view',
            'comparison.view',
            'wilayah.view',
            'pisah-kk.view',
            'contact-messages.index',
            'contact-messages.show',
        ]);

        // Create default users
        $users = [
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@desacibatu.com',
                'password' => Hash::make('password'),
                'role' => 'Super Admin'
            ],
            [
                'name' => 'Administrator',
                'email' => 'admin@desacibatu.com',
                'password' => Hash::make('password'),
                'role' => 'Admin'
            ],
            [
                'name' => 'Sekretaris Desa',
                'email' => 'sekretaris@desacibatu.com',
                'password' => Hash::make('password'),
                'role' => 'Admin'
            ],
            [
                'name' => 'Kepala Desa',
                'email' => 'kepaladesa@desacibatu.com',
                'password' => Hash::make('password'),
                'role' => 'Viewer'
            ],
            [
                'name' => 'Viewer',
                'email' => 'viewer@desacibatu.com',
                'password' => Hash::make('password'),
                'role' => 'Viewer'
            ]
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password']
                ]
            );

            // Remove all existing roles first
            $user->syncRoles([]);

            // Assign the role
            $user->assignRole($userData['role']);

            echo "User {$user->name} assigned role: {$userData['role']}\n";
        }
    }
}
