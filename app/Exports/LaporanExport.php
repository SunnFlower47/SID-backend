<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $data;
    protected $type;
    protected $startDate;
    protected $endDate;

    public function __construct($data, $type, $startDate, $endDate)
    {
        $this->data = $data;
        $this->type = $type;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        switch ($this->type) {
            case 'penduduk':
                return ['NIK', 'Nama', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Agama', 'Pekerjaan', 'Alamat Lengkap', 'Tanggal Terdaftar'];
            case 'kk':
                return ['No. KK', 'Kepala Keluarga', 'Alamat', 'RT', 'RW', 'Dusun', 'Tanggal Terdaftar'];
            case 'mutasi':
                return ['Nama Penduduk', 'Jenis Mutasi', 'Kategori', 'Tanggal Mutasi', 'Asal/Tujuan', 'Alasan', 'Keterangan'];
            case 'berita':
                return ['Judul', 'Penulis', 'Kategori', 'Status', 'Tanggal Posting', 'Views'];
            case 'surat':
                return ['Nomor Surat', 'NIK Pemohon', 'Nama Pemohon', 'Jenis Surat', 'Status', 'Tanggal Pengajuan', 'Keterangan'];
            default:
                return [];
        }
    }

    public function map($row): array
    {
        switch ($this->type) {
            case 'penduduk':
                return [
                    "'" . $row->nik,
                    $row->nama,
                    $row->jenis_kelamin,
                    $row->tempat_lahir,
                    $row->tanggal_lahir ? $row->tanggal_lahir->format('d/m/Y') : '-',
                    $row->agama,
                    $row->pekerjaan,
                    $row->alamat_lengkap,
                    $row->created_at->format('Y-m-d'),
                ];
            case 'kk':
                return [
                    "'" . $row->nkk,
                    $row->nama_kepala_keluarga,
                    $row->alamat,
                    $row->rt_label,
                    $row->rw_label,
                    $row->dusun_label,
                    $row->created_at->format('Y-m-d'),
                ];
            case 'mutasi':
                return [
                    $row->penduduk->nama ?? 'Deleted User',
                    ucfirst($row->jenis_mutasi),
                    $row->kategori_mutasi,
                    $row->tanggal_mutasi,
                    $row->asal_tujuan,
                    $row->alasan,
                    $row->keterangan ?? '-',
                ];
            case 'berita':
                return [
                    $row->judul,
                    $row->user->name ?? 'Admin',
                    $row->kategori,
                    $row->status,
                    $row->created_at->format('Y-m-d H:i'),
                    $row->views,
                ];
            case 'surat':
                return [
                    $row->nomor_surat,
                    "'" . $row->nik_pengaju,
                    $row->nama_pengaju,
                    $row->jenis_surat,
                    ucfirst($row->status),
                    $row->created_at->format('Y-m-d H:i'),
                    $row->keperluan,
                ];
            default:
                return [];
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
