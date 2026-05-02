<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DesaSetting;

class DesaSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General Info
            ['key' => 'nama_desa', 'value' => 'Cibatu', 'type' => 'text', 'group' => 'general', 'description' => 'Nama Desa'],
            ['key' => 'kode_desa', 'value' => '2001', 'type' => 'text', 'group' => 'general', 'description' => 'Kode Desa'],
            ['key' => 'kecamatan', 'value' => 'Cibatu', 'type' => 'text', 'group' => 'general', 'description' => 'Kecamatan'],
            ['key' => 'kabupaten', 'value' => 'Purwakarta', 'type' => 'text', 'group' => 'general', 'description' => 'Kabupaten'],
            ['key' => 'provinsi', 'value' => 'Jawa Barat', 'type' => 'text', 'group' => 'general', 'description' => 'Provinsi'],
            ['key' => 'kode_pos', 'value' => '41161', 'type' => 'text', 'group' => 'general', 'description' => 'Kode Pos'],
            ['key' => 'alamat_lengkap', 'value' => 'Jl. Cibatu No. 1, Desa Cibatu, Kec. Cibatu, Kab. Purwakarta, Jawa Barat 41161', 'type' => 'text', 'group' => 'general', 'description' => 'Alamat Kantor Desa'],
            ['key' => 'email', 'value' => 'desacibatu.2001@gmail.com', 'type' => 'text', 'group' => 'general', 'description' => 'Email Desa'],
            ['key' => 'telepon', 'value' => '(0264) 123456', 'type' => 'text', 'group' => 'general', 'description' => 'Telepon Desa'],
            ['key' => 'website', 'value' => 'https://desa-cibatu.id', 'type' => 'text', 'group' => 'general', 'description' => 'Website Desa'],
            
            // Logos
            ['key' => 'logo_desa', 'value' => '/logo desa cibatu.png', 'type' => 'image', 'group' => 'logo', 'description' => 'Logo Desa'],
            ['key' => 'logo_kabupaten', 'value' => null, 'type' => 'image', 'group' => 'logo', 'description' => 'Logo Kabupaten'],
        ];

        foreach ($settings as $setting) {
            DesaSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
        
        echo "Desa Settings Seeded Successfully!\n";
    }
}
