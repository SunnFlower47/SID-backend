<?php

namespace App\Exports;

use App\Models\Penduduk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AllPendudukExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Penduduk::with('kartuKeluarga')
            ->orderBy('rt')
            ->orderBy('nkk')
            ->orderByRaw("CASE
                WHEN kedudukan_keluarga = 'Kepala Keluarga' THEN 1
                WHEN kedudukan_keluarga = 'Istri' THEN 2
                WHEN kedudukan_keluarga = 'Anak' THEN 3
                WHEN kedudukan_keluarga = 'Menantu' THEN 4
                WHEN kedudukan_keluarga = 'Cucu' THEN 5
                WHEN kedudukan_keluarga = 'Orang Tua' THEN 6
                WHEN kedudukan_keluarga = 'Famili Lain' THEN 7
                ELSE 8
            END")
            ->orderBy('nama')
            ->get();
    }

    public function headings(): array
    {
        return [
            'NIK',
            'Nama',
            'No. KK',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Usia',
            'Agama',
            'Status Perkawinan',
            'Kedudukan Keluarga',
            'Pendidikan',
            'Pekerjaan',
            'Nama Ayah',
            'Nama Ibu',
            'Alamat',
            'RT',
            'RW',
            'Dusun',
            'Keterangan'
        ];
    }

    public function map($penduduk): array
    {
        return [
            $penduduk->nik ?: '',
            $penduduk->nama ?: '',
            $penduduk->nkk ?: '',
            $penduduk->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
            $penduduk->tempat_lahir ?: '',
            $penduduk->tanggal_lahir ? $penduduk->tanggal_lahir->format('d/m/Y') : '',
            $penduduk->usia ?: '',
            $penduduk->agama ?: '',
            $penduduk->status_perkawinan ?: '-',
            $penduduk->kedudukan_keluarga ?: '-',
            $penduduk->pendidikan ?: '',
            $penduduk->pekerjaan ?: '',
            $penduduk->nama_ayah ?: '',
            $penduduk->nama_ibu ?: '',
            $penduduk->alamat ?: '',
            $penduduk->rt ?: '',
            $penduduk->rw ?: '',
            $penduduk->dusun ?: '-',
            $penduduk->keterangan ?: '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // NIK
            'B' => 25, // Nama
            'C' => 20, // No. KK
            'D' => 15, // Jenis Kelamin
            'E' => 20, // Tempat Lahir
            'F' => 15, // Tanggal Lahir
            'G' => 8,  // Usia
            'H' => 15, // Agama
            'I' => 20, // Status Perkawinan
            'J' => 20, // Kedudukan Keluarga
            'K' => 20, // Pendidikan
            'L' => 25, // Pekerjaan
            'M' => 25, // Nama Ayah
            'N' => 25, // Nama Ibu
            'O' => 30, // Alamat
            'P' => 8,  // RT
            'Q' => 8,  // RW
            'R' => 30, // Keterangan
        ];
    }
}
