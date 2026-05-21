<?php

namespace App\Http\Requests\Keuangan;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApbdesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Controller checks explicit policy/authorization
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'kode_rekening' => 'required|string|max:50',
            'nama_rekening' => 'required|string|max:255',
            'jenis' => 'required|in:pendapatan,belanja,pembiayaan',
            'sumber_dana' => 'required|string|max:100',
            'anggaran' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:500',
        ];
    }
}
