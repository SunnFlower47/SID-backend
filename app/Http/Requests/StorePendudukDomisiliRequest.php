<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePendudukDomisiliRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nik'                => ['required', 'digits:16'],
            'nama'               => ['required', 'string', 'max:255'],
            'tempat_lahir'       => ['nullable', 'string', 'max:100'],
            'tanggal_lahir'      => ['nullable', 'date', 'before:today'],
            'jenis_kelamin'      => ['required', Rule::in(['L', 'P', 'LAKI-LAKI', 'PEREMPUAN'])],
            'agama'              => ['nullable', 'string', 'max:50'],
            'status_perkawinan'  => ['nullable', 'string', 'max:50'],
            'kewarganegaraan'    => ['nullable', 'string', 'max:50'],
            'pekerjaan'          => ['nullable', 'string', 'max:100'],
            'asal_daerah'        => ['nullable', 'string', 'max:150'],
            'alamat_asal'        => ['nullable', 'string', 'max:500'],
            'rt_id'              => ['required', 'exists:rts,id'],
            'rw_id'              => ['required', 'exists:rws,id'],
            'dusun_id'           => ['nullable', 'exists:dusuns,id'],
            'alamat_tinggal'     => ['required', 'string', 'max:500'],
            'keperluan_domisili' => ['nullable', 'string', 'max:100'],
            'tanggal_masuk'      => ['required', 'date', 'before_or_equal:today'],
            'catatan'            => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nik.required'           => 'NIK wajib diisi.',
            'nik.digits'             => 'NIK harus tepat 16 digit angka.',
            'nama.required'          => 'Nama wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in'       => 'Jenis kelamin tidak valid.',
            'rt_id.required'         => 'RT wajib dipilih.',
            'rw_id.required'         => 'RW wajib dipilih.',
            'alamat_tinggal.required'=> 'Alamat tinggal di desa wajib diisi.',
            'tanggal_masuk.required' => 'Tanggal masuk wajib diisi.',
            'tanggal_masuk.before_or_equal' => 'Tanggal masuk tidak boleh lebih dari hari ini.',
            'tanggal_lahir.before'   => 'Tanggal lahir harus sebelum hari ini.',
        ];
    }
}
