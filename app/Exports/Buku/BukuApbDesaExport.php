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

class BukuApbDesaExport implements FromView, ShouldAutoSize, WithStyles, WithTitle, WithEvents
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
        return 'Buku APB Desa';
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

                $sheet->mergeCells('A1:E1');
                $sheet->setCellValue('A1', 'BUKU ANGGARAN PENDAPATAN DAN BELANJA DESA (APB DESA)');
                $sheet->mergeCells('A2:E2');
                $sheet->setCellValue('A2', 'TAHUN ' . $tahun);
                $sheet->mergeCells('A3:E3');
                $sheet->setCellValue('A3', "DESA {$namaDesa}, KECAMATAN {$kecamatan}, KABUPATEN {$kabupaten}");

                $sheet->getStyle('A1:E3')->getFont()->setBold(true);
                $sheet->getStyle('A1:E3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                
                $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(5);
                $sheet->getColumnDimension('B')->setAutoSize(false)->setWidth(20);
                $sheet->getColumnDimension('C')->setAutoSize(false)->setWidth(45);
                $sheet->getColumnDimension('D')->setAutoSize(false)->setWidth(25);
                $sheet->getColumnDimension('E')->setAutoSize(false)->setWidth(25);

                if ($highestRow >= 8) {
                    $range = 'A7:' . $highestColumn . $highestRow;
                    $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle($range)->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
                }

                $sheet->getStyle('A7:E8')->getFont()->setBold(true);
                $sheet->getStyle('A7:E8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A7:E8')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF3F4F6');

                $ttdRow = $highestRow + 3;
                $sheet->setCellValue('D' . $ttdRow, $namaDesaCamel . ', ' . \Carbon\Carbon::now()->translatedFormat('d F Y'));
                
                $sheet->setCellValue('B' . ($ttdRow + 1), 'MENGETAHUI,');
                $sheet->setCellValue('B' . ($ttdRow + 2), 'SEKRETARIS DESA');
                $sheet->setCellValue('D' . ($ttdRow + 1), "KEPALA DESA {$namaDesa}");
                
                $kades = \App\Models\StrukturDesa::where('kategori', 'kepala_desa')->where('status_aktif', true)->first();
                $sekdes = \App\Models\StrukturDesa::where('kategori', 'sekretaris')->where('status_aktif', true)->first();
                $namaKades = $kades ? $kades->nama : '..........................................';
                $namaSekdes = $sekdes ? $sekdes->nama : '..........................................';

                $ttdRowEnd = $ttdRow + 6;
                $sheet->setCellValue('B' . $ttdRowEnd, $namaSekdes);
                $sheet->setCellValue('D' . $ttdRowEnd, $namaKades);
                
                $sheet->getStyle('B' . $ttdRowEnd)->getFont()->setBold(true)->setUnderline(true);
                $sheet->getStyle('D' . $ttdRowEnd)->getFont()->setBold(true)->setUnderline(true);

                $sheet->getStyle('B' . $ttdRow . ':B' . $ttdRowEnd)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D' . $ttdRow . ':E' . $ttdRowEnd)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->mergeCells('D' . $ttdRow . ':E' . $ttdRow);
                $sheet->mergeCells('D' . ($ttdRow + 1) . ':E' . ($ttdRow + 1));
                $sheet->mergeCells('D' . $ttdRowEnd . ':E' . $ttdRowEnd);
            },
        ];
    }
}
