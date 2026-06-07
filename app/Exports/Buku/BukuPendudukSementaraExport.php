<?php

namespace App\Exports\Buku;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BukuPendudukSementaraExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, ShouldAutoSize
{
    protected $query;
    protected $rowNumber = 1;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
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
                'NAMA LENGKAP',
                'JENIS KELAMIN',
                'TEMPAT DAN TANGGAL LAHIR',
                'PEKERJAAN',
                'KEWARGANEGARAAN',
                'DATANG DARI',
                'MAKSUD DAN TUJUAN DATANG',
                'NAMA DAN ALAMAT YANG DIDATANGI',
                'DATANG TANGGAL',
                'PERGI TANGGAL',
                'KET'
            ],
            [
                '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'
            ]
        ];
    }

    public function map($item): array
    {
        $ttl = '-';
        if ($item->tempat_lahir || $item->tanggal_lahir) {
            $ttl = ($item->tempat_lahir ?? '') . ', ' . ($item->tanggal_lahir ? date('d-m-Y', strtotime($item->tanggal_lahir)) : '');
        }

        $datangTgl = $item->tanggal_masuk ? date('d-m-Y', strtotime($item->tanggal_masuk)) : '-';
        $pergiTgl = $item->tanggal_berlaku ? date('d-m-Y', strtotime($item->tanggal_berlaku)) : '-';

        return [
            $this->rowNumber++,
            $item->nama,
            $item->jenis_kelamin == 'Laki-Laki' || $item->jenis_kelamin == 'LAKI-LAKI' || $item->jenis_kelamin == 'L' ? 'L' : 'P',
            $ttl,
            $item->pekerjaan ?? '-',
            'WNI', // Asumsi sistem WNI
            $item->asal_daerah ?? ($item->alamat_asal ?? '-'),
            $item->keperluan_domisili ?? '-',
            $item->alamat_tinggal ?? '-',
            $datangTgl,
            $pergiTgl,
            $item->catatan ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $desa = strtoupper(\App\Models\DesaSetting::getValue('nama_desa', 'CIBATU'));
        $kades = strtoupper(\App\Models\StrukturDesa::where('jabatan', 'Kepala Desa')->value('nama') ?? '..................');
        $sekdes = strtoupper(\App\Models\StrukturDesa::where('jabatan', 'Sekretaris Desa')->value('nama') ?? '..................');

        // Main Header
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', "BUKU PENDUDUK SEMENTARA DESA {$desa}");
        $sheet->mergeCells('A2:L2');
        $sheet->setCellValue('A2', "(Lampiran XIV — Permendagri No. 47 Tahun 2016)");
        
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(false)->setSize(11);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');

        // Table Headers
        $sheet->getStyle('A4:L5')->getFont()->setBold(true);
        $sheet->getStyle('A4:L5')->getAlignment()->setHorizontal('center')->setVertical('center');
        $sheet->getStyle('A4:L5')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A4:L{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        $sheet->getStyle("A6:A{$lastRow}")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C6:C{$lastRow}")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("F6:F{$lastRow}")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("J6:K{$lastRow}")->getAlignment()->setHorizontal('center');

        // Footer TTD
        $footerRow = $lastRow + 3;
        $tanggal = \Carbon\Carbon::now()->translatedFormat('d F Y');
        
        $sheet->setCellValue("C{$footerRow}", "MENGETAHUI");
        $sheet->setCellValue("I{$footerRow}", "Desa {$desa}, {$tanggal}");
        
        $footerRow++;
        $sheet->setCellValue("C{$footerRow}", "KEPALA DESA {$desa}");
        $sheet->setCellValue("I{$footerRow}", "SEKRETARIS DESA");
        
        $footerRow += 4;
        $sheet->setCellValue("C{$footerRow}", $kades);
        $sheet->setCellValue("I{$footerRow}", $sekdes);
        
        $sheet->getStyle("C" . ($footerRow - 5) . ":I{$footerRow}")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C{$footerRow}")->getFont()->setBold(true)->setUnderline(true);
        $sheet->getStyle("I{$footerRow}")->getFont()->setBold(true)->setUnderline(true);

        return [];
    }
}
