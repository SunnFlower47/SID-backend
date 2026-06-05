<?php

namespace App\Http\Requests\Sekretariat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KeputusanKadesRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $id = $this->route('keputusan_kade');

        return [
            'nomor_keputusan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('keputusan_kades')->ignore($id)
            ],
            'judul_keputusan' => 'required|string|max:255',
            'tanggal_ditetapkan' => 'required|date',
            'keterangan' => 'nullable|string',
            'file_dokumen' => 'nullable|file|mimes:pdf|max:5120', // Maksimal 5MB PDF
        ];
    }
}
