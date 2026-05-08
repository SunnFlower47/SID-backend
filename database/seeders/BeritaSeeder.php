<?php

namespace Database\Seeders;

use App\Models\Berita;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BeritaSeeder extends Seeder
{
    public function run(): void
    {
        $beritas = [
            [
                'judul' => 'Penyaluran BLT Dana Desa Tahap Pertama Berjalan Lancar',
                'excerpt' => 'Pemerintah Desa Cibatu telah menyalurkan Bantuan Langsung Tunai (BLT) kepada warga yang berhak...',
                'konten' => 'Penyaluran BLT Dana Desa Tahap Pertama tahun 2024 telah dilaksanakan dengan sukses di Balai Desa Cibatu. Sebanyak 100 Keluarga Penerima Manfaat (KPM) menerima bantuan secara langsung dengan protokol yang transparan.',
                'status' => 'published',
                'kategori' => 'Kegiatan Desa',
            ],
            [
                'judul' => 'Cibatu Menuju Desa Digital: Peluncuran Asisten AI Warga',
                'excerpt' => 'Inovasi terbaru dari Desa Cibatu memperkenalkan sistem asisten digital berbasis kecerdasan buatan.',
                'konten' => 'Desa Cibatu resmi meluncurkan portal layanan mandiri yang didukung oleh AI. Portal ini memungkinkan warga mengajukan surat secara online dan berinteraksi dengan chatbot pintar untuk mendapatkan informasi publik.',
                'status' => 'published',
                'kategori' => 'Inovasi',
            ],
            [
                'judul' => 'Kegiatan Kerja Bakti Massal Membersihkan Lingkungan Dusun',
                'excerpt' => 'Seluruh warga Dusun I dan II bergotong-royong membersihkan saluran air menjelang musim penghujan.',
                'konten' => 'Kerja bakti massal dilakukan serentak di seluruh wilayah Desa Cibatu. Hal ini bertujuan untuk mencegah banjir dan menjaga kebersihan lingkungan agar tetap sehat dan asri.',
                'status' => 'published',
                'kategori' => 'Lingkungan',
            ],
        ];

        foreach ($beritas as $b) {
            Berita::updateOrCreate(['judul' => $b['judul']], array_merge($b, [
                'slug' => Str::slug($b['judul']),
                'author_id' => 1, // Assume admin user exists
                'published_at' => now(),
            ]));
        }
    }
}
