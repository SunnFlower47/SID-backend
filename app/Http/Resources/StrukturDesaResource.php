<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StrukturDesaResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'jabatan' => $this->jabatan,
            'kategori' => $this->kategori,
            'email' => $this->email,
            'alamat' => $this->alamat,
            'rt' => $this->rt_label,
            'rw' => $this->rw_label,
            'dusun' => $this->dusun_label,
            'foto' => $this->foto,
            'urutan' => $this->urutan,
        ];
    }
}
