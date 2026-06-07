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

class BukuAgendaExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithTitle, WithCustomStartCell
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
        return "Buku Agenda";
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        return [
            [
                'NO. URUT',
                'TANGGAL SURAT MASUK/KELUAR',
                'SURAT MASUK',
                '',
                'SURAT KELUAR',
                '',
                'KETERANGAN'
            ],
            [
                '',
                '',
                'NOMOR DAN TANGGAL SURAT',
                'PENGIRIM, DAN ISI SINGKAT',
                'NOMOR DAN TANGGAL SURAT',
                'DITUJUKAN KEPADA, DAN ISI SINGKAT',
                ''
            ],
            [
                '1', '2', '3', '4', '5', '6', '7'
            ]
        ];
    }

    public function map($row): array
    {
        $noTglSurat = "No: " . ($row->nomor_surat ?? '-') . "\nTgl: " . \Carbon\Carbon::parse($row->tanggal_surat)->format('d/m/Y');
        
        $cMasuk = '-';
        $dMasuk = '-';
        $eKeluar = '-';
        $fKeluar = '-';

        if ($row->jenis_surat === 'Masuk') {
            $cMasuk = $noTglSurat;
            $dMasuk = "Dari: " . $row->pengirim_penerima . "\nIsi: " . $row->isi_singkat;
        } else {
            $eKeluar = $noTglSurat;
            $fKeluar = "Tujuan: " . $row->pengirim_penerima . "\nIsi: " . $row->isi_singkat;
        }

        return [
            $this->rowNumber++,
            \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y'),
            $cMasuk,
            $dMasuk,
            $eKeluar,
            $fKeluar,
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
                $sheet->mergeCells('A1:G1');
                $sheet->setCellValue('A1', 'BUKU AGENDA');
                $sheet->mergeCells('A2:G2');
                $sheet->setCellValue('A2', '(Lampiran III — Permendagri No. 47 Tahun 2016)');
                
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A2')->getFont()->setSize(11);
                $sheet->getStyle('A1:G2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $highestRow = $sheet->getHighestRow();
                $highestColumn = 'G';
                
                $sheet->freezePane('A7');

                // Lebar kolom
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(35);
                $sheet->getColumnDimension('E')->setWidth(25);
                $sheet->getColumnDimension('F')->setWidth(35);
                $sheet->getColumnDimension('G')->setWidth(20);

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

                    // Header Tabel (Baris 4-6)
                    $sheet->getStyle('A4:G6')->getFont()->setBold(true);
                    $sheet->getStyle('A4:G6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A4:G6')
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFF0F0F0');

                    // Merge Headers
                    $sheet->mergeCells('A4:A5'); // NO
                    $sheet->mergeCells('B4:B5'); // TANGGAL SURAT MASUK/KELUAR
                    $sheet->mergeCells('C4:D4'); // SURAT MASUK
                    $sheet->mergeCells('E4:F4'); // SURAT KELUAR
                    $sheet->mergeCells('G4:G5'); // KETERANGAN
                        
                    // Align center untuk NO dan TGL
                    if ($highestRow >= 7) {
                        $sheet->getStyle('A7:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                }

                // Tanda Tangan
                $ttdRow = $highestRow + 3;
                $sheet->setCellValue('E' . $ttdRow, $namaDesaCamel . ', ' . \Carbon\Carbon::now()->translatedFormat('d F Y'));
                
                $sheet->setCellValue('B' . ($ttdRow + 1), 'Mengetahui,');
                $sheet->setCellValue('B' . ($ttdRow + 2), "KEPALA DESA {$namaDesa}");
                
                $sheet->setCellValue('E' . ($ttdRow + 1), 'SEKRETARIS DESA');
                
                $kades = \App\Models\StrukturDesa::where('kategori', 'kepala_desa')->where('status_aktif', true)->first();
                $sekdes = \App\Models\StrukturDesa::where('kategori', 'sekretaris')->where('status_aktif', true)->first();
                $namaKades = $kades ? $kades->nama : '..........................................';
                $namaSekdes = $sekdes ? $sekdes->nama : '..........................................';

                $ttdRowEnd = $ttdRow + 6;
                $sheet->setCellValue('B' . $ttdRowEnd, $namaKades);
                $sheet->setCellValue('E' . $ttdRowEnd, $namaSekdes);
                
                $sheet->getStyle('B' . $ttdRowEnd)->getFont()->setBold(true)->setUnderline(true);
                $sheet->getStyle('E' . $ttdRowEnd)->getFont()->setBold(true)->setUnderline(true);

                $sheet->getStyle('B' . $ttdRow . ':B' . $ttdRowEnd)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E' . $ttdRow . ':F' . $ttdRowEnd)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . ($ttdRow + 2))->getFont()->setBold(true);
                $sheet->getStyle('E' . ($ttdRow + 1))->getFont()->setBold(true);
                $sheet->mergeCells('B' . $ttdRow . ':C' . $ttdRow);
                $sheet->mergeCells('B' . ($ttdRow + 1) . ':C' . ($ttdRow + 1));
                $sheet->mergeCells('B' . ($ttdRow + 2) . ':C' . ($ttdRow + 2));
                $sheet->mergeCells('B' . $ttdRowEnd . ':C' . $ttdRowEnd);
                
                $sheet->mergeCells('E' . $ttdRow . ':F' . $ttdRow);
                $sheet->mergeCells('E' . ($ttdRow + 1) . ':F' . ($ttdRow + 1));
                $sheet->mergeCells('E' . $ttdRowEnd . ':F' . $ttdRowEnd);
            },
        ];
    }
}
