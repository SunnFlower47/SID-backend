<?php

namespace App\Imports;

use App\Models\Umkm;
use App\Models\Penduduk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Carbon\Carbon;

class UmkmImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading
{
    public function model(array $row)
    {
        // Cari penduduk berdasarkan NIK
        $penduduk = Penduduk::where('nik', $row['nik_pemilik'])->first();

        if (!$penduduk) {
            throw new \Exception("Penduduk dengan NIK {$row['nik_pemilik']} tidak ditemukan");
        }

        return new Umkm([
            'nama_usaha' => $row['nama_usaha'],
            'nama_pemilik' => $row['nama_pemilik'],
            'nik_pemilik' => $row['nik_pemilik'],
            'alamat_usaha' => $row['alamat_usaha'],
            'rt' => $row['rt'],
            'rw' => $row['rw'],
            'dusun' => $row['dusun'],
            'no_telepon' => $row['telepon'] ?? null,
            'email' => $row['email'] ?? null,
            'jenis_usaha' => $row['jenis_usaha'],
            'deskripsi_usaha' => $row['deskripsi_usaha'],
            'modal_awal' => $row['modal_awal'] ?? 0,
            'omset_bulanan' => $row['omset_bulanan'] ?? 0,
            'jumlah_karyawan' => $row['jumlah_karyawan'] ?? 0,
            'status_usaha' => $row['status_usaha'] ?? 'aktif',
            'tanggal_berdiri' => $row['tanggal_berdiri'] ? Carbon::createFromFormat('d/m/Y', $row['tanggal_berdiri']) : null,
            'produk_unggulan' => $row['produk_unggulan'] ? explode(',', $row['produk_unggulan']) : [],
            'is_unggulan' => $row['unggulan'] === 'Ya' ? true : false,
            'is_verified' => $row['terverifikasi'] === 'Ya' ? true : false,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_usaha' => 'required|string|max:255',
            'nama_pemilik' => 'required|string|max:255',
            'nik_pemilik' => 'required|string|size:16|exists:penduduks,nik',
            'alamat_usaha' => 'required|string|max:500',
            'rt' => 'required|string|max:3',
            'rw' => 'required|string|max:3',
            'dusun' => 'required|string|max:100',
            'telepon' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'jenis_usaha' => 'required|in:makanan,kerajinan,perdagangan,jasa,pertanian,perikanan,peternakan,lainnya',
            'deskripsi_usaha' => 'nullable|string',
            'modal_awal' => 'nullable|numeric|min:0',
            'omset_bulanan' => 'nullable|numeric|min:0',
            'jumlah_karyawan' => 'nullable|integer|min:0',
            'status_usaha' => 'nullable|in:aktif,nonaktif',
            'tanggal_berdiri' => 'nullable|date_format:d/m/Y',
            'produk_unggulan' => 'nullable|string',
            'unggulan' => 'nullable|in:Ya,Tidak',
            'terverifikasi' => 'nullable|in:Ya,Tidak',
        ];
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
