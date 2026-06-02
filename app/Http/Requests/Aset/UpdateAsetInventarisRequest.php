<?php

namespace App\Http\Requests\Aset;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAsetInventarisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'aset_barang_id'       => ['required', 'exists:aset_barangs,id'],
            'nama_barang_override' => ['required', 'string', 'max:255'],
            'satuan'               => ['required', 'string', 'max:50'],
            'kondisi'              => ['required', 'in:baik,rusak_ringan,rusak_berat'],
            'lokasi'               => ['nullable', 'string', 'max:255'],
            'tanggal_perolehan'    => ['nullable', 'date'],
            'asal_usul'            => ['required', 'in:APBDes,Hibah,Aset Asli Desa,Bantuan Pemerintah,Lainnya'],
            'keterangan'           => ['nullable', 'string'],
            'no_polisi'            => ['nullable', 'string', 'max:100'],
            'no_mesin'             => ['nullable', 'string', 'max:100'],
            'no_rangka'            => ['nullable', 'string', 'max:100'],
            'no_bpkb'              => ['nullable', 'string', 'max:100'],
            'no_sertifikat'        => ['nullable', 'string', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'aset_barang_id'       => 'kode barang',
            'nama_barang_override' => 'nama spesifik aset',
            'satuan'               => 'satuan',
            'kondisi'              => 'kondisi',
            'asal_usul'            => 'asal usul',
            'no_polisi'            => 'nomor polisi',
            'no_mesin'             => 'nomor mesin',
            'no_rangka'            => 'nomor rangka',
            'no_bpkb'              => 'nomor BPKB',
            'no_sertifikat'        => 'nomor sertifikat',
        ];
    }
}
