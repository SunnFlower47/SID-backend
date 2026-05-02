<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StrukturDesa;

class StrukturDesaSeeder extends Seeder
{
    public function run(): void
    {
        $pejabat = [
            [
                'nama' => 'H. MAMAN SUTARMAN, S.Pd.I',
                'nik' => '19651231 199003 1 001',
                'jabatan' => 'Kepala Desa Cibatu',
                'kategori' => 'kepala_desa',
                'status_aktif' => true,
            ],
            [
                'nama' => 'Drs. BUDIMAN, M.Si',
                'nik' => '19700315 199203 1 002',
                'jabatan' => 'Sekretaris Desa Cibatu',
                'kategori' => 'sekretaris',
                'status_aktif' => true,
            ],
        ];

        foreach ($pejabat as $p) {
            StrukturDesa::updateOrCreate(
                ['kategori' => $p['kategori']],
                $p
            );
        }
        
        echo "Struktur Desa Seeded Successfully!\n";
    }
}
