<?php

namespace App\Exports\Buku;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BukuKasPembantuPajakExport implements FromView, ShouldAutoSize, WithStyles, WithTitle, WithEvents
{
    protected $viewName;
    protected $viewData;

    public function __construct(string $viewName, array $viewData)
    {
        $this->viewName = $viewName;
        $this->viewData = $viewData;
    }

    public function view(): View
    {
        return view($this->viewName, $this->viewData);
    }

    public function title(): string
    {
        return 'Kas Pembantu Pajak';
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                $namaDesa = strtoupper(\App\Models\DesaSetting::getValue('nama_desa', 'CIBATU'));
                $kecamatan = strtoupper(\App\Models\DesaSetting::getValue('kecamatan', 'CIBATU'));
                $kabupaten = strtoupper(\App\Models\DesaSetting::getValue('kabupaten', 'PURWAKARTA'));
                $namaDesaCamel = \App\Models\DesaSetting::getValue('nama_desa', 'Cibatu');
                $tahun = $this->viewData['filters']['tahun'] ?? date('Y');

                $sheet->insertNewRowBefore(1, 6);

                $sheet->mergeCells('A1:L1');
                $sheet->setCellValue('A1', 'BUKU KAS PEMBANTU PAJAK');
                $sheet->mergeCells('A2:L2');
                $sheet->setCellValue('A2', 'TAHUN ' . $tahun);
                $sheet->mergeCells('A3:L3');
                $sheet->setCellValue('A3', "DESA {$namaDesa}, KECAMATAN {$kecamatan}, KABUPATEN {$kabupaten}");

                $sheet->getStyle('A1:L3')->getFont()->setBold(true);
                $sheet->getStyle('A1:L3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                
                $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(5);
                $sheet->getColumnDimension('B')->setAutoSize(false)->setWidth(15);
                $sheet->getColumnDimension('C')->setAutoSize(false)->setWidth(30);
                $sheet->getColumnDimension('D')->setAutoSize(false)->setWidth(15);
                $sheet->getColumnDimension('E')->setAutoSize(false)->setWidth(15);
                $sheet->getColumnDimension('F')->setAutoSize(false)->setWidth(15);
                $sheet->getColumnDimension('G')->setAutoSize(false)->setWidth(15);
                $sheet->getColumnDimension('H')->setAutoSize(false)->setWidth(15);
                $sheet->getColumnDimension('I')->setAutoSize(false)->setWidth(15);
                $sheet->getColumnDimension('J')->setAutoSize(false)->setWidth(15);
                $sheet->getColumnDimension('K')->setAutoSize(false)->setWidth(15);
                $sheet->getColumnDimension('L')->setAutoSize(false)->setWidth(20);

                if ($highestRow >= 9) {
                    $range = 'A7:' . $highestColumn . $highestRow;
                    $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle($range)->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
                }

                $sheet->getStyle('A7:L9')->getFont()->setBold(true);
                $sheet->getStyle('A7:L9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A7:L9')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A7:L9')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF3F4F6');

                $ttdRow = $highestRow + 3;
                $sheet->setCellValue('K' . $ttdRow, $namaDesaCamel . ', ' . \Carbon\Carbon::now()->translatedFormat('d F Y'));
                
                $sheet->setCellValue('C' . ($ttdRow + 1), 'MENGETAHUI,');
                $sheet->setCellValue('C' . ($ttdRow + 2), 'SEKRETARIS DESA');
                $sheet->setCellValue('K' . ($ttdRow + 1), "KEPALA DESA {$namaDesa}");
                
                $kades = \App\Models\StrukturDesa::where('kategori', 'kepala_desa')->where('status_aktif', true)->first();
                $sekdes = \App\Models\StrukturDesa::where('kategori', 'sekretaris')->where('status_aktif', true)->first();
                $namaKades = $kades ? $kades->nama : '..........................................';
                $namaSekdes = $sekdes ? $sekdes->nama : '..........................................';

                $ttdRowEnd = $ttdRow + 6;
                $sheet->setCellValue('C' . $ttdRowEnd, $namaSekdes);
                $sheet->setCellValue('K' . $ttdRowEnd, $namaKades);
                
                $sheet->getStyle('C' . $ttdRowEnd)->getFont()->setBold(true)->setUnderline(true);
                $sheet->getStyle('K' . $ttdRowEnd)->getFont()->setBold(true)->setUnderline(true);

                $sheet->getStyle('C' . $ttdRow . ':E' . $ttdRowEnd)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('J' . $ttdRow . ':L' . $ttdRowEnd)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->mergeCells('C' . $ttdRow . ':E' . $ttdRow);
                $sheet->mergeCells('C' . ($ttdRow + 1) . ':E' . ($ttdRow + 1));
                $sheet->mergeCells('C' . ($ttdRow + 2) . ':E' . ($ttdRow + 2));
                $sheet->mergeCells('C' . $ttdRowEnd . ':E' . $ttdRowEnd);
                
                $sheet->mergeCells('J' . $ttdRow . ':L' . $ttdRow);
                $sheet->mergeCells('J' . ($ttdRow + 1) . ':L' . ($ttdRow + 1));
                $sheet->mergeCells('J' . $ttdRowEnd . ':L' . $ttdRowEnd);
            },
        ];
    }
}
