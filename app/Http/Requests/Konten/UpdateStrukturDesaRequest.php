<?php

namespace App\Http\Requests\Konten;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStrukturDesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'status_aktif' => $this->boolean('status_aktif'),
            'urutan' => $this->urutan ?? 0,
        ]);
    }

    public function rules(): array
    {
        // Get the model from the route. It could be under 'struktur_desa' or 'strukturDesa' depending on route definition.
        $strukturDesa = $this->route('struktur_desa') ?? $this->route('strukturDesa');
        
        // Ensure we extract the ID if it's a bound model object
        $ignoreId = is_object($strukturDesa) ? $strukturDesa->id : $strukturDesa;

        return [
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'kategori' => 'required|string',
            'nik' => [
                'nullable',
                'string',
                'max:16',
                Rule::unique('struktur_desas', 'nik')->ignore($ignoreId)
            ],
            'no_hp' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string',
            'rt_id' => 'nullable|exists:rts,id',
            'rw_id' => 'nullable|exists:rws,id',
            'dusun_id' => 'nullable|exists:dusuns,id',
            'tugas_wewenang' => 'nullable|string',
            'tanggal_pengangkatan' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_pengangkatan',
            'status_aktif' => 'boolean',
            'urutan' => 'nullable|integer|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
