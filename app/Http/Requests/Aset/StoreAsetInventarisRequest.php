<?php

namespace App\Http\Requests\Aset;

use Illuminate\Foundation\Http\FormRequest;

class StoreAsetInventarisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ── Data aset permanen ─────────────────────────────────────────
            'aset_barang_id'       => ['required', 'exists:aset_barangs,id'],
            'nama_barang_override' => ['required', 'string', 'max:255'],
            'satuan'               => ['required', 'string', 'max:50'],
            'kondisi'              => ['required', 'in:baik,rusak_ringan,rusak_berat'],
            'lokasi'               => ['nullable', 'string', 'max:255'],
            'tanggal_perolehan'    => ['nullable', 'date'],
            'nup'                  => ['nullable', 'string', 'max:50'],
            'asal_usul'            => ['required', 'in:APBDes,Bantuan Pusat,Bantuan Provinsi,Bantuan Kab/Kota,Sumbangan'],
            'keterangan'           => ['nullable', 'string'],

            // ── Mutasi pertama (perolehan awal) ────────────────────────────
            'tahun'             => ['required', 'integer', 'min:1945', 'max:2100'],
            'semester'          => ['required', 'in:1,2'],
            'tanggal'           => ['required', 'date'],
            'kwantitas'         => ['required', 'numeric', 'min:0.01'],
            'nilai'             => ['required', 'numeric', 'min:0'],
            'keterangan_mutasi' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'aset_barang_id'       => 'kode barang',
            'nama_barang_override' => 'nama spesifik aset',
            'satuan'               => 'satuan',
            'kondisi'              => 'kondisi',
            'nup'                  => 'NUP',
            'asal_usul'            => 'asal usul',
            'tahun'                => 'tahun',
            'semester'             => 'semester',
            'tanggal'              => 'tanggal transaksi',
            'kwantitas'            => 'kwantitas',
            'nilai'                => 'nilai',
        ];
    }
}
