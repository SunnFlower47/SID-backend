<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuratType;
use Spatie\Activitylog\Facades\LogBatch;

class SuratTypeSeeder extends Seeder
{
    public function run()
    {
        // Disable activity logging for this seeder to avoid ID type issues
        config(['activitylog.enabled' => false]);
        $types = [
            [
                'id' => 'sku',
                'kode' => 'SKU',
                'nama' => 'Surat Keterangan Usaha (SKU)',
                'deskripsi' => 'Surat keterangan untuk usaha yang dijalankan',
                'template_code' => 'sku',
                'icon' => 'fas fa-building',
                'color' => 'green',
                'has_template' => true,
                'is_active' => true,
                'form_json' => [
                    ['name' => 'nama_usaha', 'label' => 'Nama Usaha', 'type' => 'text'],
                    ['name' => 'alamat_usaha', 'label' => 'Alamat Usaha', 'type' => 'textarea'],
                    ['name' => 'jenis_usaha', 'label' => 'Jenis Usaha', 'type' => 'text'],
                ]
            ],
            [
                'id' => 'keterangan-domisili',
                'kode' => 'SKD',
                'nama' => 'Surat Keterangan Domisili',
                'deskripsi' => 'Surat keterangan tempat tinggal',
                'template_code' => 'keterangan-domisili',
                'icon' => 'fas fa-home',
                'color' => 'blue',
                'has_template' => true,
                'is_active' => true,
                'form_json' => []
            ],
            [
                'id' => 'pengantar',
                'kode' => 'SP',
                'nama' => 'Surat Pengantar',
                'deskripsi' => 'Surat pengantar untuk berbagai keperluan',
                'template_code' => 'pengantar',
                'icon' => 'fas fa-file-alt',
                'color' => 'green',
                'has_template' => true,
                'is_active' => true,
                'form_json' => []
            ],
            [
                'id' => 'pindah',
                'kode' => 'SKP',
                'nama' => 'Surat Keterangan Pindah',
                'deskripsi' => 'Surat keterangan pindah domisili',
                'template_code' => 'pindah',
                'icon' => 'fas fa-walking',
                'color' => 'red',
                'has_template' => true,
                'is_active' => true,
                'form_json' => [
                    ['name' => 'alamat_tujuan', 'label' => 'Alamat Tujuan', 'type' => 'textarea'],
                    ['name' => 'rt_rw_tujuan', 'label' => 'RT/RW Tujuan', 'type' => 'text'],
                    ['name' => 'kelurahan_tujuan', 'label' => 'Kelurahan Tujuan', 'type' => 'text'],
                    ['name' => 'kecamatan_tujuan', 'label' => 'Kecamatan Tujuan', 'type' => 'text'],
                    ['name' => 'kabupaten_tujuan', 'label' => 'Kabupaten Tujuan', 'type' => 'text'],
                ]
            ],
            [
                'id' => 'kematian',
                'kode' => 'SKK',
                'nama' => 'Surat Keterangan Kematian',
                'deskripsi' => 'Surat keterangan kematian',
                'template_code' => 'kematian',
                'icon' => 'fas fa-skull',
                'color' => 'gray',
                'has_template' => true,
                'is_active' => true,
                'form_json' => [
                    ['name' => 'tanggal_meninggal', 'label' => 'Tanggal Meninggal', 'type' => 'date'],
                    ['name' => 'penyebab_kematian', 'label' => 'Penyebab Kematian', 'type' => 'text'],
                    ['name' => 'tempat_meninggal', 'label' => 'Tempat Meninggal', 'type' => 'text'],
                ]
            ],
            [
                'id' => 'kelahiran',
                'kode' => 'SKKL',
                'nama' => 'Surat Keterangan Kelahiran',
                'deskripsi' => 'Surat keterangan kelahiran',
                'template_code' => 'kelahiran',
                'icon' => 'fas fa-baby',
                'color' => 'purple',
                'has_template' => true,
                'is_active' => true,
                'form_json' => [
                    ['name' => 'nama_bayi', 'label' => 'Nama Bayi', 'type' => 'text'],
                    ['name' => 'tempat_lahir', 'label' => 'Tempat Lahir', 'type' => 'text'],
                    ['name' => 'tanggal_lahir', 'label' => 'Tanggal Lahir', 'type' => 'date'],
                    ['name' => 'jenis_kelamin_bayi', 'label' => 'Jenis Kelamin Bayi', 'type' => 'select', 'options' => ['LAKI-LAKI', 'PEREMPUAN']],
                    ['name' => 'nama_ayah', 'label' => 'Nama Ayah', 'type' => 'text'],
                    ['name' => 'nama_ibu', 'label' => 'Nama Ibu', 'type' => 'text'],
                ]
            ],
            [
                'id' => 'sktm_dewasa',
                'kode' => 'SKTM',
                'nama' => 'Surat Keterangan Tidak Mampu (SKTM) - Dewasa',
                'deskripsi' => 'Surat keterangan tidak mampu untuk dewasa',
                'template_code' => 'tidak-mampu-dewasa',
                'icon' => 'fas fa-hand-holding-heart',
                'color' => 'indigo',
                'has_template' => true,
                'is_active' => true,
                'form_json' => [
                    ['name' => 'pekerjaan', 'label' => 'Pekerjaan', 'type' => 'text'],
                    ['name' => 'penghasilan', 'label' => 'Penghasilan', 'type' => 'number'],
                    ['name' => 'jumlah_tanggungan', 'label' => 'Jumlah Tanggungan', 'type' => 'number'],
                    ['name' => 'alasan_tidak_mampu', 'label' => 'Alasan Tidak Mampu', 'type' => 'textarea'],
                ]
            ],
            [
                'id' => 'sktm_anak',
                'kode' => 'SKTM',
                'nama' => 'Surat Keterangan Tidak Mampu (SKTM) - Anak',
                'deskripsi' => 'Surat keterangan tidak mampu untuk anak',
                'template_code' => 'tidak-mampu-anak',
                'icon' => 'fas fa-hand-holding-heart',
                'color' => 'indigo',
                'has_template' => true,
                'is_active' => true,
                'form_json' => [
                    ['name' => 'nama_anak', 'label' => 'Nama Anak', 'type' => 'text'],
                    ['name' => 'nama_ortu', 'label' => 'Nama Orang Tua', 'type' => 'text'],
                    ['name' => 'pekerjaan_ortu', 'label' => 'Pekerjaan Orang Tua', 'type' => 'text'],
                    ['name' => 'penghasilan_ortu', 'label' => 'Penghasilan Orang Tua', 'type' => 'number'],
                    ['name' => 'jumlah_tanggungan', 'label' => 'Jumlah Tanggungan', 'type' => 'number'],
                ]
            ]
        ];

        foreach ($types as $type) {
            SuratType::updateOrCreate(['id' => $type['id']], $type);
        }
    }
}
