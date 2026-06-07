<?php

namespace App\Exports\Buku;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BukuKtpKkExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, ShouldAutoSize
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
                'NO. KK',
                'NAMA LENGKAP',
                'NIK',
                'JENIS KELAMIN',
                'TEMPAT / TGL LAHIR',
                'GOLONGAN DARAH',
                'AGAMA',
                'PENDIDIKAN',
                'PEKERJAAN',
                'ALAMAT',
                'STATUS PERKAWINAN',
                'TEMPAT DAN TANGGAL DIKELUARKAN',
                'STATUS HUBUNGAN KELUARGA',
                'KEWARGANEGARAAN',
                'ORANG TUA',
                '',
                'TGL MULAI TINGGAL DI DESA',
                'KET'
            ],
            [
                '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
                'AYAH', 'IBU', '', ''
            ],
            [
                '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19'
            ]
        ];
    }

    public function map($item): array
    {
        $ttl = '-';
        if ($item->tempat_lahir || $item->tanggal_lahir) {
            $ttl = ($item->tempat_lahir ?? '') . ', ' . ($item->tanggal_lahir ? date('d-m-Y', strtotime($item->tanggal_lahir)) : '');
        }

        $tanggalMasuk = $item->created_at ? date('d-m-Y', strtotime($item->created_at)) : '-';
        
        $tanggalDikeluarkan = '-';
        if ($item->kartuKeluarga && ($item->kartuKeluarga->tempat_dikeluarkan || $item->kartuKeluarga->tanggal_dikeluarkan)) {
            $tanggalDikeluarkan = ($item->kartuKeluarga->tempat_dikeluarkan ?? '') . ', ' . ($item->kartuKeluarga->tanggal_dikeluarkan ? date('d-m-Y', strtotime($item->kartuKeluarga->tanggal_dikeluarkan)) : '');
        }

        return [
            $this->rowNumber++,
            $item->kartuKeluarga ? $item->kartuKeluarga->nkk : '-',
            $item->nama,
            $item->nik,
            $item->jenis_kelamin == 'Laki-Laki' || $item->jenis_kelamin == 'LAKI-LAKI' || $item->jenis_kelamin == 'L' ? 'L' : 'P',
            $ttl,
            $item->golongan_darah ?? '-',
            $item->agama ?? '-',
            $item->pendidikan ?? '-',
            $item->pekerjaan ?? '-',
            $item->kartuKeluarga ? $item->kartuKeluarga->alamat : '-',
            $item->status_perkawinan ?? '-',
            $tanggalDikeluarkan,
            $item->kedudukan_keluarga ?? '-',
            $item->kewarganegaraan ?? ($item->warganegara ?? 'WNI'),
            $item->nama_ayah ?? '-',
            $item->nama_ibu ?? '-',
            $tanggalMasuk,
            $item->keterangan ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $desa = strtoupper(\App\Models\DesaSetting::getValue('nama_desa', 'CIBATU'));
        $kades = strtoupper(\App\Models\StrukturDesa::where('jabatan', 'Kepala Desa')->value('nama') ?? '..................');
        $sekdes = strtoupper(\App\Models\StrukturDesa::where('jabatan', 'Sekretaris Desa')->value('nama') ?? '..................');

        // Main Header
        $sheet->mergeCells('A1:S1');
        $sheet->setCellValue('A1', "BUKU KARTU TANDA PENDUDUK DAN BUKU KARTU KELUARGA DESA {$desa}");
        $sheet->mergeCells('A2:S2');
        $sheet->setCellValue('A2', "(Lampiran XV — Permendagri No. 47 Tahun 2016)");
        
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(false)->setSize(11);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');

        // Table Headers
        $sheet->getStyle('A4:S6')->getFont()->setBold(true);
        $sheet->getStyle('A4:S6')->getAlignment()->setHorizontal('center')->setVertical('center');
        $sheet->getStyle('A4:S6')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Header Merges
        $columnsToMerge = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'R', 'S'];
        foreach ($columnsToMerge as $col) {
            $sheet->mergeCells("{$col}4:{$col}5");
        }
        $sheet->mergeCells("P4:Q4"); // ORANG TUA

        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A4:S{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        $sheet->getStyle("A7:A{$lastRow}")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("E7:E{$lastRow}")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("G7:G{$lastRow}")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("R7:R{$lastRow}")->getAlignment()->setHorizontal('center');

        // Footer TTD
        $footerRow = $lastRow + 3;
        $tanggal = \Carbon\Carbon::now()->translatedFormat('d F Y');
        
        $sheet->setCellValue("E{$footerRow}", "MENGETAHUI");
        $sheet->setCellValue("P{$footerRow}", "Desa {$desa}, {$tanggal}");
        
        $footerRow++;
        $sheet->setCellValue("E{$footerRow}", "KEPALA DESA {$desa}");
        $sheet->setCellValue("P{$footerRow}", "SEKRETARIS DESA");
        
        $footerRow += 4;
        $sheet->setCellValue("E{$footerRow}", $kades);
        $sheet->setCellValue("P{$footerRow}", $sekdes);
        
        $sheet->getStyle("E" . ($footerRow - 5) . ":P{$footerRow}")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("E{$footerRow}")->getFont()->setBold(true)->setUnderline(true);
        $sheet->getStyle("P{$footerRow}")->getFont()->setBold(true)->setUnderline(true);

        return [];
    }
}
