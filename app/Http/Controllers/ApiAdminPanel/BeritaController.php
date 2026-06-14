<?php

namespace App\Http\Controllers\ApiAdminPanel;

use Illuminate\Http\Request;
use App\Models\Berita;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class BeritaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Berita::query();
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $beritas = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 10));
        
        return response()->json([
            'status' => 'success',
            'data' => $beritas
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'status' => 'required|in:draft,published',
            'kategori' => 'required|string|max:100',
            'featured' => 'boolean'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->judul) . '-' . time();
        $data['author_id'] = Auth::id();

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('berita');
        }

        if ($request->status === 'published') {
            $data['published_at'] = now();
        }

        $berita = Berita::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Berita berhasil dibuat',
            'data' => $berita
        ], 201);
    }

    public function show(Berita $berita): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $berita
        ]);
    }

    public function update(Request $request, Berita $berita): JsonResponse
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'status' => 'required|in:draft,published',
            'kategori' => 'required|string|max:100',
        ]);

        $data = $request->all();
        if ($request->judul !== $berita->judul) {
            $data['slug'] = Str::slug($request->judul) . '-' . time();
        }

        if ($request->hasFile('gambar')) {
            if ($berita->gambar) Storage::disk('s3')->delete($berita->gambar);
            $data['gambar'] = $request->file('gambar')->store('berita');
        }

        $berita->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Berita berhasil diperbarui',
            'data' => $berita
        ]);
    }

    public function destroy(Berita $berita): JsonResponse
    {
        if ($berita->gambar) Storage::disk('s3')->delete($berita->gambar);
        $berita->delete();
        return response()->json(['status' => 'success', 'message' => 'Berita berhasil dihapus']);
    }
}
