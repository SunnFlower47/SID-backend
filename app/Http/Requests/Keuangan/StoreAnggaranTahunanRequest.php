<?php

namespace App\Http\Requests\Keuangan;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnggaranTahunanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Middleware handles authorization
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $sumberDanaValues = implode(',', array_keys(\App\Models\Apbdes::SUMBER_DANA));

        return [
            'tahun'          => 'required|integer|min:2020|max:2035',
            'bidang'         => 'required|integer|in:1,2,3,4,5',
            'sub_bidang'     => 'nullable|string|max:10',
            'kegiatan'       => 'nullable|string|max:200',
            'jenis'          => 'required|in:pendapatan,belanja,pembiayaan',
            'sumber_dana'    => 'required|in:' . $sumberDanaValues,
            'kode_rekening'  => 'required|string|max:20',
            'nama_rekening'  => 'required|string|max:255',
            'anggaran'       => 'required|numeric|min:0',
            'keterangan'     => 'nullable|string|max:500',
        ];
    }
}
