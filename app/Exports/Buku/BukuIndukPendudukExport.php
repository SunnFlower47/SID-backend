<?php

namespace App\Exports\Buku;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class BukuIndukPendudukExport implements FromQuery, WithHeadings, WithMapping, WithCustomChunkSize, ShouldAutoSize, WithEvents, WithCustomStartCell, WithColumnFormatting
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

    // Mengambil 500 baris per eksekusi dari database untuk menghemat RAM
    public function chunkSize(): int
    {
        return 500;
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
                'NAMA LENGKAP / PANGGILAN',
                'JENIS KELAMIN',
                'STATUS PERKAWINAN',
                'TEMPAT & TANGGAL LAHIR',
                '',
                'AGAMA',
                'PENDIDIKAN TERAKHIR',
                'PEKERJAAN',
                'DAPAT MEMBACA HURUF',
                'KEWARGANEGARAAN',
                'ALAMAT LENGKAP',
                'KEDUDUKAN DLM KELUARGA',
                'NIK',
                'NO. KK',
                'KET'
            ],
            [
                '', '', '', '', 
                'TEMPAT LAHIR', 'TGL', 
                '', '', '', '', '', '', '', '', '', ''
            ]
        ];
    }

    public function map($item): array
    {
        $tanggalLahir = $item->tanggal_lahir ? \Carbon\Carbon::parse($item->tanggal_lahir)->format('d/m/Y') : '';
        $jk = $item->jenis_kelamin === 'LAKI-LAKI' ? 'L' : 'P';
        $alamat = trim($item->alamat . ' RT ' . $item->rt_label . ' / RW ' . $item->rw_label);
        $nkk = $item->nkk ?? ($item->kartuKeluarga->nkk ?? '');

        return [
            $this->rowNumber++,
            strtoupper($item->nama ?: ''),
            $jk,
            strtoupper($item->status_perkawinan ?: '-'),
            strtoupper($item->tempat_lahir ?: '-'),
            strtoupper($tanggalLahir ?: '-'),
            strtoupper($item->agama ?: ''),
            strtoupper($item->pendidikan ?: ''),
            strtoupper($item->pekerjaan ?: ''),
            strtoupper($item->dapat_membaca_huruf ?: '-'),
            strtoupper($item->warganegara ?: 'WNI'),
            strtoupper($alamat),
            strtoupper($item->kedudukan_keluarga ?: ''),
            "'" . $item->nik,
            $nkk ? "'" . $nkk : '',
            strtoupper($item->keterangan ?: '')
        ];
    }

    public function columnFormats(): array
    {
        return [
            'N' => NumberFormat::FORMAT_TEXT, // NIK
            'O' => NumberFormat::FORMAT_TEXT, // NKK
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

                
                // Menulis Kop Surat di baris 1 dan 2
                $sheet->mergeCells('A1:P1');
                $sheet->setCellValue('A1', "BUKU INDUK PENDUDUK DESA");
                $sheet->mergeCells('A2:P2');
                $sheet->setCellValue('A2', "(Lampiran XI — Permendagri No. 47 Tahun 2016)");
                
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A2')->getFont()->setBold(false)->setSize(11);
                $sheet->getStyle('A1:P2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn(); // O

                // Freeze Header (Baris 1 s.d. 5 akan diam saat di-scroll)
                $sheet->freezePane('A6');
                
                // Styling Tabel (Mulai Baris 4 ke bawah)
                if ($highestRow >= 4) {
                    $range = 'A4:' . $highestColumn . $highestRow;

                    // Set Border
                    $sheet->getStyle($range)
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);

                    // Vertical Center dan Wrap Text
                    $sheet->getStyle($range)
                        ->getAlignment()
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setWrapText(true);
                        
                    // Styling Header Tabel (Baris 4 & 5)
                    $sheet->getStyle('A4:P5')
                        ->getFont()
                        ->setBold(true);
                    $sheet->getStyle('A4:P5')
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle('A4:P5')
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFF0F0F0');

                    // Merge specific header columns (rowspan=2)
                    $columnsToMerge = ['A', 'B', 'C', 'D', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P'];
                    foreach ($columnsToMerge as $col) {
                        $sheet->mergeCells($col . '4:' . $col . '5');
                    }
                    
                    // Merge TEMPAT & TANGGAL LAHIR (colspan=2)
                    $sheet->mergeCells('E4:F4');
                }
            },
        ];
    }
}
