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

class TanahKasDesaExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithTitle, WithCustomStartCell
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
        return "Buku Tanah Kas Desa";
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
                'NAMA / JENIS TANAH',
                'KODE BARANG',
                'LOKASI',
                'LUAS (m²)',
                'NO. SERTIFIKAT / BUKTI',
                'TGL PEROLEHAN',
                'ASAL USUL',
                'KONDISI',
                'KETERANGAN'
            ],
            [
                '1', '2', '3', '4', '5', '6', '7', '8', '9', '10'
            ]
        ];
    }

    public function map($row): array
    {
        $kondisiLabel = match($row->kondisi) {
            'baik'         => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat'  => 'Rusak Berat',
            default        => '-'
        };

        return [
            $this->rowNumber++,
            $row->nama_barang_override ?: ($row->barang->nama_barang ?? '-'),
            $row->barang->kode_barang ?? '-',
            $row->lokasi ?? '-',
            $row->saldo_kwantitas > 0 ? $row->saldo_kwantitas : '-',
            $row->no_sertifikat ?? '-',
            $row->tanggal_perolehan ? \Carbon\Carbon::parse($row->tanggal_perolehan)->translatedFormat('d F Y') : '-',
            $row->asal_usul ?? '-',
            $kondisiLabel,
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
                $sheet->mergeCells('A1:J1');
                $sheet->setCellValue('A1', 'BUKU TANAH KAS DESA');
                $sheet->mergeCells('A2:J2');
                $sheet->setCellValue('A2', '(Lampiran VI — Permendagri No. 47 Tahun 2016)');
                
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A2')->getFont()->setSize(11);
                $sheet->getStyle('A1:J2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $highestRow = $sheet->getHighestRow();
                $highestColumn = 'J';
                
                // Tambahkan Row Total
                if (count($this->data) > 0) {
                    $totalLuas = $this->data->sum('saldo_kwantitas');
                    $highestRow++;
                    $sheet->mergeCells("A{$highestRow}:D{$highestRow}");
                    $sheet->setCellValue("A{$highestRow}", 'JUMLAH TOTAL');
                    $sheet->setCellValue("E{$highestRow}", $totalLuas);
                    $sheet->getStyle("A{$highestRow}:J{$highestRow}")->getFont()->setBold(true);
                    $sheet->getStyle("A{$highestRow}:J{$highestRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF0F0F0');
                    $sheet->getStyle("A{$highestRow}:E{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
                
                $sheet->freezePane('A6');

                // Lebar kolom
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(25);
                $sheet->getColumnDimension('C')->setWidth(15);
                $sheet->getColumnDimension('D')->setWidth(25);
                $sheet->getColumnDimension('E')->setWidth(15);
                $sheet->getColumnDimension('F')->setWidth(20);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(15);
                $sheet->getColumnDimension('J')->setWidth(30);

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
                    $sheet->getStyle('A4:J5')->getFont()->setBold(true);
                    $sheet->getStyle('A4:J5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A4:J5')
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFF0F0F0');
                        
                    // Align center untuk beberapa kolom
                    if ($highestRow >= 6) {
                        $sheet->getStyle('A6:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('C6:C' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('E6:I' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                }

                // Tanda Tangan
                $ttdRow = $highestRow + 3;
                $sheet->setCellValue('H' . $ttdRow, $namaDesaCamel . ', ' . \Carbon\Carbon::now()->translatedFormat('d F Y'));
                
                $sheet->setCellValue('B' . ($ttdRow + 1), 'Mengetahui,');
                $sheet->setCellValue('B' . ($ttdRow + 2), "KEPALA DESA {$namaDesa}");
                
                $sheet->setCellValue('H' . ($ttdRow + 1), 'SEKRETARIS DESA');
                
                $kades = \App\Models\StrukturDesa::where('kategori', 'kepala_desa')->where('status_aktif', true)->first();
                $sekdes = \App\Models\StrukturDesa::where('kategori', 'sekretaris')->where('status_aktif', true)->first();
                $namaKades = $kades ? $kades->nama : '..........................................';
                $namaSekdes = $sekdes ? $sekdes->nama : '..........................................';

                $ttdRowEnd = $ttdRow + 6;
                $sheet->setCellValue('B' . $ttdRowEnd, $namaKades);
                $sheet->setCellValue('H' . $ttdRowEnd, $namaSekdes);
                
                $sheet->getStyle('B' . $ttdRowEnd)->getFont()->setBold(true)->setUnderline(true);
                $sheet->getStyle('H' . $ttdRowEnd)->getFont()->setBold(true)->setUnderline(true);

                $sheet->getStyle('B' . $ttdRow . ':B' . $ttdRowEnd)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H' . $ttdRow . ':J' . $ttdRowEnd)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . ($ttdRow + 2))->getFont()->setBold(true);
                $sheet->getStyle('H' . ($ttdRow + 1))->getFont()->setBold(true);
                $sheet->mergeCells('B' . $ttdRow . ':C' . $ttdRow);
                $sheet->mergeCells('B' . ($ttdRow + 1) . ':C' . ($ttdRow + 1));
                $sheet->mergeCells('B' . ($ttdRow + 2) . ':C' . ($ttdRow + 2));
                $sheet->mergeCells('B' . $ttdRowEnd . ':C' . $ttdRowEnd);
                
                $sheet->mergeCells('H' . $ttdRow . ':J' . $ttdRow);
                $sheet->mergeCells('H' . ($ttdRow + 1) . ':J' . ($ttdRow + 1));
                $sheet->mergeCells('H' . $ttdRowEnd . ':J' . $ttdRowEnd);
            },
        ];
    }
}
