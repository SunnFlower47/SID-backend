<?php

namespace App\Exports;

use App\Models\SuratPengajuan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SuratPengajuanExport implements FromQuery, WithChunkReading, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = SuratPengajuan::with(['penduduk']);

        if (isset($this->filters['jenis_surat']) && $this->filters['jenis_surat']) {
            $query->where('jenis_surat', $this->filters['jenis_surat']);
        }

        if (isset($this->filters['status']) && $this->filters['status']) {
            $query->where('status', $this->filters['status']);
        }

        if (isset($this->filters['tahun']) && $this->filters['tahun']) {
            $query->whereYear('created_at', $this->filters['tahun']);
        }

        if (isset($this->filters['bulan']) && $this->filters['bulan']) {
            $query->whereMonth('created_at', $this->filters['bulan']);
        }

        return $query;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Surat',
            'Jenis Surat',
            'Nama Pemohon',
            'NIK',
            'Alamat',
            'RT/RW',
            'Dusun',
            'Status',
            'Tanggal Pengajuan',
            'Tanggal Selesai',
            'Keterangan',
            'Dibuat Pada'
        ];
    }

    public function map($surat): array
    {
        static $no = 1;

        return [
            $no++,
            $surat->nomor_surat,
            $surat->surat_type,
            $surat->penduduk->nama ?? '-',
            $surat->penduduk->nik ? "'" . $surat->penduduk->nik : '-',
            $surat->penduduk->alamat ?? '-',
            'RT ' . ($surat->penduduk->rt_label ?? '-') . ' / RW ' . ($surat->penduduk->rw_label ?? '-'),
            $surat->penduduk->dusun_label ?? '-',
            $surat->status,
            $surat->tanggal_pengajuan ? (is_string($surat->tanggal_pengajuan) ? \Carbon\Carbon::parse($surat->tanggal_pengajuan)->format('d/m/Y') : $surat->tanggal_pengajuan->format('d/m/Y')) : '-',
            $surat->tanggal_selesai ? (is_string($surat->tanggal_selesai) ? \Carbon\Carbon::parse($surat->tanggal_selesai)->format('d/m/Y') : $surat->tanggal_selesai->format('d/m/Y')) : '-',
            $surat->keterangan ?? '-',
            $surat->created_at ? $surat->created_at->format('d/m/Y H:i') : '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E1F5FE']
                ]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 20,
            'C' => 20,
            'D' => 25,
            'E' => 18,
            'F' => 30,
            'G' => 12,
            'H' => 15,
            'I' => 15,
            'J' => 18,
            'K' => 18,
            'L' => 30,
            'M' => 18
        ];
    }
}
