<?php

namespace App\Http\Requests\Pengaduan;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePengaduanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status'    => ['required', 'in:baru,diproses,selesai,ditolak'],
            'prioritas' => ['required', 'in:rendah,sedang,tinggi,darurat'],
            'tanggapan' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'status'    => 'Status Penanganan',
            'prioritas' => 'Tingkat Prioritas',
            'tanggapan' => 'Tanggapan Resmi',
        ];
    }
}
