<?php

namespace App\Http\Requests\Pengaduan;

use Illuminate\Foundation\Http\FormRequest;

class StorePengaduanRequest extends FormRequest
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
            'nama_pelapor' => ['required', 'string', 'max:255'],
            'nik_pelapor'  => ['nullable', 'string', 'size:16'],
            'telepon'      => ['nullable', 'string', 'max:20'],
            'email'        => ['nullable', 'email', 'max:255'],
            'alamat'       => ['required', 'string', 'max:500'],
            'kategori'     => ['required', 'string', 'in:infrastruktur,keamanan,kebersihan,administrasi,lainnya'],
            'judul'        => ['required', 'string', 'max:255'],
            'deskripsi'    => ['required', 'string'],
            'lokasi'       => ['nullable', 'string', 'max:255'],
            'foto.*'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'prioritas'    => ['required', 'in:rendah,sedang,tinggi,darurat'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama_pelapor' => 'Nama Pelapor',
            'nik_pelapor'  => 'NIK Pelapor',
            'telepon'      => 'Nomor Telepon',
            'email'        => 'Email',
            'alamat'       => 'Alamat Lengkap',
            'kategori'     => 'Kategori Aduan',
            'judul'        => 'Judul Aduan',
            'deskripsi'    => 'Deskripsi Aduan',
            'lokasi'       => 'Detail Lokasi',
            'foto.*'       => 'Foto Bukti',
            'prioritas'    => 'Prioritas',
        ];
    }
}
