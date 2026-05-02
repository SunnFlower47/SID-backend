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
        $query = DB::table('kartu_keluargas as kk')
            ->leftJoin('rts', 'kk.rt_id', '=', 'rts.id')
            ->leftJoin('rws', 'kk.rw_id', '=', 'rws.id')
            ->leftJoin('dusuns', 'kk.dusun_id', '=', 'dusuns.id')
            ->leftJoin('penduduks as p', 'p.kartu_keluarga_id', '=', 'kk.id')
            ->leftJoin('mutasis as m', function($join) {
                $join->on('m.penduduk_id', '=', 'p.id')
                     ->whereIn('m.jenis_mutasi', ['kematian', 'pindah_keluar', 'pisah_kk']);
            })
            ->select([
                'kk.nkk',
                'kk.nama_kepala_keluarga',
                'rts.kode as rt',
                'rws.kode as rw',
                'dusuns.nama as dusun',
                'kk.alamat',
                'kk.jumlah_anggota',
                'kk.anggota_aktif',
                'kk.anggota_meninggal',
                'kk.anggota_pindah',
                'kk.anggota_pisah_kk',
                'kk.status_kk',
                'kk.created_at as tanggal_dibuat',
                'kk.updated_at as tanggal_update'
            ])
            ->groupBy('kk.id', 'kk.nkk', 'kk.nama_kepala_keluarga', 'rts.kode', 'rws.kode', 'dusuns.nama', 'kk.alamat', 'kk.jumlah_anggota', 'kk.anggota_aktif', 'kk.anggota_meninggal', 'kk.anggota_pindah', 'kk.anggota_pisah_kk', 'kk.status_kk', 'kk.created_at', 'kk.updated_at');

        // Apply filters from request
        if ($this->request) {
            // Search filter
            if (!empty($this->request['search'])) {
                $search = $this->request['search'];
                $query->where(function($q) use ($search) {
                    $q->where('kk.nkk', 'like', "%{$search}%")
                      ->orWhere('kk.nama_kepala_keluarga', 'like', "%{$search}%");
                });
            }

            // Wilayah filters
            if (!empty($this->request['dusun'])) {
                $query->where('kk.dusun_id', $this->request['dusun']);
            }
            if (!empty($this->request['rw'])) {
                $query->where('kk.rw_id', $this->request['rw']);
            }
            if (!empty($this->request['rt'])) {
                $query->where('kk.rt_id', $this->request['rt']);
            }

            // Status filter
            $status = $this->request['status'] ?? 'all';
            if ($status === 'aktif') {
                $query->where('kk.status_kk', 'normal');
            } elseif ($status === 'bermasalah') {
                $query->whereIn('kk.status_kk', ['bermasalah', 'bermasalah_sementara']);
            } elseif ($status === 'kosong') {
                $query->where('kk.anggota_aktif', 0);
            }
        }

        return $query->orderBy('kk.updated_at', 'desc')->get();
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

