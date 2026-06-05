<?php

namespace App\Http\Requests\Sekretariat;

use Illuminate\Foundation\Http\FormRequest;

class BukuAgendaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal'           => ['required', 'date'],
            'jenis_surat'       => ['required', 'in:Masuk,Keluar'],
            'nomor_surat'       => ['nullable', 'string', 'max:255'],
            'tanggal_surat'     => ['required', 'date'],
            'pengirim_penerima' => ['required', 'string', 'max:255'],
            'isi_singkat'       => ['required', 'string'],
            'keterangan'        => ['nullable', 'string', 'max:255'],
        ];
    }
}
