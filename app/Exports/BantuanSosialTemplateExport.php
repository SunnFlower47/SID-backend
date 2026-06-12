<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BantuanSosialTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return [
            'Program',
            'Jenis Bantuan',
            'Deskripsi',
            'Periode',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Sumber Dana',
            'Nilai Bantuan',
            'Kriteria Penerima',
            'Status',
        ];
    }

    public function array(): array
    {
        return [
            [
                'BLT Dana Desa',
                'Uang Tunai',
                'Bantuan langsung tunai dampak inflasi',
                '2024',
                '01/01/2024',
                '31/12/2024',
                'Dana Desa',
                '300000',
                'Keluarga Miskin Ekstrem',
                'aktif',
            ],
            [
                'PKH',
                'Sembako',
                'Program Keluarga Harapan Tahap 1',
                '2024',
                '15/02/2024',
                '15/03/2024',
                'APBN',
                '0',
                'Ibu Hamil, Balita, Lansia',
                'aktif',
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
