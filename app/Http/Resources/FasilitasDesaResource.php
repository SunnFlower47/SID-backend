<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FasilitasDesaResource extends JsonResource
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
            'jenis' => $this->jenis,
            'alamat' => $this->alamat,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'deskripsi' => $this->deskripsi ?? $this->keterangan,
            'kontak' => $this->kontak,
            'jam_operasional' => $this->jam_operasional,
            'foto' => $this->foto,
            'rt_label' => $this->rt_label,
            'rw_label' => $this->rw_label,
            'dusun_label' => $this->dusun_label,
        ];
    }
}
