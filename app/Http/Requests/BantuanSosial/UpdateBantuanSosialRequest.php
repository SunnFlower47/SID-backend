<?php

namespace App\Http\Requests\BantuanSosial;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBantuanSosialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('pelayanan_informasi');
    }

    public function rules(): array
    {
        return [
            'nama_program'      => ['required', 'string', 'max:255'],
            'jenis_bantuan'     => ['required', 'string', 'in:BLT,PKH,BPNT,Bansos Lainnya'],
            'deskripsi'         => ['required', 'string'],
            'nilai_bantuan'     => ['nullable', 'numeric', 'min:0'],
            'periode'           => ['required', 'string', 'max:50'],
            'tanggal_mulai'     => ['required', 'date'],
            'tanggal_selesai'   => ['required', 'date', 'after:tanggal_mulai'],
            'status'            => ['required', 'in:aktif,selesai,ditangguhkan'],
            'kriteria_penerima' => ['required'],
            'sumber_dana'       => ['required', 'string', 'max:255'],
            'kuota_penerima'    => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_program.required'      => 'Nama program wajib diisi.',
            'jenis_bantuan.required'     => 'Jenis bantuan wajib dipilih.',
            'jenis_bantuan.in'           => 'Jenis bantuan tidak valid.',
            'deskripsi.required'         => 'Deskripsi wajib diisi.',
            'periode.required'           => 'Periode wajib diisi.',
            'tanggal_mulai.required'     => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required'   => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after'      => 'Tanggal selesai harus setelah tanggal mulai.',
            'status.required'            => 'Status wajib dipilih.',
            'status.in'                  => 'Status tidak valid.',
            'kriteria_penerima.required' => 'Kriteria penerima wajib diisi.',
            'sumber_dana.required'       => 'Sumber dana wajib diisi.',
        ];
    }
}
