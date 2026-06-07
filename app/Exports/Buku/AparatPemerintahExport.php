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

class AparatPemerintahExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithTitle, WithCustomStartCell
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
        return "Buku Aparat Pemerintah";
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
                'NAMA LENGKAP',
                'NIK / NO. HP',
                'JABATAN',
                'KATEGORI',
                'TGL PENGANGKATAN',
                'TGL BERAKHIR',
                'STATUS',
                'ALAMAT'
            ],
            [
                '1', '2', '3', '4', '5', '6', '7', '8', '9'
            ]
        ];
    }

    public function map($row): array
    {
        $nikHp = ($row->nik ?? '-');
        if (!empty($row->no_hp)) {
            $nikHp .= "\n" . $row->no_hp;
        }

        return [
            $this->rowNumber++,
            $row->nama ?? '-',
            $nikHp,
            strtoupper($row->jabatan ?? '-'),
            $row->kategori_label ?? $row->kategori ?? '-',
            $row->tanggal_pengangkatan ? \Carbon\Carbon::parse($row->tanggal_pengangkatan)->translatedFormat('d F Y') : '-',
            $row->tanggal_berakhir ? \Carbon\Carbon::parse($row->tanggal_berakhir)->translatedFormat('d F Y') : '-',
            $row->status_aktif ? 'AKTIF' : 'NON-AKTIF',
            $row->alamat ?? '-'
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
                $sheet->mergeCells('A1:I1');
                $sheet->setCellValue('A1', 'BUKU APARAT PEMERINTAH DESA');
                $sheet->mergeCells('A2:I2');
                $sheet->setCellValue('A2', '(Lampiran VIII — Permendagri No. 47 Tahun 2016)');
                
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A2')->getFont()->setSize(11);
                $sheet->getStyle('A1:I2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $highestRow = $sheet->getHighestRow();
                $highestColumn = 'I';
                
                $sheet->freezePane('A6');

                // Lebar kolom
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(15);
                $sheet->getColumnDimension('F')->setWidth(20);
                $sheet->getColumnDimension('G')->setWidth(20);
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(35);

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
                    $sheet->getStyle('A4:I5')->getFont()->setBold(true);
                    $sheet->getStyle('A4:I5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A4:I5')
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFF0F0F0');
                        
                    // Align center untuk beberapa kolom
                    if ($highestRow >= 6) {
                        $sheet->getStyle('A6:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('C6:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
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
                $sheet->getStyle('G' . $ttdRow . ':I' . $ttdRowEnd)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . ($ttdRow + 2))->getFont()->setBold(true);
                $sheet->getStyle('G' . ($ttdRow + 1))->getFont()->setBold(true);
                $sheet->mergeCells('G' . $ttdRow . ':I' . $ttdRow);
                $sheet->mergeCells('G' . ($ttdRow + 1) . ':I' . ($ttdRow + 1));
                $sheet->mergeCells('G' . $ttdRowEnd . ':I' . $ttdRowEnd);
            },
        ];
    }
}
