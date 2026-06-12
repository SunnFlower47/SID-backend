<?php

namespace App\Imports;

use App\Models\Umkm;
use App\Models\Penduduk;
use App\Traits\WilayahResolver;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UmkmImport implements ToCollection, WithHeadingRow, WithValidation, WithChunkReading
{
    use WilayahResolver;

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                // Cari penduduk berdasarkan NIK
                $penduduk = Penduduk::where('nik', $row['nik_pemilik'])->first();

                if (!$penduduk) {
                    continue; // Skip or handle error
                }

                // Sanitize and Resolve Wilayah
                $wilayah = $this->resolveWilayah(
                    $row['rt'] ?? '001',
                    $row['rw'] ?? '001',
                    $row['dusun'] ?? null
                );

                Umkm::create([
                    'nama_usaha' => $row['nama_usaha'],
                    'nama_pemilik' => $row['nama_pemilik'],
                    'nik_pemilik' => $row['nik_pemilik'],
                    'alamat_usaha' => $row['alamat_usaha'],
                    'rt_id' => $wilayah['rt_id'],
                    'rw_id' => $wilayah['rw_id'],
                    'dusun_id' => $wilayah['dusun_id'],
                    'no_telepon' => $row['telepon'] ?? null,
                    'email' => $row['email'] ?? null,
                    'jenis_usaha' => $row['jenis_usaha'],
                    'deskripsi_usaha' => $row['deskripsi_usaha'],
                    'modal_awal' => $row['modal_awal'] ?? 0,
                    'omset_bulanan' => $row['omset_bulanan'] ?? 0,
                    'jumlah_karyawan' => $row['jumlah_karyawan'] ?? 0,
                    'status_usaha' => $row['status_usaha'] ?? 'aktif',
                    'tanggal_berdiri' => (function($val) {
                        if (empty($val) || $val === '-') return null;
                        if (is_numeric($val)) return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)->format('Y-m-d');
                        if (strpos($val, '/') !== false) {
                            try { return Carbon::createFromFormat('d/m/Y', $val)->format('Y-m-d'); } 
                            catch (\Exception $e) { return null; }
                        }
                        try { return Carbon::parse($val)->format('Y-m-d'); } 
                        catch (\Exception $e) { return null; }
                    })($row['tanggal_berdiri']),
                    'produk_unggulan' => !empty($row['produk_unggulan']) ? explode(',', $row['produk_unggulan']) : [],
                    'is_unggulan' => ($row['unggulan'] ?? '') === 'Ya',
                    'is_verified' => ($row['terverifikasi'] ?? '') === 'Ya',
                ]);
            }
        });
    }

    public function rules(): array
    {
        return [
            'nama_usaha' => 'required|string|max:255',
            'nama_pemilik' => 'required|string|max:255',
            'nik_pemilik' => 'required|string|size:16|exists:penduduks,nik',
            'alamat_usaha' => 'required|string|max:500',
            'rt' => 'required|string|max:10',
            'rw' => 'required|string|max:10',
            'dusun' => 'nullable|string|max:100',
            'telepon' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'jenis_usaha' => 'required|in:makanan,minuman,kerajinan,perdagangan,jasa,pertanian,perikanan,peternakan,lainnya',
            'deskripsi_usaha' => 'nullable|string',
            'modal_awal' => 'nullable|numeric|min:0',
            'omset_bulanan' => 'nullable|numeric|min:0',
            'jumlah_karyawan' => 'nullable|integer|min:0',
            'status_usaha' => 'nullable|in:aktif,nonaktif,tutup,pindah',
            'tanggal_berdiri' => 'nullable',
            'produk_unggulan' => 'nullable|string',
            'unggulan' => 'nullable|in:Ya,Tidak',
            'terverifikasi' => 'nullable|in:Ya,Tidak',
        ];
    }

    public function prepareForValidation($data, $index)
    {
        $stringFields = ['nik_pemilik', 'rt', 'rw', 'telepon', 'dusun'];
        foreach ($stringFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = (string) $data[$field];
            }
        }
        
        if (isset($data['tanggal_berdiri']) && is_numeric($data['tanggal_berdiri'])) {
            $data['tanggal_berdiri'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['tanggal_berdiri'])->format('Y-m-d');
        }

        return $data;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
