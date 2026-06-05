<?php

namespace App\Http\Requests\Sekretariat;

use Illuminate\Foundation\Http\FormRequest;

class PeraturanDesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'jenis_peraturan'    => ['required', 'string', 'max:255'],
            'tahun_anggaran'     => ['required', 'integer', 'min:1900', 'max:2100'],
            'judul'              => ['required', 'string', 'max:255'],
            'nomor_peraturan'    => ['nullable', 'string', 'max:100'],
            'tanggal_ditetapkan' => ['nullable', 'date'],
            'status'             => ['required', 'in:draft,diajukan_bpd,dibahas,disetujui,ditolak'],
            'keterangan_bpd'     => ['nullable', 'string'],
        ];

        if ($this->hasFile('file_dokumen')) {
            $rules['file_dokumen'] = ['nullable', 'file', 'mimes:pdf', 'max:5120'];
        }

        return $rules;
    }
}
