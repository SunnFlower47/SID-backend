<?php

namespace App\Exports\Buku;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;
use App\Models\DesaSetting;
use App\Models\MasterJabatan;


class BukuMutasiPendudukExport implements FromQuery, WithMapping, WithHeadings, WithEvents, WithCustomStartCell, WithTitle, ShouldAutoSize
{
    protected $query;
    protected $rowNumber = 0;
    protected $startRow = 12; // Data starts at row 12

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function map($mutasi): array
    {
        $this->rowNumber++;

        $penduduk = $mutasi->penduduk;
        
        $ttl = $penduduk ? ($penduduk->tempat_lahir . ', ' . ($penduduk->tanggal_lahir ? Carbon::parse($penduduk->tanggal_lahir)->translatedFormat('d M Y') : '')) : '-';

        $datangDari = '-';
        $tglDatang = '-';
        $pindahKe = '-';
        $tglPindah = '-';
        $meninggal = '-';
        
        $tglMutasi = $mutasi->tanggal_mutasi ? Carbon::parse($mutasi->tanggal_mutasi)->translatedFormat('d M Y') : '-';

        if ($mutasi->jenis_mutasi === 'pindah_masuk' || $mutasi->jenis_mutasi === 'kelahiran') {
            $datangDari = $mutasi->asal_tujuan ?? '-';
            $tglDatang = $tglMutasi;
        } elseif ($mutasi->jenis_mutasi === 'pindah_keluar') {
            $pindahKe = $mutasi->asal_tujuan ?? '-';
            $tglPindah = $tglMutasi;
        } elseif ($mutasi->jenis_mutasi === 'kematian') {
            $lokasi = $mutasi->asal_tujuan ?? 'Tidak diketahui';
            $meninggal = $tglMutasi . ' di ' . $lokasi;
        }

        return [
            $this->rowNumber,
            $penduduk ? $penduduk->nama : 'Penduduk Terhapus',
            $ttl,
            $penduduk ? ($penduduk->jenis_kelamin == 'Laki-Laki' || $penduduk->jenis_kelamin == 'LAKI-LAKI' ? 'L' : 'P') : '-',
            $penduduk ? ($penduduk->warganegara ?? $penduduk->kewarganegaraan ?? 'WNI') : '-',
            $datangDari,
            $tglDatang,
            $pindahKe,
            $tglPindah,
            $meninggal,
            $mutasi->alasan ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            ['NO', 'NAMA LENGKAP', "TEMPAT DAN\nTGL LAHIR", "JENIS\nKELAMIN", "KEWARGA-\nNEGARAAN", 'DATANG DARI', '', 'PINDAH KE', '', "MENINGGAL\n(TEMPAT & TGL)", 'KETERANGAN'],
            ['', '', '', '', '', 'ASAL USUL', 'TANGGAL', 'TUJUAN', 'TANGGAL', '', ''],
            ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11']
        ];
    }

    public function startCell(): string
    {
        return 'A9';
    }

    public function title(): string
    {
        return 'Buku Mutasi Penduduk';
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

                // Header / Kop
                $sheet->mergeCells('A1:K1');
                $sheet->setCellValue('A1', "BUKU MUTASI PENDUDUK DESA");
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                $sheet->mergeCells('A2:K2');
                $sheet->setCellValue('A2', "(Lampiran XII — Permendagri No. 47 Tahun 2016)");
                $sheet->getStyle('A2')->getFont()->setSize(11);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

                // Merge headings
                $sheet->mergeCells('A9:A10');
                $sheet->mergeCells('B9:B10');
                $sheet->mergeCells('C9:C10');
                $sheet->mergeCells('D9:D10');
                $sheet->mergeCells('E9:E10');
                $sheet->mergeCells('F9:G9'); // Datang dari
                $sheet->mergeCells('H9:I9'); // Pindah Ke
                $sheet->mergeCells('J9:J10');
                $sheet->mergeCells('K9:K10');

                // Styling headings
                $sheet->getStyle('A9:K11')->getFont()->setBold(true);
                $sheet->getStyle('A9:K11')->getAlignment()->setHorizontal('center')->setVertical('center')->setWrapText(true);
                
                // Set AutoSize false because ShouldAutoSize doesn't play well with merged kop
                // We'll set manual width
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(20);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(20);
                $sheet->getColumnDimension('I')->setWidth(15);
                $sheet->getColumnDimension('J')->setWidth(25);
                $sheet->getColumnDimension('K')->setWidth(20);

                $lastRow = $this->rowNumber + 11;
                
                // Borders for data
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ];
                $sheet->getStyle('A9:K' . $lastRow)->applyFromArray($styleArray);
                $sheet->getStyle('A12:A' . $lastRow)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('D12:D' . $lastRow)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('E12:G' . $lastRow)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('I12:I' . $lastRow)->getAlignment()->setHorizontal('center');

                // Menyiapkan Tanda Tangan
                $ttdRow = $lastRow + 3;

                $kades = \App\Models\StrukturDesa::where('kategori', 'kepala_desa')->where('status_aktif', true)->first();
                $sekdes = \App\Models\StrukturDesa::where('kategori', 'sekretaris')->where('status_aktif', true)->first();

                $namaKades = $kades ? $kades->nama : '..........................';
                $namaSekdes = $sekdes ? $sekdes->nama : '..........................';

                // Kolom TTD Sekdes (Kiri)
                $sheet->setCellValue("B{$ttdRow}", "MENGETAHUI,");
                $sheet->setCellValue("B" . ($ttdRow + 1), "SEKRETARIS DESA");
                $sheet->getStyle("B{$ttdRow}:B" . ($ttdRow + 1))->getAlignment()->setHorizontal('center');
                
                $sheet->setCellValue("B" . ($ttdRow + 5), $namaSekdes);
                $sheet->getStyle("B" . ($ttdRow + 5))->getFont()->setBold(true)->setUnderline(true);
                $sheet->getStyle("B" . ($ttdRow + 5))->getAlignment()->setHorizontal('center');

                // Kolom TTD Kades (Kanan)
                $sheet->setCellValue("J{$ttdRow}", "Cibatu, " . date('d F Y'));
                $sheet->setCellValue("J" . ($ttdRow + 1), "KEPALA DESA {$namaDesa}");
                $sheet->getStyle("J{$ttdRow}:J" . ($ttdRow + 1))->getAlignment()->setHorizontal('center');

                $sheet->setCellValue("J" . ($ttdRow + 5), $namaKades);
                $sheet->getStyle("J" . ($ttdRow + 5))->getFont()->setBold(true)->setUnderline(true);
                $sheet->getStyle("J" . ($ttdRow + 5))->getAlignment()->setHorizontal('center');
            },
        ];
    }
}
