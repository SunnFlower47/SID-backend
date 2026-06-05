<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TanahDiDesa;
use App\Models\TanahDiDesaMutasi;

class TanahDiDesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tanah = TanahDiDesa::create([
            'nop' => '32.13.010.001.000-0000.0',
            'nama_pemilik' => 'Bapak Ujang Mulyana',
            'tempat_lahir_berdiri' => 'Garut',
            'tanggal_lahir_berdiri' => '1975-08-15',
            'status_kepemilikan' => 'Hak Milik (SHM)',
            'no_sertifikat' => '10.29.30.04.1.00345',
            'tanggal_penerbitan_sertifikat' => '2015-05-10',
            'tanggal_perolehan' => '2010-02-12',
            'no_buku_c' => 'C.450',
            'no_persil' => 'Persil 20 D.II',
            'no_kelas' => 'S.1',
            
            'luas_sawah' => 0,
            'luas_tegalan' => 500.5,
            'luas_kebun' => 200,
            'luas_perumahan' => 150,
            'luas_industri' => 0,
            'luas_fasilitas_umum' => 0,
            'luas_lain_lain' => 0,
            
            'lokasi_tanah' => 'Dusun Mekarsari RT 02 / RW 04',
            'batas_utara' => 'Jalan Desa',
            'batas_timur' => 'Tanah Pak Asep',
            'batas_selatan' => 'Saluran Irigasi',
            'batas_barat' => 'Tanah Pak Budi',
            
            'keterangan' => 'Tanah pekarangan dan kebun campuran.'
        ]);

        // Berikan 1 histori mutasi agar user bisa lihat UI mutasinya
        TanahDiDesaMutasi::create([
            'tanah_di_desa_id' => $tanah->id,
            'pemilik_lama' => 'Bapak Udin Hendarmin',
            'pemilik_baru' => 'Bapak Ujang Mulyana',
            'tanggal_mutasi' => '2010-02-12',
            'keterangan' => 'Dibeli melalui Akta Jual Beli No. 123/AJB/2010'
        ]);
    }
}
