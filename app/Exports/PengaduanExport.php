<?php

namespace App\Exports;

use App\Models\Pengaduan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PengaduanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Pengaduan::with(['user']);

        if (isset($this->filters['status']) && $this->filters['status']) {
            $query->where('status', $this->filters['status']);
        }

        if (isset($this->filters['kategori']) && $this->filters['kategori']) {
            $query->where('kategori', $this->filters['kategori']);
        }

        if (isset($this->filters['tahun']) && $this->filters['tahun']) {
            $query->whereYear('created_at', $this->filters['tahun']);
        }

        if (isset($this->filters['bulan']) && $this->filters['bulan']) {
            $query->whereMonth('created_at', $this->filters['bulan']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Pengaduan',
            'Nama Pengadu',
            'NIK',
            'Telepon',
            'Email',
            'Kategori',
            'Judul',
            'Deskripsi',
            'Status',
            'Tanggal Pengaduan',
            'Tanggal Ditanggapi',
            'Tanggapan',
            'Dibuat Pada'
        ];
    }

    public function map($pengaduan): array
    {
        static $no = 1;

        return [
            $no++,
            $pengaduan->id,
            $pengaduan->nama_pelapor,
            $pengaduan->nik_pelapor,
            $pengaduan->telepon ?? '-',
            $pengaduan->email ?? '-',
            $pengaduan->kategori,
            $pengaduan->judul,
            $pengaduan->deskripsi,
            $pengaduan->status,
            $pengaduan->created_at->format('d/m/Y'),
            $pengaduan->tanggal_tanggapan ? $pengaduan->tanggal_tanggapan->format('d/m/Y') : '-',
            $pengaduan->tanggapan ?? '-',
            $pengaduan->created_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF3E0']
                ]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 18,
            'C' => 25,
            'D' => 18,
            'E' => 15,
            'F' => 25,
            'G' => 15,
            'H' => 30,
            'I' => 40,
            'J' => 12,
            'K' => 18,
            'L' => 18,
            'M' => 40,
            'N' => 18
        ];
    }
}
