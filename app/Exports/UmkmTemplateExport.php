<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UmkmTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return [
            'Nama Usaha',
            'Nama Pemilik',
            'NIK Pemilik',
            'Alamat Usaha',
            'RT',
            'RW',
            'Dusun',
            'Telepon',
            'Email',
            'Jenis Usaha',
            'Deskripsi Usaha',
            'Modal Awal',
            'Omset Bulanan',
            'Jumlah Karyawan',
            'Status Usaha',
            'Tanggal Berdiri',
            'Produk Unggulan',
            'Unggulan',
            'Terverifikasi'
        ];
    }

    public function array(): array
    {
        return [
            [
                'Warung Nasi Bu Susi',
                'Susi Susanti',
                '3205xxxxxxxxxxxx',
                'Jalan Raya Cibatu No. 10',
                '001',
                '002',
                'Cibatu Pusat',
                '081234567890',
                'susi@example.com',
                'makanan',
                'Menjual berbagai macam makanan rumahan',
                '5000000',
                '15000000',
                '2',
                'aktif',
                '20/05/2020',
                'Nasi Rames, Ayam Bakar',
                'Ya',
                'Ya'
            ],
            [
                'Kerajinan Bambu Pak Jajang',
                'Jajang Nurjaman',
                '3205xxxxxxxxxxxx',
                'Kampung Bambu RT 03',
                '003',
                '001',
                'Cibatu Utara',
                '',
                '',
                'kerajinan',
                'Membuat anyaman bambu untuk perabot rumah',
                '2000000',
                '5000000',
                '1',
                'aktif',
                '10/01/2018',
                'Tampah, Boboko',
                'Tidak',
                'Tidak'
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
