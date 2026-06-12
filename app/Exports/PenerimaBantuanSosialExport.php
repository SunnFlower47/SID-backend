<?php

namespace App\Exports;

use App\Models\PenerimaBantuanSosial;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenerimaBantuanSosialExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = PenerimaBantuanSosial::with(['penduduk', 'bantuanSosial']);

        if (isset($this->filters['program']) && $this->filters['program']) {
            $query->whereHas('bantuanSosial', function($q) {
                $q->where('nama_program', 'like', '%' . $this->filters['program'] . '%');
            });
        }

        if (isset($this->filters['tahun']) && $this->filters['tahun']) {
            $query->whereYear('tanggal_penerimaan', $this->filters['tahun']);
        }

        if (isset($this->filters['dusun']) && $this->filters['dusun']) {
            $query->whereHas('penduduk.kartuKeluarga', function($q) {
                $q->where('dusun_id', $this->filters['dusun']);
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'NIK',
            'Nama Lengkap',
            'Alamat',
            'RT/RW',
            'Dusun',
            'Program Bantuan',
            'Jenis Bantuan',
            'Nomor Kartu',
            'Nilai Bantuan',
            'Tanggal Penerimaan',
            'Status',
            'Keterangan'
        ];
    }

    public function map($penerima): array
    {
        static $no = 1;

        return [
            $no++,
            $penerima->penduduk->nik ? "'" . $penerima->penduduk->nik : '-',
            $penerima->penduduk->nama ?? '-',
            $penerima->penduduk->alamat ?? '-',
            'RT ' . ($penerima->penduduk->rt_label ?? '-') . ' / RW ' . ($penerima->penduduk->rw_label ?? '-'),
            $penerima->penduduk->dusun_label ?? '-',
            $penerima->bantuanSosial->nama_program ?? '-',
            $penerima->bantuanSosial->jenis_bantuan ?? '-',
            $penerima->nomor_kartu ?? '-',
            'Rp ' . number_format($penerima->nilai_diterima, 0, ',', '.'),
            $penerima->tanggal_penerimaan ? $penerima->tanggal_penerimaan->format('d/m/Y') : '-',
            $penerima->status_penerimaan,
            $penerima->keterangan ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8F5E8']
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
            'D' => 30,
            'E' => 12,
            'F' => 15,
            'G' => 20,
            'H' => 15,
            'I' => 15,
            'J' => 18,
            'K' => 18,
            'L' => 10,
            'M' => 25
        ];
    }
}
