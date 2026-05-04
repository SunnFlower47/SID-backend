<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * This is the main entry point for resetting the system to a clean initial state.
     */
    public function run(): void
    {
        $this->call([
            // 1. Roles, Permissions, and Admin Users
            SimplifiedPermissionsSeeder::class,
            
            // 2. Global Village Information (Settings)
            DesaSettingsSeeder::class,
            
            // 4. Struktur Desa (Pejabat Desa)
            StrukturDesaSeeder::class,
            
            // 5. Master Jenis Surat (Layanan Surat)
            SuratTypeSeeder::class,
        ]);
        
        echo "\nDatabase Reset and Seeding Completed Successfully!\n";
    }
}
