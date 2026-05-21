<?php

namespace App\Http\Requests\Konten;

use Illuminate\Foundation\Http\FormRequest;

class StoreBeritaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB = 5120KB
            'status' => 'required|in:draft,published',
            'kategori' => 'required|string|max:100',
            'excerpt' => 'nullable|string|max:500',
            'featured' => 'boolean'
        ];
    }
}
