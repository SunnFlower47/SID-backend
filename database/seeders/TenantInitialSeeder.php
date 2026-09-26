<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TenantInitialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Ini dipanggil otomatis saat tenant baru dibuat.
     */
    public function run(): void
    {
        $this->call([
            // 1. Roles, Permissions, and Admin Users
            RolesAndPermissionsSeeder::class,
            
            // 2. Global Village Information (Settings)
            DesaSettingsSeeder::class,
            
            // 3. Struktur Desa (Pejabat Desa)
            StrukturDesaSeeder::class,
            
            // 4. Master Jenis Surat (Layanan Surat)
            SuratTypeSeeder::class,

            // 5. Master Wilayah (RT/RW/Dusun dasar)
            WilayahSeeder::class,
        ]);
        
        $this->command->info("Tenant DB seeded with essential master data!");
    }
}
