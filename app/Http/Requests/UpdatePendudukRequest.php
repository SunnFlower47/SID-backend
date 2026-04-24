<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NikValidation;
use App\Rules\DataConsistencyValidation;
use App\Models\Rt;
use App\Rules\RtValidation;

class UpdatePendudukRequest extends FormRequest
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
        $pendudukId = $this->route('penduduk')->id ?? null;

        return [
            'nik' => [
                'required',
                'string',
                'size:16',
                new NikValidation($pendudukId),
                new DataConsistencyValidation($this->all())
            ],
            'nama' => 'required|string|max:255|min:2',
            'jenis_kelamin' => 'required|in:LAKI-LAKI,PEREMPUAN',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => [
                'required',
                'date',
                'before:today',
                'after:1900-01-01',
                new DataConsistencyValidation($this->all())
            ],
            'agama' => 'required|string|max:50',
            'status_perkawinan' => [
                'required',
                'string',
                'max:50',
                new DataConsistencyValidation($this->all())
            ],
            'kedudukan_keluarga' => [
                'required',
                'string',
                'max:50',
                new DataConsistencyValidation($this->all())
            ],
            'pendidikan' => 'nullable|string|max:100',
            'pekerjaan' => 'nullable|string|max:100',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'alamat' => 'required|string|max:500',
            'rt' => [
                'required',
                'string',
                'size:3',
                new RtValidation()
            ],
            'rw' => 'required|string|max:3|regex:/^[0-9]+$/',
            'rw_id' => 'nullable|exists:rws,id',
            'rt_id' => 'nullable|exists:rts,id',
            'dusun' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:500',
            'kartu_keluarga_id' => 'nullable|exists:kartu_keluargas,id',
            'nkk' => 'nullable|string|max:20'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus terdiri dari 16 digit.',
            'nama.required' => 'Nama wajib diisi.',
            'nama.min' => 'Nama minimal 2 karakter.',
            'nama.max' => 'Nama maksimal 255 karakter.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin harus Laki-laki atau Perempuan.',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'tempat_lahir.max' => 'Tempat lahir maksimal 100 karakter.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid.',
            'tanggal_lahir.before' => 'Tanggal lahir tidak boleh di masa depan.',
            'tanggal_lahir.after' => 'Tanggal lahir tidak boleh sebelum tahun 1900.',
            'agama.required' => 'Agama wajib dipilih.',
            'agama.in' => 'Agama tidak valid.',
            'status_perkawinan.required' => 'Status perkawinan wajib dipilih.',
            'status_perkawinan.in' => 'Status perkawinan tidak valid.',
            'kedudukan_keluarga.required' => 'Kedudukan keluarga wajib dipilih.',
            'kedudukan_keluarga.in' => 'Kedudukan keluarga tidak valid.',
            'pendidikan.in' => 'Pendidikan tidak valid.',
            'pekerjaan.max' => 'Pekerjaan maksimal 100 karakter.',
            'nama_ayah.max' => 'Nama ayah maksimal 255 karakter.',
            'nama_ibu.max' => 'Nama ibu maksimal 255 karakter.',
            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.max' => 'Alamat maksimal 500 karakter.',
            'rt.required' => 'RT wajib diisi.',
            'rt.size' => 'RT harus terdiri dari 3 digit angka.',
            'rt.regex' => 'RT harus berupa angka.',
            'rw.required' => 'RW wajib diisi.',
            'rw.regex' => 'RW harus berupa angka.',
            'rw.max' => 'RW maksimal 3 digit.',
            'dusun.max' => 'Dusun maksimal 100 karakter.',
            'keterangan.max' => 'Keterangan maksimal 500 karakter.',
            'kartu_keluarga_id.exists' => 'Kartu keluarga tidak ditemukan.',
            'nkk.max' => 'Nomor KK maksimal 20 karakter.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nik' => 'NIK',
            'nama' => 'Nama',
            'jenis_kelamin' => 'Jenis Kelamin',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'agama' => 'Agama',
            'status_perkawinan' => 'Status Perkawinan',
            'kedudukan_keluarga' => 'Kedudukan Keluarga',
            'pendidikan' => 'Pendidikan',
            'pekerjaan' => 'Pekerjaan',
            'nama_ayah' => 'Nama Ayah',
            'nama_ibu' => 'Nama Ibu',
            'alamat' => 'Alamat',
            'rt' => 'RT',
            'rw' => 'RW',
            'dusun' => 'Dusun',
            'keterangan' => 'Keterangan',
            'kartu_keluarga_id' => 'Kartu Keluarga',
            'nkk' => 'Nomor KK'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $rtId = $this->input('rt_id');
            $rwId = $this->input('rw_id');

            if ($rtId && $rwId) {
                $rt = Rt::find($rtId);
                if (!$rt || (int)$rt->rw_id !== (int)$rwId) {
                    $validator->errors()->add('rt_id', 'RT tidak sesuai dengan RW yang dipilih.');
                }
            }
        });
    }

    protected function prepareForValidation(): void
    {
        // Auto-format NIK (hapus spasi dan karakter non-digit)
        if ($this->has('nik')) {
            $this->merge([
                'nik' => preg_replace('/[^0-9]/', '', $this->nik)
            ]);
        }

        // Auto-format RT/RW (hapus spasi dan karakter non-digit)
        if ($this->has('rt')) {
            $rt = preg_replace('/[^0-9]/', '', $this->rt);
            // Pad with leading zeros to make it 3 digits
            $rt = str_pad($rt, 3, '0', STR_PAD_LEFT);
            $this->merge(['rt' => $rt]);
        }

        if ($this->has('rw')) {
            $this->merge([
                'rw' => preg_replace('/[^0-9]/', '', $this->rw)
            ]);
        }

        // Auto-format nama (trim dan title case)
        if ($this->has('nama')) {
            $this->merge([
                'nama' => ucwords(strtolower(trim($this->nama)))
            ]);
        }

        // Auto-format tempat lahir
        if ($this->has('tempat_lahir')) {
            $this->merge([
                'tempat_lahir' => ucwords(strtolower(trim($this->tempat_lahir)))
            ]);
        }

        // Auto-format alamat
        if ($this->has('alamat')) {
            $this->merge([
                'alamat' => ucwords(strtolower(trim($this->alamat)))
            ]);
        }
    }
}
