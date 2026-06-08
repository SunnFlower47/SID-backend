<?php

namespace App\Http\Requests\Keuangan;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePengeluaranRequest extends FormRequest
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
        return [
            'nama_pengeluaran'    => 'required|string|max:255',
            'jumlah'              => 'required|numeric|min:0',
            'keterangan'          => 'nullable|string|max:500',
            'tanggal_pengeluaran' => 'required|date',
            'no_bukti'            => 'nullable|string|max:50',
            'jenis_bukti'         => 'nullable|in:kwitansi,nota,spj,transfer,lainnya',
            'file_bukti'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'hapus_file'          => 'nullable|boolean',
            'pajak_ppn'           => 'nullable|numeric|min:0',
            'pajak_pph21'         => 'nullable|numeric|min:0',
            'pajak_pph22'         => 'nullable|numeric|min:0',
            'pajak_pph23'         => 'nullable|numeric|min:0',
        ];
    }
}
