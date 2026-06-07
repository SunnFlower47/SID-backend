<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PajakPbbTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithColumnFormatting
{
    public function array(): array
    {
        return [
            ['320500000000000000', 'BAPAK DUMMY W'],
            ['320500000000000001', 'IBU DUMMY W'],
            ['(Hapus 3 baris ini sebelum upload)', '(Hapus baris ini sebelum upload)'],
        ];
    }

    public function headings(): array
    {
        return [
            'NOP (Wajib 18 Digit Angka)',
            'Nama Wajib Pajak',
        ];
    }

    public function columnFormats(): array
    {
        return [
            // Ensure NOP is formatted as text so it doesn't become scientific notation (3.2E+17)
            'A' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35,
            'B' => 45,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Styling header
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF16A34A'], // Tailwind Green-600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF14532D'], // Dark Green
                ],
            ],
        ]);

        // Styling the dummy data cells
        $sheet->getStyle('A2:B4')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFD1D5DB'], // Gray-300
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF9FAFB'], // Gray-50
            ],
            'font' => [
                'italic' => true,
                'color' => ['argb' => 'FF6B7280'], // Gray-500
            ],
        ]);
        
        // Text align center and red color for the warning text row
        $sheet->getStyle('A4:B4')->applyFromArray([
            'font' => [
                'color' => ['argb' => 'FFDC2626'], // Red-600
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ]
        ]);

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(20);
        $sheet->getRowDimension(4)->setRowHeight(20);

        return [];
    }
}
