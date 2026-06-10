<?php

namespace App\Exports\Buku;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BukuKasPembantuKegiatanExport implements FromView, ShouldAutoSize, WithStyles, WithTitle, WithEvents
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
        return 'Kas Pembantu Kegiatan'; // Max 31 chars
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

                $sheet->mergeCells('A1:G1');
                $sheet->setCellValue('A1', 'BUKU KAS PEMBANTU KEGIATAN');
                $sheet->mergeCells('A2:G2');
                $sheet->setCellValue('A2', 'TAHUN ' . $tahun);
                $sheet->mergeCells('A3:G3');
                $sheet->setCellValue('A3', "DESA {$namaDesa}, KECAMATAN {$kecamatan}, KABUPATEN {$kabupaten}");

                $kegiatanText = 'Semua Kegiatan (Belum Dipilih)';
                if (!empty($this->viewData['filters']['apbdes_id'])) {
                    $kegiatan = \App\Models\Apbdes::find($this->viewData['filters']['apbdes_id']);
                    if ($kegiatan) {
                        $kegiatanText = $kegiatan->kode_rekening . ' - ' . $kegiatan->nama_rekening;
                    }
                }
                $sheet->mergeCells('A5:G5');
                $sheet->setCellValue('A5', 'Kegiatan: ' . $kegiatanText);

                $sheet->getStyle('A1:G3')->getFont()->setBold(true);
                $sheet->getStyle('A1:G3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A5')->getFont()->setBold(true);

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                
                $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(5);
                $sheet->getColumnDimension('B')->setAutoSize(false)->setWidth(15);
                $sheet->getColumnDimension('C')->setAutoSize(false)->setWidth(40);
                $sheet->getColumnDimension('D')->setAutoSize(false)->setWidth(20);
                $sheet->getColumnDimension('E')->setAutoSize(false)->setWidth(20);
                $sheet->getColumnDimension('F')->setAutoSize(false)->setWidth(15);
                $sheet->getColumnDimension('G')->setAutoSize(false)->setWidth(20);

                if ($highestRow >= 8) {
                    $range = 'A7:' . $highestColumn . $highestRow;
                    $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle($range)->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
                }

                $sheet->getStyle('A7:G8')->getFont()->setBold(true);
                $sheet->getStyle('A7:G8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A7:G8')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF3F4F6');

                $ttdRow = $highestRow + 3;
                $sheet->setCellValue('F' . $ttdRow, $namaDesaCamel . ', ' . \Carbon\Carbon::now()->translatedFormat('d F Y'));
                
                $sheet->setCellValue('B' . ($ttdRow + 1), 'MENGETAHUI,');
                $sheet->setCellValue('B' . ($ttdRow + 2), 'SEKRETARIS DESA');
                $sheet->setCellValue('F' . ($ttdRow + 1), "KEPALA DESA {$namaDesa}");
                
                $kades = \App\Models\StrukturDesa::where('kategori', 'kepala_desa')->where('status_aktif', true)->first();
                $sekdes = \App\Models\StrukturDesa::where('kategori', 'sekretaris')->where('status_aktif', true)->first();
                $namaKades = $kades ? $kades->nama : '..........................................';
                $namaSekdes = $sekdes ? $sekdes->nama : '..........................................';

                $ttdRowEnd = $ttdRow + 6;
                $sheet->setCellValue('B' . $ttdRowEnd, $namaSekdes);
                $sheet->setCellValue('F' . $ttdRowEnd, $namaKades);
                
                $sheet->getStyle('B' . $ttdRowEnd)->getFont()->setBold(true)->setUnderline(true);
                $sheet->getStyle('F' . $ttdRowEnd)->getFont()->setBold(true)->setUnderline(true);

                $sheet->getStyle('B' . $ttdRow . ':C' . $ttdRowEnd)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F' . $ttdRow . ':G' . $ttdRowEnd)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->mergeCells('B' . $ttdRow . ':C' . $ttdRow);
                $sheet->mergeCells('B' . ($ttdRow + 1) . ':C' . ($ttdRow + 1));
                $sheet->mergeCells('B' . ($ttdRow + 2) . ':C' . ($ttdRow + 2));
                $sheet->mergeCells('B' . $ttdRowEnd . ':C' . $ttdRowEnd);
                
                $sheet->mergeCells('F' . $ttdRow . ':G' . $ttdRow);
                $sheet->mergeCells('F' . ($ttdRow + 1) . ':G' . ($ttdRow + 1));
                $sheet->mergeCells('F' . $ttdRowEnd . ':G' . $ttdRowEnd);
            },
        ];
    }
}
