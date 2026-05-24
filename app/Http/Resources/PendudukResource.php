<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PendudukResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            // Masking NIK untuk keamanan jika diakses publik
            'nik' => $this->nik ? substr($this->nik, 0, 6) . '******' . substr($this->nik, -4) : null,
            'nama' => $this->nama,
            'jenis_kelamin' => $this->jenis_kelamin,
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir,
            'agama' => $this->agama,
            'pendidikan' => $this->pendidikan,
            'pekerjaan' => $this->pekerjaan,
            'golongan_darah' => $this->golongan_darah,
            'status_perkawinan' => $this->status_perkawinan,
            'status_keluarga' => $this->status_keluarga,
            'kewarganegaraan' => $this->kewarganegaraan,
        ];
    }
}
