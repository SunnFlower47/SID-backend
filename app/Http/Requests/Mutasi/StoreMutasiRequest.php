<?php

namespace App\Http\Requests\Mutasi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMutasiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Main check is done via Gate/Middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'jenis_mutasi' => 'required|in:kelahiran,kematian,pindah_masuk,pindah_keluar,pindah_rt_rw,pisah_kk,pembaruan_kk',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];

        $jenisMutasi = $this->input('jenis_mutasi');

        switch ($jenisMutasi) {
            case 'kelahiran':
                $rules += [
                    'nkk' => ['required', 'string', 'size:16'],
                    'nik_bayi' => ['required', 'string', 'size:16', Rule::unique('penduduks', 'nik')],
                    'nama_bayi' => 'required|string|max:255',
                    'jenis_kelamin_bayi' => 'required|in:LAKI-LAKI,PEREMPUAN',
                    'tempat_lahir' => 'required|string|max:255',
                    'tanggal_lahir' => 'required|date',
                    'agama_bayi' => 'required|string|max:50',
                    'status_perkawinan_bayi' => 'required|string|max:50',
                    'kedudukan_keluarga_bayi' => 'required|string|max:50',
                    'pendidikan_bayi' => 'nullable|string|max:100',
                    'pekerjaan_bayi' => 'required|string|max:100',
                    'nama_ayah' => 'required|string|max:255',
                    'nama_ibu' => 'required|string|max:255',
                    'alamat_bayi' => 'required|string|max:500',
                    'rt_id_bayi' => 'required|exists:rts,id',
                    'rw_id_bayi' => 'required|exists:rws,id',
                    'dusun_id_bayi' => 'nullable|exists:dusuns,id',
                    'keterangan_bayi' => 'nullable|string|max:1000',
                    'tanggal_mutasi' => 'required|date',
                ];
                break;

            case 'kematian':
                $rules += [
                    'penduduk_id' => 'required|exists:penduduks,id',
                    'hari_meninggal' => 'required|string|max:20',
                    'tanggal_mutasi' => 'required|date',
                    'jam_meninggal' => 'required|date_format:H:i',
                    'bertempat_di' => 'required|string|max:255',
                    'alasan' => 'required|string|max:500',
                    'hari_pemakaman' => 'required|string|max:20',
                    'tanggal_pemakaman' => 'required|date',
                    'jam_pemakaman' => 'required|date_format:H:i',
                    'lokasi_pemakaman' => 'required|string|max:255',
                    'pelapor_nama' => 'nullable|string|max:255',
                    'pelapor_hubungan' => 'nullable|string|max:100',
                    'pelapor_umur' => 'nullable|integer|min:0|max:150',
                    'pelapor_pekerjaan' => 'nullable|string|max:100',
                    'pelapor_alamat' => 'nullable|string|max:500',
                ];
                break;

            case 'pindah_masuk':
                $rules += [
                    'nik' => [
                        'required',
                        'string',
                        'size:16',
                        Rule::unique('penduduks', 'nik')->whereNull('deleted_at'),
                    ],
                    'nama' => 'required|string|max:255',
                    'jenis_kelamin' => 'required|in:LAKI-LAKI,PEREMPUAN',
                    'agama' => 'required|string|max:50',
                    'status_perkawinan' => 'required|string|max:50',
                    'tempat_lahir' => 'required|string|max:255',
                    'tanggal_lahir' => 'required|date',
                    'kedudukan_keluarga' => 'required|string|max:50',
                    'pendidikan' => 'required|string|max:100',
                    'pekerjaan' => 'nullable|string|max:100',
                    'nama_ayah' => 'nullable|string|max:255',
                    'nama_ibu' => 'nullable|string|max:255',
                    'nkk' => 'nullable|string|size:16',
                    'nkk_new' => 'nullable|string|size:16',
                    'alamat' => 'required|string|max:500',
                    'rt_id' => 'required|exists:rts,id',
                    'rw_id' => 'required|exists:rws,id',
                    'dusun_id' => 'nullable|exists:dusuns,id',
                    'kategori_mutasi' => 'required|in:dalam_desa,dalam_kota,luar_kota,luar_negeri',
                    'asal_tujuan' => 'required|string|max:255',
                    'tanggal_mutasi' => 'required|date',
                    'alasan' => 'nullable|string|max:500',
                    'keterangan' => 'nullable|string|max:1000',
                    'family_members' => 'nullable|array',
                    'family_members.*.nik' => [
                        'required',
                        'string',
                        'size:16',
                        Rule::unique('penduduks', 'nik')->whereNull('deleted_at'),
                    ],
                    'family_members.*.nama' => 'required|string|max:255',
                    'family_members.*.jenis_kelamin' => 'required|in:LAKI-LAKI,PEREMPUAN',
                    'family_members.*.kedudukan_keluarga' => 'required|string|max:50',
                    'family_members.*.tempat_lahir' => 'nullable|string|max:255',
                    'family_members.*.tanggal_lahir' => 'nullable|date',
                ];
                break;

            case 'pindah_keluar':
                $rules += [
                    'penduduk_id' => 'required|exists:penduduks,id',
                    'kategori_mutasi' => 'required|in:dalam_kota,luar_kota,luar_negeri',
                    'asal_tujuan' => 'required|string|max:500',
                    'tanggal_mutasi' => 'required|date',
                    'alasan' => 'nullable|string|max:500',
                    'anggota_pindah' => 'nullable|array',
                    'anggota_pindah.*' => 'integer|exists:penduduks,id',
                ];
                break;

            case 'pindah_rt_rw':
                $rules += [
                    'nkk' => 'required|string|size:16',
                    'rt_id_tujuan' => 'required|exists:rts,id',
                    'rw_id_tujuan' => 'required|exists:rws,id',
                    'dusun_id_tujuan' => 'nullable|exists:dusuns,id',
                    'alamat_tujuan' => 'nullable|string|max:500',
                    'asal_tujuan' => 'nullable|string|max:500',
                    'tanggal_mutasi' => 'required|date',
                ];
                break;

            case 'pisah_kk':
                $rules += [
                    'penduduk_id' => 'required|exists:penduduks,id',
                    'kategori_mutasi' => 'required|in:dalam_desa,dalam_kota,luar_kota,luar_negeri',
                    'kk_option' => 'nullable|in:new,existing',
                    'nkk_existing' => 'nullable|string|size:16',
                    'nkk_existing_id' => 'nullable|string|size:16',
                    'nkk_baru' => 'nullable|string|size:16',
                    'nkk_tujuan' => 'nullable|string|size:16',
                    'alamat' => 'nullable|string|max:500',
                    'rt_id' => 'nullable|exists:rts,id',
                    'rw_id' => 'nullable|exists:rws,id',
                    'dusun_id' => 'nullable|exists:dusuns,id',
                    'kedudukan_keluarga_pisah' => 'required|string|max:50',
                    'status_perkawinan_pisah' => 'nullable|string|max:50',
                    'tanggal_mutasi' => 'required|date',
                    'alasan' => 'nullable|string|max:500',
                    'move_members' => 'nullable|array',
                    'move_members.*' => 'integer|exists:penduduks,id',
                    'anggota_pisah_ids' => 'nullable|array',
                    'anggota_pisah_ids.*' => 'integer|exists:penduduks,id',
                    'anggota_pisah_data' => 'nullable|array',
                    'anggota_pisah_data.*.id' => 'required|integer|exists:penduduks,id',
                    'anggota_pisah_data.*.kedudukan_keluarga' => 'required|string|max:50',
                ];
                break;
        }

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('jenis_mutasi') !== 'pindah_masuk') {
                return;
            }

            $hasExisting = !empty($this->input('nkk'));
            $hasNew = !empty($this->input('nkk_new'));

            if (!$hasExisting && !$hasNew) {
                $validator->errors()->add('nkk', 'Pilih KK existing atau isi NKK baru.');
            }

            if ($hasExisting && $hasNew) {
                $validator->errors()->add('nkk_new', 'Gunakan salah satu: KK existing atau NKK baru.');
            }
        });

        $validator->after(function ($validator) {
            if ($this->input('jenis_mutasi') !== 'pisah_kk') {
                return;
            }

            $kategori = $this->input('kategori_mutasi');
            $kkOption = $this->input('kk_option');

            if ($kategori === 'dalam_desa') {
                if ($kkOption === 'new') {
                    if (empty($this->input('nkk_baru'))) {
                        $validator->errors()->add('nkk_baru', 'NKK baru wajib diisi untuk pisah KK dalam desa (KK baru).');
                    }
                    if (empty($this->input('rt_id')) || empty($this->input('rw_id'))) {
                        $validator->errors()->add('rt_id', 'RT dan RW wajib diisi untuk pisah KK dalam desa (KK baru).');
                    }
                }

                if ($kkOption === 'existing' && empty($this->input('nkk_existing_id')) && empty($this->input('nkk_existing'))) {
                    $validator->errors()->add('nkk_existing', 'Pilih KK existing untuk pisah KK dalam desa.');
                }
            }

            if (in_array($kategori, ['dalam_kota', 'luar_kota', 'luar_negeri'], true)) {
                if (empty($this->input('nkk_tujuan'))) {
                    $validator->errors()->add('nkk_tujuan', 'NKK tujuan wajib diisi untuk pisah KK luar wilayah.');
                }
                if (empty($this->input('alamat'))) {
                    $validator->errors()->add('alamat', 'Alamat tujuan wajib diisi untuk pisah KK luar wilayah.');
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $mergeData = [];

        // Normalize tanggal_lahir if present
        if ($this->has('tanggal_lahir') && !empty($this->tanggal_lahir)) {
            if (strpos($this->tanggal_lahir, '/') !== false) {
                try {
                    $mergeData['tanggal_lahir'] = \Carbon\Carbon::createFromFormat('d/m/Y', $this->tanggal_lahir)->format('Y-m-d');
                } catch (\Exception $e) {
                    // Let validation handle incorrect formats
                }
            }
        }

        // Normalize tanggal_mutasi if present
        if ($this->has('tanggal_mutasi') && !empty($this->tanggal_mutasi)) {
            if (strpos($this->tanggal_mutasi, '/') !== false) {
                try {
                    $mergeData['tanggal_mutasi'] = \Carbon\Carbon::createFromFormat('d/m/Y', $this->tanggal_mutasi)->format('Y-m-d');
                } catch (\Exception $e) {
                    // Let validation handle incorrect formats
                }
            }
        }

        // Normalize tanggal_pemakaman if present
        if ($this->has('tanggal_pemakaman') && !empty($this->tanggal_pemakaman)) {
            if (strpos($this->tanggal_pemakaman, '/') !== false) {
                try {
                    $mergeData['tanggal_pemakaman'] = \Carbon\Carbon::createFromFormat('d/m/Y', $this->tanggal_pemakaman)->format('Y-m-d');
                } catch (\Exception $e) {
                    // Let validation handle incorrect formats
                }
            }
        }

        if (!empty($mergeData)) {
            $this->merge($mergeData);
        }
    }
}

