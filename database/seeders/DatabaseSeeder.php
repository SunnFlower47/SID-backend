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
            RolesAndPermissionsSeeder::class,
            
            // 2. Global Village Information (Settings)
            DesaSettingsSeeder::class,
            
            // 4. Struktur Desa (Pejabat Desa)
            StrukturDesaSeeder::class,
            
            // 5. Master Jenis Surat (Layanan Surat)
            SuratTypeSeeder::class,

            // 6. Master Wilayah (RT/RW/Dusun dasar)
            WilayahSeeder::class,
        ]);

        // 7. Simulation & Demo Data (HANYA UNTUK NON-PRODUCTION)
        if (!app()->environment('production')) {
            $this->call([
                BeritaSeeder::class,
                TestimoniSeeder::class,
                PendudukSeeder::class,
            ]);
            $this->command->info('Dummy data seeded for local development.');
        } else {
            $this->command->warn('Production environment detected. Dummy data skipped.');
        }
        
        
        echo "\nDatabase Reset and Seeding Completed Successfully!\n";
    }
}
