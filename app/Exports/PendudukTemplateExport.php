<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PendudukTemplateExport implements FromArray, WithHeadings, WithStyles, WithEvents
{
    public function headings(): array
    {
        return [
            'NIK',
            'Nama',
            'No. KK',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Agama',
            'Status Perkawinan',
            'Kedudukan Keluarga',
            'Pendidikan',
            'Pekerjaan',
            'Nama Ayah',
            'Nama Ibu',
            'Alamat',
            'RT',
            'RW',
            'Dusun',
            'Golongan Darah',
            'Warganegara',
            'No. Akta Lahir',
            'Status Pendidikan',
            'Telepon',
            'Jenis Cacat',
            'Sakit Menahun',
            'Status Asuransi',
            'Keterangan'
        ];
    }

    public function array(): array
    {
        return [
            [
                '3214xxxxxxxxxxxx',
                'Nama Warga',
                '3214xxxxxxxxxxxx',
                'L',
                'Purwakarta',
                '2000-01-01',
                'Islam',
                'Belum Kawin',
                'Anak',
                'SMA/Sederajat',
                'Pelajar',
                'Nama Ayah',
                'Nama Ibu',
                'Jl. Contoh No.1',
                '001',
                '001',
                'Dusun Satu',
                'B',
                'WNI',
                '12345/LU/2000',
                'Tamat Sekolah',
                '081234567890',
                '-',
                '-',
                'BPJS Mandiri',
                ''
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2D5A27']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $highestRow = $event->sheet->getDelegate()->getHighestRow();
                $highestColumn = $event->sheet->getDelegate()->getHighestColumn();

                foreach (range('A', $highestColumn) as $column) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setAutoSize(true);
                }

                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(25);

                $event->sheet->getDelegate()->getStyle('A1:' . $highestColumn . $highestRow)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                $event->sheet->getDelegate()->getStyle('A1:' . $highestColumn . $highestRow)
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);

                $event->sheet->getDelegate()->freezePane('A2');
            },
        ];
    }
}
