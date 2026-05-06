<?php

namespace App\Http\Requests\BantuanSosial;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePenerimaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'penduduk_id'            => ['required', 'exists:penduduks,id'],
            'sistem_pembayaran'      => ['required', 'in:sekali,berkala'],
            'nilai_diterima'         => ['required_if:sistem_pembayaran,sekali', 'nullable', 'numeric', 'min:0'],
            'nilai_total_berkala'    => ['required_if:sistem_pembayaran,berkala', 'nullable', 'numeric', 'min:0'],
            'tanggal_penerimaan'     => ['required_if:sistem_pembayaran,sekali', 'nullable', 'date'],
            'tanggal_tahap_1'        => ['required_if:sistem_pembayaran,berkala', 'nullable', 'date'],
            'tanggal_tahap_2'        => ['required_if:sistem_pembayaran,berkala', 'nullable', 'date'],
            'tanggal_tahap_3'        => ['required_if:sistem_pembayaran,berkala', 'nullable', 'date'],
            'tanggal_tahap_4'        => ['required_if:sistem_pembayaran,berkala', 'nullable', 'date'],
            'status_penerimaan'      => ['required', 'in:aktif,ditangguhkan,berhenti'],
            'keterangan'             => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'penduduk_id.required'            => 'Penduduk wajib dipilih.',
            'penduduk_id.exists'              => 'Penduduk tidak ditemukan.',
            'sistem_pembayaran.required'      => 'Sistem pembayaran wajib dipilih.',
            'sistem_pembayaran.in'            => 'Sistem pembayaran tidak valid.',
            'nilai_diterima.required_if'      => 'Nilai diterima wajib diisi untuk pembayaran sekali.',
            'nilai_total_berkala.required_if' => 'Nilai total wajib diisi untuk pembayaran berkala.',
            'tanggal_penerimaan.required_if'  => 'Tanggal penerimaan wajib diisi.',
            'tanggal_tahap_1.required_if'     => 'Tanggal tahap 1 wajib diisi.',
            'tanggal_tahap_2.required_if'     => 'Tanggal tahap 2 wajib diisi.',
            'tanggal_tahap_3.required_if'     => 'Tanggal tahap 3 wajib diisi.',
            'tanggal_tahap_4.required_if'     => 'Tanggal tahap 4 wajib diisi.',
            'status_penerimaan.required'      => 'Status penerimaan wajib dipilih.',
        ];
    }
}
