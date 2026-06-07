<?php

namespace App\Exports\Buku;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TanahDiDesaExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithTitle, WithCustomStartCell
{
    protected $data;
    protected $rowNumber = 1;

    public function __construct(string $viewName, array $viewData)
    {
        $this->data = $viewData['data'];
    }

    public function collection()
    {
        return $this->data;
    }

    public function title(): string
    {
        return "Buku Tanah di Desa";
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        return [
            [
                'NO',
                'NAMA PEMILIK / BADAN HUKUM',
                'NOP',
                'STATUS KEPEMILIKAN',
                'NO. SERTIFIKAT',
                'TOTAL LUAS (m²)',
                'LOKASI',
                'KETERANGAN'
            ],
            [
                '1', '2', '3', '4', '5', '6', '7', '8'
            ]
        ];
    }

    public function map($row): array
    {
        $totalLuas = ($row->luas_sawah ?? 0) + ($row->luas_tegalan ?? 0) + ($row->luas_kebun ?? 0) + ($row->luas_perumahan ?? 0) + ($row->luas_industri ?? 0) + ($row->luas_fasilitas_umum ?? 0) + ($row->luas_lain_lain ?? 0);

        return [
            $this->rowNumber++,
            $row->nama_pemilik ?? '-',
            $row->nop ?? '-',
            $row->status_kepemilikan ?? '-',
            $row->no_sertifikat ?? '-',
            $totalLuas > 0 ? $totalLuas : '-',
            $row->lokasi_tanah ?? '-',
            $row->keterangan ?? '-'
        ];
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

                
                // Kop Laporan
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'BUKU TANAH DI DESA');
                $sheet->mergeCells('A2:H2');
                $sheet->setCellValue('A2', '(Lampiran V — Permendagri No. 47 Tahun 2016)');
                
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A2')->getFont()->setSize(11);
                $sheet->getStyle('A1:H2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $highestRow = $sheet->getHighestRow();
                $highestColumn = 'H';
                
                $sheet->freezePane('A6');

                // Lebar kolom
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(25);
                $sheet->getColumnDimension('H')->setWidth(30);

                if ($highestRow >= 4) {
                    $range = 'A4:' . $highestColumn . $highestRow;
                    $sheet->getStyle($range)
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);
                    
                    $sheet->getStyle($range)
                        ->getAlignment()
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setWrapText(true);

                    // Header Tabel (Baris 4-5)
                    $sheet->getStyle('A4:H5')->getFont()->setBold(true);
                    $sheet->getStyle('A4:H5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A4:H5')
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFF0F0F0');
                        
                    // Align center untuk beberapa kolom
                    if ($highestRow >= 6) {
                        $sheet->getStyle('A6:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('C6:E' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('F6:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                }

                // Tanda Tangan
                $ttdRow = $highestRow + 3;
                $sheet->setCellValue('G' . $ttdRow, $namaDesaCamel . ', ' . \Carbon\Carbon::now()->translatedFormat('d F Y'));
                
                $sheet->setCellValue('B' . ($ttdRow + 1), 'Mengetahui,');
                $sheet->setCellValue('B' . ($ttdRow + 2), "KEPALA DESA {$namaDesa}");
                
                $sheet->setCellValue('G' . ($ttdRow + 1), 'SEKRETARIS DESA');
                
                $kades = \App\Models\StrukturDesa::where('kategori', 'kepala_desa')->where('status_aktif', true)->first();
                $sekdes = \App\Models\StrukturDesa::where('kategori', 'sekretaris')->where('status_aktif', true)->first();
                $namaKades = $kades ? $kades->nama : '..........................................';
                $namaSekdes = $sekdes ? $sekdes->nama : '..........................................';

                $ttdRowEnd = $ttdRow + 6;
                $sheet->setCellValue('B' . $ttdRowEnd, $namaKades);
                $sheet->setCellValue('G' . $ttdRowEnd, $namaSekdes);
                
                $sheet->getStyle('B' . $ttdRowEnd)->getFont()->setBold(true)->setUnderline(true);
                $sheet->getStyle('G' . $ttdRowEnd)->getFont()->setBold(true)->setUnderline(true);

                $sheet->getStyle('B' . $ttdRow . ':B' . $ttdRowEnd)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G' . $ttdRow . ':H' . $ttdRowEnd)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . ($ttdRow + 2))->getFont()->setBold(true);
                $sheet->getStyle('G' . ($ttdRow + 1))->getFont()->setBold(true);
                $sheet->mergeCells('G' . $ttdRow . ':H' . $ttdRow);
                $sheet->mergeCells('G' . ($ttdRow + 1) . ':H' . ($ttdRow + 1));
                $sheet->mergeCells('G' . $ttdRowEnd . ':H' . $ttdRowEnd);
            },
        ];
    }
}
