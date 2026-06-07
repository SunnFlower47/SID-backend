<?php

namespace App\Exports\Buku;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Services\Administrasi\BukuAdministrasiService;

class BukuRekapitulasiPendudukExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, ShouldAutoSize
{
    protected $filters;
    protected $rowNumber = 1;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        // Panggil service untuk mendapatkan kalkulasi array rekapitulasi
        $service = app(BukuAdministrasiService::class);
        $dataArray = $service->getData('buku-rekapitulasi-penduduk', $this->filters, true);
        return collect($dataArray);
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        return [
            [
                'NOMOR URUT',
                'NAMA DUSUN / LINGKUNGAN / KEL',
                'JUMLAH PENDUDUK AWAL BULAN', '', '', '', '',
                'TAMBAHAN BULAN INI', '', '', '',
                'PENGURANGAN BULAN INI', '', '', '',
                'JUMLAH PENDUDUK AKHIR BULAN', '', '', '', '',
                'KETERANGAN'
            ],
            [
                '', '', 
                'WNA', '', 'WNI', '', 'JML',
                'LAHIR', '', 'DATANG', '',
                'MATI', '', 'PINDAH', '',
                'WNA', '', 'WNI', '', 'JML',
                ''
            ],
            [
                '', '', 
                'L', 'P', 'L', 'P', 'TOTAL',
                'L', 'P', 'L', 'P',
                'L', 'P', 'L', 'P',
                'L', 'P', 'L', 'P', 'TOTAL',
                ''
            ]
        ];
    }

    public function map($item): array
    {
        $awalJml = $item['awal_wna_l'] + $item['awal_wna_p'] + $item['awal_wni_l'] + $item['awal_wni_p'];
        $akhirJml = $item['akhir_wna_l'] + $item['akhir_wna_p'] + $item['akhir_wni_l'] + $item['akhir_wni_p'];

        return [
            $this->rowNumber++,
            strtoupper($item['nama_dusun']),
            
            $item['awal_wna_l'], $item['awal_wna_p'], $item['awal_wni_l'], $item['awal_wni_p'], $awalJml,
            
            $item['tambah_lahir_l'], $item['tambah_lahir_p'], $item['tambah_datang_l'], $item['tambah_datang_p'],
            
            $item['kurang_mati_l'], $item['kurang_mati_p'], $item['kurang_pindah_l'], $item['kurang_pindah_p'],
            
            $item['akhir_wna_l'], $item['akhir_wna_p'], $item['akhir_wni_l'], $item['akhir_wni_p'], $akhirJml,
            
            ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $desa = strtoupper(\App\Models\DesaSetting::getValue('nama_desa', 'CIBATU'));
        $kecamatan = strtoupper(\App\Models\DesaSetting::getValue('kecamatan', 'KECAMATAN'));
        $kabupaten = strtoupper(\App\Models\DesaSetting::getValue('kabupaten', 'KABUPATEN'));
        $kades = strtoupper(\App\Models\StrukturDesa::where('jabatan', 'Kepala Desa')->value('nama') ?? '..................');
        $sekdes = strtoupper(\App\Models\StrukturDesa::where('jabatan', 'Sekretaris Desa')->value('nama') ?? '..................');

        // Main Header
        $sheet->mergeCells('A1:U1');
        $sheet->setCellValue('A1', "BUKU REKAPITULASI JUMLAH PENDUDUK DESA {$desa}");
        $sheet->mergeCells('A2:U2');
        $sheet->setCellValue('A2', "(Lampiran XIII — Permendagri No. 47 Tahun 2016)");
        
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(false)->setSize(11);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');

        // Table Headers Merge
        // Row 4
        $sheet->mergeCells('A4:A6'); // Nomor Urut
        $sheet->mergeCells('B4:B6'); // Nama Dusun
        $sheet->mergeCells('C4:G4'); // Awal Bulan
        $sheet->mergeCells('H4:K4'); // Tambahan
        $sheet->mergeCells('L4:O4'); // Pengurangan
        $sheet->mergeCells('P4:T4'); // Akhir Bulan
        $sheet->mergeCells('U4:U6'); // Keterangan

        // Row 5
        $sheet->mergeCells('C5:D5'); // WNA (Awal)
        $sheet->mergeCells('E5:F5'); // WNI (Awal)
        $sheet->mergeCells('G5:G6'); // Total (Awal)
        
        $sheet->mergeCells('H5:I5'); // Lahir
        $sheet->mergeCells('J5:K5'); // Datang
        
        $sheet->mergeCells('L5:M5'); // Mati
        $sheet->mergeCells('N5:O5'); // Pindah
        
        $sheet->mergeCells('P5:Q5'); // WNA (Akhir)
        $sheet->mergeCells('R5:S5'); // WNI (Akhir)
        $sheet->mergeCells('T5:T6'); // Total (Akhir)

        $sheet->getStyle('A4:U6')->getFont()->setBold(true);
        $sheet->getStyle('A4:U6')->getAlignment()->setHorizontal('center')->setVertical('center');
        $sheet->getStyle('A4:U6')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $lastRow = $sheet->getHighestRow();
        
        $sheet->getStyle("A4:U{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        $sheet->getStyle("A7:A{$lastRow}")->getAlignment()->setHorizontal('center');
        // Numeric center
        $sheet->getStyle("C7:T{$lastRow}")->getAlignment()->setHorizontal('center');

        // Footer TTD
        $footerRow = $lastRow + 3;
        $tanggal = \Carbon\Carbon::now()->translatedFormat('d F Y');
        
        $sheet->setCellValue("D{$footerRow}", "MENGETAHUI");
        $sheet->setCellValue("R{$footerRow}", "Desa {$desa}, {$tanggal}");
        
        $footerRow++;
        $sheet->setCellValue("D{$footerRow}", "KEPALA DESA {$desa}");
        $sheet->setCellValue("R{$footerRow}", "SEKRETARIS DESA");
        
        $footerRow += 4;
        $sheet->setCellValue("D{$footerRow}", $kades);
        $sheet->setCellValue("R{$footerRow}", $sekdes);
        
        $sheet->getStyle("D" . ($footerRow - 5) . ":R{$footerRow}")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("D{$footerRow}")->getFont()->setBold(true)->setUnderline(true);
        $sheet->getStyle("R{$footerRow}")->getFont()->setBold(true)->setUnderline(true);

        return [];
    }
}
