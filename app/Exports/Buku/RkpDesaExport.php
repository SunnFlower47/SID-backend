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

class RkpDesaExport implements FromView, ShouldAutoSize, WithStyles, WithTitle, WithEvents
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
        return 'RKP Desa';
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

                $sheet->mergeCells('A1:I1');
                $sheet->setCellValue('A1', 'BUKU RENCANA KERJA PEMBANGUNAN (RKP) DESA');
                $sheet->mergeCells('A2:I2');
                $sheet->setCellValue('A2', 'TAHUN ' . $tahun);
                $sheet->mergeCells('A3:I3');
                $sheet->setCellValue('A3', "DESA {$namaDesa}, KECAMATAN {$kecamatan}, KABUPATEN {$kabupaten}");

                $sheet->getStyle('A1:I3')->getFont()->setBold(true);
                $sheet->getStyle('A1:I3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                
                $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(5);
                $sheet->getColumnDimension('B')->setAutoSize(false)->setWidth(30);
                $sheet->getColumnDimension('C')->setAutoSize(false)->setWidth(25);
                $sheet->getColumnDimension('D')->setAutoSize(false)->setWidth(20);
                $sheet->getColumnDimension('E')->setAutoSize(false)->setWidth(20);
                $sheet->getColumnDimension('F')->setAutoSize(false)->setWidth(15);
                $sheet->getColumnDimension('G')->setAutoSize(false)->setWidth(20);
                $sheet->getColumnDimension('H')->setAutoSize(false)->setWidth(20);
                $sheet->getColumnDimension('I')->setAutoSize(false)->setWidth(25);

                if ($highestRow >= 8) {
                    $range = 'A7:' . $highestColumn . $highestRow;
                    $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle($range)->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
                }

                $sheet->getStyle('A7:I8')->getFont()->setBold(true);
                $sheet->getStyle('A7:I8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A7:I8')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF3F4F6');

                $ttdRow = $highestRow + 3;
                $sheet->setCellValue('H' . $ttdRow, $namaDesaCamel . ', ' . \Carbon\Carbon::now()->translatedFormat('d F Y'));
                
                $sheet->setCellValue('B' . ($ttdRow + 1), 'MENGETAHUI,');
                $sheet->setCellValue('B' . ($ttdRow + 2), 'SEKRETARIS DESA');
                $sheet->setCellValue('H' . ($ttdRow + 1), "KEPALA DESA {$namaDesa}");
                
                $kades = \App\Models\StrukturDesa::where('kategori', 'kepala_desa')->where('status_aktif', true)->first();
                $sekdes = \App\Models\StrukturDesa::where('kategori', 'sekretaris')->where('status_aktif', true)->first();
                $namaKades = $kades ? $kades->nama : '..........................................';
                $namaSekdes = $sekdes ? $sekdes->nama : '..........................................';

                $ttdRowEnd = $ttdRow + 6;
                $sheet->setCellValue('B' . $ttdRowEnd, $namaSekdes);
                $sheet->setCellValue('H' . $ttdRowEnd, $namaKades);
                
                $sheet->getStyle('B' . $ttdRowEnd)->getFont()->setBold(true)->setUnderline(true);
                $sheet->getStyle('H' . $ttdRowEnd)->getFont()->setBold(true)->setUnderline(true);

                $sheet->getStyle('B' . $ttdRow . ':C' . $ttdRowEnd)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H' . $ttdRow . ':I' . $ttdRowEnd)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->mergeCells('B' . $ttdRow . ':C' . $ttdRow);
                $sheet->mergeCells('B' . ($ttdRow + 1) . ':C' . ($ttdRow + 1));
                $sheet->mergeCells('B' . ($ttdRow + 2) . ':C' . ($ttdRow + 2));
                $sheet->mergeCells('B' . $ttdRowEnd . ':C' . $ttdRowEnd);
                
                $sheet->mergeCells('H' . $ttdRow . ':I' . $ttdRow);
                $sheet->mergeCells('H' . ($ttdRow + 1) . ':I' . ($ttdRow + 1));
                $sheet->mergeCells('H' . $ttdRowEnd . ':I' . $ttdRowEnd);
            },
        ];
    }
}
