<?php

namespace App\Exports;

use App\Models\Umkm;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UmkmExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Umkm::withWilayah();

        if (isset($this->filters['jenis_usaha']) && $this->filters['jenis_usaha']) {
            $query->where('jenis_usaha', $this->filters['jenis_usaha']);
        }

        if (isset($this->filters['status_usaha']) && $this->filters['status_usaha']) {
            $query->where('status_usaha', $this->filters['status_usaha']);
        }

        if (isset($this->filters['is_unggulan']) && $this->filters['is_unggulan'] !== '') {
            $query->where('is_unggulan', $this->filters['is_unggulan']);
        }

        if (isset($this->filters['is_verified']) && $this->filters['is_verified'] !== '') {
            $query->where('is_verified', $this->filters['is_verified']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Usaha',
            'Nama Pemilik',
            'NIK Pemilik',
            'Alamat Usaha',
            'RT/RW',
            'Dusun',
            'Telepon',
            'Email',
            'Jenis Usaha',
            'Deskripsi Usaha',
            'Modal Awal',
            'Omset Bulanan',
            'Jumlah Karyawan',
            'Status Usaha',
            'Tanggal Berdiri',
            'Produk Unggulan',
            'Unggulan',
            'Terverifikasi',
            'Dibuat Pada'
        ];
    }

    public function map($umkm): array
    {
        static $no = 1;

        return [
            $no++,
            $umkm->nama_usaha,
            $umkm->nama_pemilik,
            $umkm->nik_pemilik ? "'" . $umkm->nik_pemilik : '-',
            $umkm->alamat_usaha,
            'RT ' . $umkm->rt_label . ' / RW ' . $umkm->rw_label,
            $umkm->dusun_label,
            $umkm->no_telepon ?? '-',
            $umkm->email ?? '-',
            $umkm->jenis_usaha_label,
            $umkm->deskripsi_usaha,
            'Rp ' . number_format($umkm->modal_awal, 0, ',', '.'),
            'Rp ' . number_format($umkm->omset_bulanan, 0, ',', '.'),
            $umkm->jumlah_karyawan,
            $umkm->status_usaha_label,
            $umkm->tanggal_berdiri ? $umkm->tanggal_berdiri->format('d/m/Y') : '-',
            is_array($umkm->produk_unggulan) ? implode(', ', $umkm->produk_unggulan) : '-',
            $umkm->is_unggulan ? 'Ya' : 'Tidak',
            $umkm->is_verified ? 'Ya' : 'Tidak',
            $umkm->created_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F3E5F5']
                ]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 25,
            'C' => 25,
            'D' => 18,
            'E' => 30,
            'F' => 12,
            'G' => 15,
            'H' => 15,
            'I' => 25,
            'J' => 15,
            'K' => 30,
            'L' => 18,
            'M' => 18,
            'N' => 15,
            'O' => 15,
            'P' => 15,
            'Q' => 25,
            'R' => 10,
            'S' => 12,
            'T' => 18
        ];
    }
}
