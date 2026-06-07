<?php

namespace App\Exports\Buku;

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

class InventarisKekayaanExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithTitle, WithCustomStartCell
{
    protected $data;
    protected $tahun;
    protected $rowNumber = 1;

    public function __construct(string $viewName, array $viewData)
    {
        $this->data = $viewData['data'];
        $this->tahun = $viewData['tahun'] ?? date('Y');
    }

    public function collection()
    {
        return $this->data;
    }

    public function title(): string
    {
        return "Buku Inventaris " . $this->tahun;
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function headings(): array
    {
        return [
            [
                'NO',
                "JENIS BARANG /\nBANGUNAN",
                'ASAL BARANG / BANGUNAN',
                '', '', '', '',
                "KEADAAN BARANG /\nBANGUNAN AWAL TAHUN",
                '',
                'PENGHAPUSAN BARANG DAN BANGUNAN',
                '', '', '',
                "KEADAAN BARANG /\nBANGUNAN AKHIR TAHUN",
                '',
                'KET.'
            ],
            [
                '', '',
                "DIBELI\nSENDIRI",
                'BANTUAN',
                '', '',
                'SUMBANGAN',
                'BAIK',
                'RUSAK',
                'RUSAK',
                'DIJUAL',
                "DISUMBANG\nKAN",
                "TGL\nPENGHAPUSAN",
                'BAIK',
                'RUSAK',
                ''
            ],
            [
                '', '', '',
                "PEMERINTAH\n(PUSAT)",
                'PROVINSI',
                "KAB/\nKOTA",
                '', '', '', '', '', '', '', '', '', ''
            ],
            [
                '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16'
            ]
        ];
    }

    public function map($row): array
    {
        $namaBarang = $row['nama_barang'];
        if ($row['kode_barang'] !== '-') {
            $namaBarang .= "\n" . $row['kode_barang'];
        }

        return [
            $this->rowNumber++,
            $namaBarang,
            $row['asal_dibeli'] > 0 ? $row['asal_dibeli'] : '-',
            $row['asal_bantuan_pusat'] > 0 ? $row['asal_bantuan_pusat'] : '-',
            $row['asal_bantuan_prov'] > 0 ? $row['asal_bantuan_prov'] : '-',
            $row['asal_bantuan_kab'] > 0 ? $row['asal_bantuan_kab'] : '-',
            $row['asal_sumbangan'] > 0 ? $row['asal_sumbangan'] : '-',
            $row['awal_baik'] > 0 ? $row['awal_baik'] : '-',
            $row['awal_rusak'] > 0 ? $row['awal_rusak'] : '-',
            $row['hapus_rusak'] > 0 ? $row['hapus_rusak'] : '-',
            $row['hapus_dijual'] > 0 ? $row['hapus_dijual'] : '-',
            $row['hapus_disumbangkan'] > 0 ? $row['hapus_disumbangkan'] : '-',
            $row['tgl_penghapusan'] ?: '-',
            $row['akhir_baik'] > 0 ? $row['akhir_baik'] : '-',
            $row['akhir_rusak'] > 0 ? $row['akhir_rusak'] : '-',
            $row['keterangan'] ?: '-'
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

                
                // Menulis Kop Laporan di baris 1 s/d 3
                $sheet->mergeCells('A1:P1');
                $sheet->setCellValue('A1', 'BUKU INVENTARIS DAN KEKAYAAN DESA');
                $sheet->mergeCells('A2:P2');
                $sheet->setCellValue('A2', 'Tahun Anggaran: ' . $this->tahun);
                $sheet->mergeCells('A3:P3');
                $sheet->setCellValue('A3', '(Format Permendagri No. 47 Tahun 2016 — Lampiran Buku Administrasi Umum)');
                
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A2:A3')->getFont()->setBold(true)->setSize(11);
                $sheet->getStyle('A1:P3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $highestRow = $sheet->getHighestRow();
                $highestColumn = 'P';

                // Freeze Header (Baris 1 s/d 8 akan diam saat di-scroll)
                $sheet->freezePane('A9');

                // Set lebar kolom spesifik
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(35);
                $sheet->getColumnDimension('C')->setWidth(14);
                $sheet->getColumnDimension('D')->setWidth(14);
                $sheet->getColumnDimension('E')->setWidth(14);
                $sheet->getColumnDimension('F')->setWidth(14);
                $sheet->getColumnDimension('G')->setWidth(14);
                $sheet->getColumnDimension('H')->setWidth(12);
                $sheet->getColumnDimension('I')->setWidth(12);
                $sheet->getColumnDimension('J')->setWidth(12);
                $sheet->getColumnDimension('K')->setWidth(12);
                $sheet->getColumnDimension('L')->setWidth(14);
                $sheet->getColumnDimension('M')->setWidth(16);
                $sheet->getColumnDimension('N')->setWidth(12);
                $sheet->getColumnDimension('O')->setWidth(12);
                $sheet->getColumnDimension('P')->setWidth(25);

                if ($highestRow >= 5) {
                    $range = 'A5:' . $highestColumn . $highestRow;

                    // Set Border seluruh tabel
                    $sheet->getStyle($range)
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);

                    // Vertical Center & Wrap Text (Supaya \n terbaca)
                    $sheet->getStyle($range)
                        ->getAlignment()
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setWrapText(true);
                        
                    // Header Tabel (Baris 5 s/d 8)
                    $sheet->getStyle('A5:P8')->getFont()->setBold(true);
                    $sheet->getStyle('A5:P8')
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A5:P8')
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFF0F0F0');

                    // Center semua text/angka untuk kolom angka/nilai
                    if ($highestRow >= 9) {
                        $sheet->getStyle('C9:O' . $highestRow)
                            ->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('A9:A' . $highestRow)
                            ->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }

                    // ====== MERGE HEADERS ======
                    // Baris 5 (Utama)
                    $sheet->mergeCells('A5:A7'); // NO
                    $sheet->mergeCells('B5:B7'); // JENIS BARANG
                    $sheet->mergeCells('C5:G5'); // ASAL BARANG / BANGUNAN
                    $sheet->mergeCells('H5:I5'); // KEADAAN AWAL
                    $sheet->mergeCells('J5:M5'); // PENGHAPUSAN
                    $sheet->mergeCells('N5:O5'); // KEADAAN AKHIR
                    $sheet->mergeCells('P5:P7'); // KET.

                    // Baris 6 (Sub-header)
                    $sheet->mergeCells('C6:C7'); // DIBELI SENDIRI
                    $sheet->mergeCells('D6:F6'); // BANTUAN
                    $sheet->mergeCells('G6:G7'); // SUMBANGAN
                    $sheet->mergeCells('H6:H7'); // BAIK
                    $sheet->mergeCells('I6:I7'); // RUSAK
                    $sheet->mergeCells('J6:J7'); // RUSAK
                    $sheet->mergeCells('K6:K7'); // DIJUAL
                    $sheet->mergeCells('L6:L7'); // DISUMBANGKAN
                    $sheet->mergeCells('M6:M7'); // TGL PENGHAPUSAN
                    $sheet->mergeCells('N6:N7'); // BAIK
                    $sheet->mergeCells('O6:O7'); // RUSAK

                    // Baris 7 (Bantuan Detail)
                    // D7, E7, F7 tidak dimerge, terisi teks dari index ke-2 `headings()`
                }
                
                // Tambahkan TTD di akhir
                $ttdRow = $highestRow + 3;
                $sheet->setCellValue('M' . $ttdRow, $namaDesaCamel . ', ' . \Carbon\Carbon::now()->translatedFormat('d F Y'));
                
                $sheet->setCellValue('B' . ($ttdRow + 1), 'Mengetahui,');
                $sheet->setCellValue('B' . ($ttdRow + 2), "KEPALA DESA {$namaDesa}");
                
                $sheet->setCellValue('M' . ($ttdRow + 1), 'SEKRETARIS DESA');
                
                // Spasi TTD
                $kades = \App\Models\StrukturDesa::where('kategori', 'kepala_desa')->where('status_aktif', true)->first();
                $sekdes = \App\Models\StrukturDesa::where('kategori', 'sekretaris')->where('status_aktif', true)->first();
                $namaKades = $kades ? $kades->nama : '..........................................';
                $namaSekdes = $sekdes ? $sekdes->nama : '..........................................';

                $ttdRowEnd = $ttdRow + 6;
                $sheet->setCellValue('B' . $ttdRowEnd, $namaKades);
                $sheet->setCellValue('M' . $ttdRowEnd, $namaSekdes);
                
                $sheet->getStyle('B' . $ttdRowEnd)->getFont()->setBold(true)->setUnderline(true);
                $sheet->getStyle('M' . $ttdRowEnd)->getFont()->setBold(true)->setUnderline(true);

                $sheet->getStyle('B' . $ttdRow . ':B' . $ttdRowEnd)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('M' . $ttdRow . ':M' . $ttdRowEnd)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . ($ttdRow + 2))->getFont()->setBold(true);
                $sheet->getStyle('M' . ($ttdRow + 1))->getFont()->setBold(true);
            },
        ];
    }
}
