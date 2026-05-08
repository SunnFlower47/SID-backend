<?php

namespace Database\Seeders;

use App\Models\Testimoni;
use Illuminate\Database\Seeder;

class TestimoniSeeder extends Seeder
{
    public function run(): void
    {
        $testimonis = [
            [
                'nama' => 'Budi Santoso',
                'testimoni' => 'Sangat memudahkan! Sekarang urus surat domisili nggak perlu bolak-balik balai desa lagi.',
                'rating' => 5,
                'kategori' => 'Warga RT 001',
                'status' => 'approved',
            ],
            [
                'nama' => 'Siti Aminah',
                'testimoni' => 'Asisten AI-nya pintar sekali, saya tanya prosedur bantuan sosial langsung dijawab lengkap.',
                'rating' => 5,
                'kategori' => 'Ibu Rumah Tangga',
                'status' => 'approved',
            ],
            [
                'nama' => 'Agus Hermawan',
                'testimoni' => 'Website desa paling keren yang pernah saya lihat. UI-nya premium dan cepat diakses.',
                'rating' => 4,
                'kategori' => 'Tokoh Pemuda',
                'status' => 'approved',
            ],
            [
                'nama' => 'H. Dudung',
                'testimoni' => 'Transparansi data statistik desa membantu kami memantau perkembangan pembangunan di RW kami.',
                'rating' => 5,
                'kategori' => 'Ketua RW 002',
                'status' => 'approved',
            ],
            [
                'nama' => 'Lani Cahyani',
                'testimoni' => 'Layanan pengaduan di respon sangat cepat. Terima kasih Pemerintah Desa Cibatu!',
                'rating' => 5,
                'kategori' => 'Warga Dusun II',
                'status' => 'approved',
            ],
        ];

        foreach ($testimonis as $t) {
            Testimoni::create($t);
        }
    }
}
