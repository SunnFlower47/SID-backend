<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class KartuKeluargaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $request;

    public function __construct($request = null)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = DB::table('penduduks as p')
            ->leftJoin('mutasis as m', function($join) {
                $join->on('m.penduduk_id', '=', 'p.id')
                     ->whereIn('m.jenis_mutasi', ['kematian', 'pindah_keluar', 'pisah_kk']);
            })
            ->leftJoin('rts', 'p.rt_id', '=', 'rts.id')
            ->leftJoin('rws', 'p.rw_id', '=', 'rws.id')
            ->leftJoin('dusuns', 'p.dusun_id', '=', 'dusuns.id')
            ->select([
                'p.nkk',
                DB::raw('MAX(CASE WHEN p.kedudukan_keluarga = "Kepala Keluarga" THEN p.nama ELSE NULL END) as nama_kepala_keluarga'),
                DB::raw('MAX(CASE WHEN p.kedudukan_keluarga = "Kepala Keluarga" THEN rts.kode ELSE NULL END) as rt'),
                DB::raw('MAX(CASE WHEN p.kedudukan_keluarga = "Kepala Keluarga" THEN rws.kode ELSE NULL END) as rw'),
                DB::raw('MAX(CASE WHEN p.kedudukan_keluarga = "Kepala Keluarga" THEN dusuns.nama ELSE NULL END) as dusun'),
                DB::raw('MAX(CASE WHEN p.kedudukan_keluarga = "Kepala Keluarga" THEN p.alamat ELSE NULL END) as alamat'),
                DB::raw('COUNT(DISTINCT p.id) as jumlah_anggota'),
                DB::raw('SUM(CASE WHEN m.id IS NULL THEN 1 ELSE 0 END) as anggota_aktif'),
                DB::raw('SUM(CASE WHEN m.jenis_mutasi = "kematian" THEN 1 ELSE 0 END) as anggota_meninggal'),
                DB::raw('SUM(CASE WHEN m.jenis_mutasi = "pindah_keluar" THEN 1 ELSE 0 END) as anggota_pindah'),
                DB::raw('SUM(CASE WHEN m.jenis_mutasi = "pisah_kk" THEN 1 ELSE 0 END) as anggota_pisah_kk'),
                DB::raw('SUM(CASE WHEN m.id IS NOT NULL THEN 1 ELSE 0 END) as anggota_mutasi'),
                DB::raw('MIN(p.created_at) as tanggal_dibuat'),
                DB::raw('MAX(p.updated_at) as tanggal_update')
            ])
            ->whereNotNull('p.nkk')
            ->where('p.nkk', '!=', '')
            ->groupBy('p.nkk');

        // Apply filters from request
        if ($this->request) {
            // Search filter
            if ($this->request->filled('search')) {
                $search = $this->request->search;
                $query->where(function($q) use ($search) {
                    $q->where('p.nkk', 'like', "%{$search}%")
                      ->orWhere('p.nama', 'like', "%{$search}%");
                });
            }

            // Status filter
            $status = $this->request->get('status', 'all');
            if ($status === 'aktif') {
                $query->having('anggota_aktif', '>', 0)
                      ->having('anggota_mutasi', '=', 0);
            } elseif ($status === 'bermasalah') {
                $query->having('anggota_aktif', '>', 0)
                      ->having('anggota_mutasi', '>', 0);
            } elseif ($status === 'kosong') {
                $query->having('anggota_aktif', '=', 0);
            }
        }

        return $query->orderBy('tanggal_update', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'NKK',
            'KEPALA KELUARGA',
            'RT',
            'RW',
            'DUSUN',
            'ALAMAT',
            'JUMLAH ANGGOTA',
            'ANGGOTA AKTIF',
            'ANGGOTA MENINGGAL',
            'ANGGOTA PINDAH',
            'ANGGOTA PISAH KK',
            'STATUS KK',
            'TANGGAL DIBUAT',
            'TANGGAL UPDATE'
        ];
    }

    public function map($kk): array
    {
        static $no = 0;
        $no++;

        // Determine status
        if ($kk->anggota_aktif == 0) {
            $status = 'Kosong';
        } elseif ($kk->anggota_meninggal > 0 || $kk->anggota_pindah > 0 || $kk->anggota_pisah_kk > 0) {
            $status = 'Bermasalah';
        } else {
            $status = 'Aktif';
        }

        return [
            $no,
            "'" . $kk->nkk, // Add apostrophe prefix to force text format
            $kk->nama_kepala_keluarga ?: 'Tidak ada',
            $kk->rt ?: '-',
            $kk->rw ?: '-',
            $kk->dusun ?: '-',
            $kk->alamat ?: '-',
            $kk->jumlah_anggota,
            $kk->anggota_aktif,
            $kk->anggota_meninggal,
            $kk->anggota_pindah,
            $kk->anggota_pisah_kk,
            $status,
            \Carbon\Carbon::parse($kk->tanggal_dibuat)->format('d/m/Y'),
            \Carbon\Carbon::parse($kk->tanggal_update)->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style header row
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '16A34A'] // Green-600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Style data rows
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A2:O' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Auto row height
        for ($row = 1; $row <= $lastRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(-1);
        }

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // NO
            'B' => 20,  // NKK
            'C' => 30,  // KEPALA KELUARGA
            'D' => 8,   // RT
            'E' => 8,   // RW
            'F' => 15,  // DUSUN
            'G' => 40,  // ALAMAT
            'H' => 15,  // JUMLAH ANGGOTA
            'I' => 15,  // ANGGOTA AKTIF
            'J' => 18,  // ANGGOTA MENINGGAL
            'K' => 15,  // ANGGOTA PINDAH
            'L' => 18,  // ANGGOTA PISAH KK
            'M' => 15,  // STATUS KK
            'N' => 15,  // TANGGAL DIBUAT
            'O' => 18,  // TANGGAL UPDATE
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT, // NKK as text
        ];
    }
}

