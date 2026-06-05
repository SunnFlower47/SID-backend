<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KaderPemberdayaan;

class KaderPemberdayaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kaders = [
            [
                'nama' => 'Siti Aminah',
                'umur' => 45,
                'jenis_kelamin' => 'P',
                'pendidikan_terakhir' => 'SMA',
                'bidang' => 'Posyandu',
                'alamat' => 'Kp. Cibatu RT 01 RW 02',
                'keterangan' => 'Kader aktif Posyandu Melati',
            ],
            [
                'nama' => 'Budi Santoso',
                'umur' => 38,
                'jenis_kelamin' => 'L',
                'pendidikan_terakhir' => 'S1 Pertanian',
                'bidang' => 'Pemberdayaan Ekonomi / Pertanian',
                'alamat' => 'Kp. Suka Maju RT 03 RW 01',
                'keterangan' => 'Pendamping Kelompok Tani',
            ],
            [
                'nama' => 'Rina Wati',
                'umur' => 30,
                'jenis_kelamin' => 'P',
                'pendidikan_terakhir' => 'D3 Kebidanan',
                'bidang' => 'Kesehatan Masyarakat',
                'alamat' => 'Kp. Cibatu RT 02 RW 02',
                'keterangan' => 'Kader Desa Siaga',
            ],
            [
                'nama' => 'Ahmad Fauzi',
                'umur' => 50,
                'jenis_kelamin' => 'L',
                'pendidikan_terakhir' => 'SMP',
                'bidang' => 'Pembangunan & Keamanan',
                'alamat' => 'Kp. Bojong RT 05 RW 03',
                'keterangan' => 'Linmas / Keamanan Lingkungan',
            ],
            [
                'nama' => 'Susi Susanti',
                'umur' => 42,
                'jenis_kelamin' => 'P',
                'pendidikan_terakhir' => 'SMA',
                'bidang' => 'PKK',
                'alamat' => 'Kp. Bojong RT 04 RW 03',
                'keterangan' => 'Ketua Pokja II PKK',
            ],
        ];

        foreach ($kaders as $kader) {
            KaderPemberdayaan::create($kader);
        }
    }
}
