<?php

namespace App\Http\Requests\Kependudukan;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NikValidation;
use App\Rules\DataConsistencyValidation;
use App\Models\Rt;
use App\Rules\RtValidation;

class StorePendudukRequest extends FormRequest
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
            'nik' => [
                'required',
                'string',
                'size:16',
                new NikValidation(),
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
            'alamat' => 'required_if:kk_option,manual|nullable|string|max:500',
            'rt_id' => 'required_if:kk_option,manual|nullable|exists:rts,id',
            'rw_id' => 'required_if:kk_option,manual|nullable|exists:rws,id',
            'dusun_id' => 'nullable|exists:dusuns,id',
            'keterangan' => 'nullable|string|max:500',
            'kk_option' => 'required|in:existing,manual',
            'nkk_existing' => 'nullable|string|size:16|required_if:kk_option,existing',
            'nkk' => 'nullable|string|size:16|required_if:kk_option,manual',
            'golongan_darah' => 'nullable|string|max:20',
            'dapat_membaca_huruf' => 'nullable|string|max:25',
            'warganegara' => 'nullable|string|max:10',
            'no_akta_lahir' => 'nullable|string|max:50',
            'status_pendidikan' => 'nullable|string|max:50',
            'telepon' => 'nullable|string|max:20',
            'cacat_type' => 'nullable|string|max:50',
            'sakit_menahun' => 'nullable|string|max:100',
            'status_asuransi' => 'nullable|string|max:50',
            'family_members' => 'nullable|array',
            'family_members.*.nik' => 'required|string|size:16',
            'family_members.*.nama' => 'required|string|max:255|min:2',
            'family_members.*.jenis_kelamin' => 'required|in:LAKI-LAKI,PEREMPUAN',
            'family_members.*.tempat_lahir' => 'required|string|max:100',
            'family_members.*.tanggal_lahir' => 'nullable|date|before:today|after:1900-01-01',
            'family_members.*.agama' => 'required|string|max:50',
            'family_members.*.kedudukan_keluarga' => 'required|string|max:50',
            'family_members.*.status_perkawinan' => 'nullable|string|max:50',
            'family_members.*.pendidikan' => 'required|string|max:100',
            'family_members.*.pekerjaan' => 'required|string|max:100',
            'family_members.*.nama_ayah' => 'nullable|string|max:255',
            'family_members.*.nama_ibu' => 'nullable|string|max:255',
            'family_members.*.alamat' => 'nullable|string|max:500',
            'family_members.*.rt_id' => 'nullable|exists:rts,id',
            'family_members.*.rw_id' => 'nullable|exists:rws,id',
            'family_members.*.dusun_id' => 'nullable|exists:dusuns,id',
            'family_members.*.golongan_darah' => 'nullable|string|max:20',
            'family_members.*.dapat_membaca_huruf' => 'nullable|string|max:25',
            'family_members.*.warganegara' => 'nullable|string|max:10',
            'family_members.*.no_akta_lahir' => 'nullable|string|max:50',
            'family_members.*.status_pendidikan' => 'nullable|string|max:50',
            'family_members.*.telepon' => 'nullable|string|max:20',
            'family_members.*.cacat_type' => 'nullable|string|max:50',
            'family_members.*.sakit_menahun' => 'nullable|string|max:100',
            'family_members.*.status_asuransi' => 'nullable|string|max:50'
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
            'alamat.required_if' => 'Alamat wajib diisi untuk keluarga baru.',
            'alamat.max' => 'Alamat maksimal 500 karakter.',
            'rt_id.required_if' => 'RT wajib dipilih untuk keluarga baru.',
            'rw_id.required_if' => 'RW wajib dipilih untuk keluarga baru.',
            'dusun.max' => 'Dusun maksimal 100 karakter.',
            'keterangan.max' => 'Keterangan maksimal 500 karakter.',
            'kk_option.required' => 'Pilihan KK wajib dipilih.',
            'kk_option.in' => 'Pilihan KK tidak valid.',
            'nkk_existing.required_if' => 'No KK wajib dipilih jika menggunakan KK yang sudah ada.',
            'nkk.size' => 'Nomor KK harus 16 digit.',
            'nkk.required_if' => 'Nomor KK wajib diisi jika input manual.',
            'family_members.array' => 'Data anggota keluarga harus berupa array.',
            'family_members.*.nik.required' => 'NIK anggota keluarga harus diisi.',
            'family_members.*.nik.size' => 'NIK anggota keluarga harus 16 digit.',
            'family_members.*.nama.required' => 'Nama anggota keluarga harus diisi.',
            'family_members.*.nama.min' => 'Nama anggota keluarga minimal 2 karakter.',
            'family_members.*.nama.max' => 'Nama anggota keluarga maksimal 255 karakter.',
            'family_members.*.jenis_kelamin.required' => 'Jenis kelamin anggota keluarga harus diisi.',
            'family_members.*.jenis_kelamin.in' => 'Jenis kelamin anggota keluarga harus Laki-laki atau Perempuan.',
            'family_members.*.tempat_lahir.required' => 'Tempat lahir anggota keluarga harus diisi.',
            'family_members.*.tempat_lahir.max' => 'Tempat lahir anggota keluarga maksimal 100 karakter.',
            'family_members.*.tanggal_lahir.date' => 'Format tanggal lahir anggota keluarga tidak valid.',
            'family_members.*.tanggal_lahir.before' => 'Tanggal lahir anggota keluarga tidak boleh di masa depan.',
            'family_members.*.tanggal_lahir.after' => 'Tanggal lahir anggota keluarga tidak boleh sebelum tahun 1900.',
            'family_members.*.agama.required' => 'Agama anggota keluarga harus diisi.',
            'family_members.*.kedudukan_keluarga.required' => 'Kedudukan keluarga anggota harus diisi.',
            'family_members.*.kedudukan_keluarga.in' => 'Kedudukan keluarga anggota tidak valid.',
            'family_members.*.status_perkawinan.in' => 'Status perkawinan anggota keluarga tidak valid.',
            'family_members.*.pendidikan.required' => 'Pendidikan anggota keluarga harus diisi.',
            'family_members.*.pekerjaan.required' => 'Pekerjaan anggota keluarga harus diisi.',
            'family_members.*.pekerjaan.max' => 'Pekerjaan anggota keluarga maksimal 100 karakter.',
            'family_members.*.nama_ayah.max' => 'Nama ayah anggota keluarga maksimal 255 karakter.',
            'family_members.*.nama_ibu.max' => 'Nama ibu anggota keluarga maksimal 255 karakter.'
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
            'rt_id' => 'RT',
            'rw_id' => 'RW',
            'dusun' => 'Dusun',
            'keterangan' => 'Keterangan',
            'kk_option' => 'Pilihan KK',
            'nkk_existing' => 'No KK yang Dipilih',
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

            if ($this->input('kk_option') === 'manual' && $rtId && $rwId) {
                $rt = Rt::find($rtId);
                if (!$rt || (int)$rt->rw_id !== (int)$rwId) {
                    $validator->errors()->add('rt_id', 'RT tidak sesuai dengan RW yang dipilih.');
                }
            }
        });
    }

    protected function prepareForValidation(): void
    {
        // Auto-format NIK
        if ($this->has('nik')) {
            $this->merge([
                'nik' => preg_replace('/[^0-9]/', '', $this->nik)
            ]);
        }

        // Auto-format nama
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

        // Auto-format tanggal_lahir
        if ($this->has('tanggal_lahir') && !empty($this->tanggal_lahir)) {
            try {
                if (strpos($this->tanggal_lahir, '/') !== false) {
                    $this->merge([
                        'tanggal_lahir' => \Carbon\Carbon::createFromFormat('d/m/Y', $this->tanggal_lahir)->format('Y-m-d')
                    ]);
                } else {
                    $this->merge([
                        'tanggal_lahir' => \Carbon\Carbon::parse($this->tanggal_lahir)->format('Y-m-d')
                    ]);
                }
            } catch (\Exception $e) {
                // Ignore
            }
        }

        // Auto-format family members
        if ($this->has('family_members') && is_array($this->family_members)) {
            $formattedMembers = [];
            foreach ($this->family_members as $index => $member) {
                $formattedMember = $member;
                if (isset($member['nik'])) $formattedMember['nik'] = preg_replace('/[^0-9]/', '', $member['nik']);
                if (isset($member['nama'])) $formattedMember['nama'] = ucwords(strtolower(trim($member['nama'])));
                if (isset($member['tempat_lahir'])) $formattedMember['tempat_lahir'] = ucwords(strtolower(trim($member['tempat_lahir'])));
                $formattedMembers[$index] = $formattedMember;
            }
            $this->merge(['family_members' => $formattedMembers]);
        }
    }
}
