<?php

namespace App\Http\Requests\Keuangan;

use Illuminate\Foundation\Http\FormRequest;

class StoreProyekRequest extends FormRequest
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
            'nama_proyek' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'jenis' => 'required|in:infrastruktur,sosial,ekonomi,lingkungan,lainnya',
            'anggaran' => 'required|numeric|min:0',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'lokasi' => 'required|string|max:255',
            'penanggung_jawab' => 'required|string|max:255',
            'kontraktor' => 'nullable|string|max:255',
            'tahun_anggaran' => 'required|integer|min:2020|max:2030',
            'apbdes_id' => 'required|exists:apbdes,id',
        ];
    }
}
