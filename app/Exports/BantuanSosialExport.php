<?php

namespace App\Exports;

use App\Models\BantuanSosial;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BantuanSosialExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = BantuanSosial::with(['penerima.penduduk']);

        if (isset($this->filters['program']) && $this->filters['program']) {
            $query->where('nama_program', 'like', '%' . $this->filters['program'] . '%');
        }

        if (isset($this->filters['jenis']) && $this->filters['jenis']) {
            $query->where('jenis_bantuan', $this->filters['jenis']);
        }

        if (isset($this->filters['tahun']) && $this->filters['tahun']) {
            $query->whereYear('tanggal_mulai', $this->filters['tahun']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Program',
            'Jenis Bantuan',
            'Deskripsi',
            'Periode',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Total Penerima',
            'Total Nilai Bantuan',
            'Status',
            'Dibuat Pada',
            'Diupdate Pada'
        ];
    }

    public function map($bantuan): array
    {
        static $no = 1;

        return [
            $no++,
            $bantuan->nama_program,
            $bantuan->jenis_bantuan,
            $bantuan->deskripsi,
            $bantuan->periode,
            $bantuan->tanggal_mulai ? $bantuan->tanggal_mulai->format('d/m/Y') : '-',
            $bantuan->tanggal_selesai ? $bantuan->tanggal_selesai->format('d/m/Y') : '-',
            $bantuan->penerima->count(),
            'Rp ' . number_format($bantuan->penerima->sum('nilai_bantuan'), 0, ',', '.'),
            $bantuan->status,
            $bantuan->created_at->format('d/m/Y H:i'),
            $bantuan->updated_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E3F2FD']
                ]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 20,
            'C' => 15,
            'D' => 30,
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 20,
            'J' => 10,
            'K' => 18,
            'L' => 18
        ];
    }
}
