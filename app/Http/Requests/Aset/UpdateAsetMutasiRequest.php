<?php

namespace App\Http\Requests\Aset;

use App\Models\AsetInventaris;
use App\Models\AsetMutasi;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAsetMutasiRequest extends FormRequest
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
            // Kita tidak menyarankan mengganti "jenis" karena bisa merusak data surat dll,
            // tapi secara logic form akan mengirim "jenis" yang sama.
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

            /** @var AsetMutasi $mutasi */
            $mutasi = $this->route('mutasi');
            if (!$mutasi) return;

            $inventaris = $mutasi->inventaris;
            if (!$inventaris) return;

            $inventaris->load('mutasis');

            // Hitung saldo hipotetis tanpa mutasi ini
            $saldoTanpaMutasiIni = $inventaris->saldo_kwantitas;
            if ($mutasi->jenis === 'tambah') {
                $saldoTanpaMutasiIni -= $mutasi->kwantitas;
            } else {
                $saldoTanpaMutasiIni += $mutasi->kwantitas;
            }

            if ((float) $this->kwantitas > $saldoTanpaMutasiIni) {
                $validator->errors()->add(
                    'kwantitas',
                    "Kwantitas pengurangan ({$this->kwantitas}) melebihi saldo aset yang tersedia ({$saldoTanpaMutasiIni} {$inventaris->satuan})."
                );
            }
        });
    }
}
