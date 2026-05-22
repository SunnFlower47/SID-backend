<?php

namespace Database\Seeders;

use App\Models\AsetKategori;
use Illuminate\Database\Seeder;

class AsetKategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            ['kode' => '1', 'nama' => 'Kas dan Setara Kas',                        'urutan' => 1],
            ['kode' => '2', 'nama' => 'Tanah',                                     'urutan' => 2],
            ['kode' => '3', 'nama' => 'Peralatan dan Mesin',                       'urutan' => 3],
            ['kode' => '4', 'nama' => 'Gedung dan Bangunan',                       'urutan' => 4],
            ['kode' => '5', 'nama' => 'Jalan, Jaringan dan Irigasi',               'urutan' => 5],
            ['kode' => '6', 'nama' => 'Aset Tetap Lainnya',                        'urutan' => 6],
            ['kode' => '7', 'nama' => 'Konstruksi Dalam Pengerjaan',               'urutan' => 7],
        ];

        foreach ($kategoris as $kategori) {
            AsetKategori::updateOrCreate(
                ['kode' => $kategori['kode']],
                $kategori
            );
        }
    }
}
