<?php

namespace App\Http\Requests\Konten;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFasilitasDesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'status_aktif' => $this->boolean('status_aktif'),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'jenis' => 'required|in:sekolah,posyandu,masjid,gereja,puskesmas,pos_ronda,balai_desa,lapangan,pasar,lainnya',
            'alamat' => 'required|string',
            'rt_id' => 'nullable|exists:rts,id',
            'rw_id' => 'nullable|exists:rws,id',
            'dusun_id' => 'nullable|exists:dusuns,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'deskripsi' => 'nullable|string',
            'kontak' => 'nullable|string|max:50',
            'jam_operasional' => 'nullable|string|max:100',
            'status_aktif' => 'boolean',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
