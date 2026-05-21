<?php

namespace App\Http\Requests\Konten;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUmkmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if (empty($this->jumlah_karyawan)) {
            $this->merge([
                'jumlah_karyawan' => 0,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'nama_usaha' => 'required|string|max:255',
            'nama_pemilik' => 'required|string|max:255',
            'nik_pemilik' => 'nullable|string|size:16',
            'alamat_usaha' => 'required|string|max:500',
            'rt_id' => 'nullable|exists:rts,id',
            'rw_id' => 'nullable|exists:rws,id',
            'dusun_id' => 'nullable|exists:dusuns,id',
            'no_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'jenis_usaha' => 'required|in:makanan,minuman,kerajinan,jasa,perdagangan,pertanian,peternakan,lainnya',
            'deskripsi_usaha' => 'nullable|string',
            'jumlah_karyawan' => 'required|integer|min:0',
            'status_usaha' => 'required|in:aktif,tutup,pindah',
            'tanggal_berdiri' => 'nullable|date',
            'produk_unggulan' => 'nullable|array',
            'foto_usaha' => 'nullable|array',
            'foto_usaha.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'is_unggulan' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_usaha.required' => 'Nama usaha harus diisi.',
            'nama_pemilik.required' => 'Nama pemilik harus diisi.',
            'alamat_usaha.required' => 'Alamat usaha harus diisi.',
            'jenis_usaha.required' => 'Jenis usaha harus dipilih.',
            'jumlah_karyawan.required' => 'Jumlah karyawan harus diisi.',
            'jumlah_karyawan.integer' => 'Jumlah karyawan harus berupa angka.',
            'jumlah_karyawan.min' => 'Jumlah karyawan minimal 0.',
            'status_usaha.required' => 'Status usaha harus dipilih.',
            'nik_pemilik.size' => 'NIK harus 16 digit.',
            'email.email' => 'Format email tidak valid.',
        ];
    }
}
