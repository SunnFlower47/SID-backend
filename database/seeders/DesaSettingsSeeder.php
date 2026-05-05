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
            ['key' => 'logo_provinsi', 'value' => null, 'type' => 'image', 'group' => 'logo', 'description' => 'Logo Provinsi'],

            // Geography
            ['key' => 'latitude', 'value' => '-6.5001403', 'type' => 'text', 'group' => 'general', 'description' => 'Latitude Lokasi Desa'],
            ['key' => 'longitude', 'value' => '107.5342964', 'type' => 'text', 'group' => 'general', 'description' => 'Longitude Lokasi Desa'],
            ['key' => 'luas_total', 'value' => '1250', 'type' => 'text', 'group' => 'geography', 'description' => 'Luas Total Wilayah (Ha)'],

            // Strategic Profile
            ['key' => 'visi', 'value' => 'Terwujudnya Desa Cibatu yang Mandiri, Sejahtera, dan Berakhlak Mulia.', 'type' => 'text', 'group' => 'profile', 'description' => 'Visi Desa'],
            ['key' => 'misi', 'value' => "1. Meningkatkan kualitas pelayanan publik.\n2. Mengembangkan potensi ekonomi lokal.\n3. Memperkuat kerukunan antar warga.", 'type' => 'text', 'group' => 'profile', 'description' => 'Misi Desa'],
            ['key' => 'sejarah_desa', 'value' => 'Desa Cibatu memiliki sejarah panjang yang dimulai dari...', 'type' => 'text', 'group' => 'profile', 'description' => 'Sejarah Singkat Desa'],

            // Social Media
            ['key' => 'link_facebook', 'value' => 'https://facebook.com/desacibatu', 'type' => 'text', 'group' => 'social', 'description' => 'URL Facebook Desa'],
            ['key' => 'link_instagram', 'value' => 'https://instagram.com/desacibatu', 'type' => 'text', 'group' => 'social', 'description' => 'URL Instagram Desa'],
            ['key' => 'link_youtube', 'value' => 'https://youtube.com/@desacibatu', 'type' => 'text', 'group' => 'social', 'description' => 'URL YouTube Desa'],
        ];

        foreach ($settings as $setting) {
            DesaSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
        
        echo "Desa Settings Seeded Successfully!\n";
    }
}
