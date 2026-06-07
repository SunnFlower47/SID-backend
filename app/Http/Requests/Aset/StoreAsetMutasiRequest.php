<?php

namespace App\Http\Requests\Aset;

use App\Models\AsetInventaris;
use Illuminate\Foundation\Http\FormRequest;

class StoreAsetMutasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tahun'      => ['required', 'integer', 'min:1945', 'max:2100'],
            'semester'   => ['required', 'in:1,2'],
            'tanggal'    => ['required', 'date'],
            'jenis'      => ['required', 'in:tambah,kurang'],
            'kwantitas'     => ['required', 'numeric', 'min:0.01'],
            'nilai'         => ['required', 'numeric', 'min:0'],
            'keterangan'    => ['nullable', 'string', 'max:255'],
            'kondisi'       => ['nullable', 'in:baik,rusak_ringan,rusak_berat'],
            'alasan_kurang' => ['nullable', 'required_if:jenis,kurang', 'in:rusak,dijual,disumbangkan,lainnya'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tahun'     => 'tahun',
            'semester'  => 'semester',
            'tanggal'   => 'tanggal transaksi',
            'jenis'     => 'jenis mutasi',
            'kwantitas' => 'kwantitas',
            'nilai'     => 'nilai',
        ];
    }

    /**
     * Validasi tambahan: mutasi kurang tidak boleh melebihi saldo.
     * Dipanggil setelah rules() lulus.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->jenis !== 'kurang') return;

            /** @var AsetInventaris $inventaris */
            $inventaris = $this->route('inventaris');
            if (!$inventaris) return;

            $inventaris->load('mutasis');

            if ((float) $this->kwantitas > $inventaris->saldo_kwantitas) {
                $validator->errors()->add(
                    'kwantitas',
                    "Kwantitas pengurangan ({$this->kwantitas}) melebihi saldo aset saat ini ({$inventaris->saldo_kwantitas} {$inventaris->satuan})."
                );
            }
        });
    }
}
