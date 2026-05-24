<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgendaDesaResource extends JsonResource
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
            'judul' => $this->judul,
            'deskripsi' => $this->konten,
            'tanggal' => $this->published_at ? $this->published_at->format('Y-m-d') : null,
            'waktu' => $this->published_at ? $this->published_at->format('H:i') : null,
            'lokasi' => 'Kantor Desa Cibatu',
            'kategori' => $this->kategori,
            'status' => 'upcoming',
        ];
    }
}
