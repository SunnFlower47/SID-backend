<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BeritaResource extends JsonResource
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
            'judul' => $this->judul,
            'slug' => $this->slug,
            'konten' => $this->konten,
            'excerpt' => $this->excerpt,
            'gambar' => $this->image_url, // Menggunakan accessor dari model
            'kategori' => $this->kategori,
            'status' => $this->status,
            'featured' => (bool) $this->featured,
            'author' => $this->author ? [
                'id' => $this->author->id,
                'name' => $this->author->name
            ] : null,
            'published_at' => $this->published_at ? $this->published_at->format('Y-m-d H:i:s') : null,
            // 'created_at' dan 'updated_at' disembunyikan agar payload lebih ringan
        ];
    }
}
