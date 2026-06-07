<?php

namespace App\Exports;

use App\Models\Penduduk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AllPendudukExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Penduduk::with('kartuKeluarga.rtMaster', 'kartuKeluarga.rwMaster', 'kartuKeluarga.dusunMaster')
            ->orderBy('kartu_keluarga_id')
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
            'Nama Lengkap',
            'No. KK',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Usia',
            'Agama',
            'Status Perkawinan',
            'Kedudukan Keluarga',
            'Kewarganegaraan',
            'Pendidikan',
            'Status Pendidikan',
            'Pekerjaan',
            'Golongan Darah',
            'No. Akta Lahir',
            'Nama Ayah',
            'Nama Ibu',
            'Telepon',
            'Disabilitas / Cacat',
            'Sakit Menahun',
            'Status Asuransi',
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
            $penduduk->nik ? "'" . $penduduk->nik : '',
            strtoupper($penduduk->nama ?: ''),
            $penduduk->nkk ? "'" . $penduduk->nkk : '',
            strtoupper($penduduk->jenis_kelamin_label ?: ''),
            strtoupper($penduduk->tempat_lahir ?: ''),
            $penduduk->tanggal_lahir ? $penduduk->tanggal_lahir->format('d/m/Y') : '',
            $penduduk->usia ?: '',
            strtoupper($penduduk->agama ?: ''),
            strtoupper($penduduk->status_perkawinan ?: '-'),
            strtoupper($penduduk->kedudukan_keluarga ?: '-'),
            strtoupper($penduduk->warganegara ?: '-'),
            strtoupper($penduduk->pendidikan ?: ''),
            strtoupper($penduduk->status_pendidikan ?: '-'),
            strtoupper($penduduk->pekerjaan ?: ''),
            strtoupper($penduduk->golongan_darah ?: '-'),
            $penduduk->no_akta_lahir ?: '-',
            strtoupper($penduduk->nama_ayah ?: ''),
            strtoupper($penduduk->nama_ibu ?: ''),
            $penduduk->telepon ?: '-',
            strtoupper($penduduk->cacat_type ?: '-'),
            strtoupper($penduduk->sakit_menahun ?: '-'),
            strtoupper($penduduk->status_asuransi ?: '-'),
            strtoupper($penduduk->alamat ?: ''),
            $penduduk->rt_label ?: '',
            $penduduk->rw_label ?: '',
            strtoupper($penduduk->dusun_label ?: '-'),
            strtoupper($penduduk->keterangan ?: ''),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFF0F0F0'],
                ]
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // NIK
            'B' => 30, // Nama
            'C' => 20, // No. KK
            'D' => 15, // Jenis Kelamin
            'E' => 20, // Tempat Lahir
            'F' => 15, // Tanggal Lahir
            'G' => 8,  // Usia
            'H' => 15, // Agama
            'I' => 20, // Status Perkawinan
            'J' => 20, // Kedudukan Keluarga
            'K' => 20, // Kewarganegaraan
            'L' => 20, // Pendidikan
            'M' => 20, // Status Pendidikan
            'N' => 25, // Pekerjaan
            'O' => 15, // Gol. Darah
            'P' => 20, // No Akta Lahir
            'Q' => 25, // Nama Ayah
            'R' => 25, // Nama Ibu
            'S' => 20, // Telepon
            'T' => 20, // Cacat
            'U' => 20, // Sakit
            'V' => 20, // Asuransi
            'W' => 30, // Alamat
            'X' => 8,  // RT
            'Y' => 8,  // RW
            'Z' => 20, // Dusun
            'AA'=> 30, // Keterangan
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT, // NIK
            'C' => NumberFormat::FORMAT_TEXT, // No. KK
            'P' => NumberFormat::FORMAT_TEXT, // No. Akta Lahir
            'S' => NumberFormat::FORMAT_TEXT, // Telepon
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Freeze pane di baris pertama, sehingga saat scroll bawah header tetap diam
                $event->sheet->getDelegate()->freezePane('A2');
            },
        ];
    }
}
