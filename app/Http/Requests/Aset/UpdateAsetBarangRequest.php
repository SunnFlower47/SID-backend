<?php

namespace App\Http\Requests\Aset;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAsetBarangRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'aset_kategori_id' => 'required|exists:aset_kategoris,id',
            'kode_barang'      => 'required|string|max:20|unique:aset_barangs,kode_barang,' . $this->route('asetBarang')->id,
            'nama_barang'      => 'required|string|max:200',
            'satuan_default'   => 'nullable|string|max:30',
        ];
    }
}
