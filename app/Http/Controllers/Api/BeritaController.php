<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Berita;
use App\Http\Resources\BeritaResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class BeritaController extends Controller
{
    /**
     * Get list of published berita
     */
    public function index(Request $request)
    {
        // Cache key berdasarkan parameter request
        $cacheKey = 'api_berita_index_' . md5(serialize($request->all()));

        return Cache::remember($cacheKey, 15, function () use ($request) {
            $query = Berita::published()->with('author');

            // Filter by category
            if ($request->has('kategori') && $request->kategori) {
                $query->where('kategori', $request->kategori);
            }

            // Search
            if ($request->has('search') && $request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('judul', 'like', "%{$request->search}%")
                      ->orWhere('konten', 'like', "%{$request->search}%");
                });
            }

            // Featured berita
            if ($request->has('featured') && $request->featured) {
                $query->featured();
            }

            $beritas = $query->orderBy('published_at', 'desc')
                            ->paginate($request->get('per_page', 10));

            return response()->json([
                'success' => true,
                'data' => BeritaResource::collection($beritas->items()),
                'pagination' => [
                    'current_page' => $beritas->currentPage(),
                    'last_page' => $beritas->lastPage(),
                    'per_page' => $beritas->perPage(),
                    'total' => $beritas->total()
                ]
            ]);
        });
    }

    /**
     * Get single berita by slug
     */
    public function show($slug)
    {
        // Cache individual berita untuk 1 jam (jarang berubah)
        $cacheKey = "api_berita_show_{$slug}";

        return Cache::remember($cacheKey, 15, function () use ($slug) {
            $berita = Berita::published()
                            ->with('author')
                            ->where('slug', $slug)
                            ->first();

            if (!$berita) {
                return response()->json([
                    'success' => false,
                    'message' => 'Berita tidak ditemukan'
                ], 404);
            }

            // Get related berita
            $relatedBeritas = Berita::published()
                                    ->where('kategori', $berita->kategori)
                                    ->where('id', '!=', $berita->id)
                                    ->limit(3)
                                    ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'berita' => new BeritaResource($berita),
                    'related' => BeritaResource::collection($relatedBeritas)
                ]
            ]);
        });
    }

    /**
     * Get featured berita
     */
    public function featured()
    {
        // Redis cache untuk 10 menit (featured berita jarang berubah)
        return Cache::remember('api_berita_featured', 15, function () {
            $beritas = Berita::published()
                            ->featured()
                            ->select('id', 'judul', 'excerpt', 'gambar', 'slug', 'published_at', 'kategori')
                            ->with(['author:id,name'])
                            ->orderBy('published_at', 'desc')
                            ->limit(5)
                            ->get();

            return response()->json([
                'success' => true,
                'data' => BeritaResource::collection($beritas)
            ]);
        });
    }

    /**
     * Get berita categories
     */
    public function categories()
    {
        // Redis cache untuk 1 jam (categories jarang berubah)
        return Cache::remember('api_berita_categories', 15, function () {
            $categories = Berita::published()
                                ->select('kategori')
                                ->distinct()
                                ->pluck('kategori');

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        });
    }

    /**
     * Get latest berita
     */
    public function latest()
    {
        // Redis cache untuk 5 menit (sering berubah tapi tidak terlalu sering)
        return Cache::remember('api_berita_latest', 15, function () {
            $beritas = Berita::published()
                            ->select('id', 'judul', 'excerpt', 'gambar', 'slug', 'published_at', 'kategori')
                            ->with(['author:id,name'])
                            ->orderBy('published_at', 'desc')
                            ->limit(6)
                            ->get();

            return response()->json([
                'success' => true,
                'data' => BeritaResource::collection($beritas)
            ]);
        });
    }

    /**
     * Search berita
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:3|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Query minimal 3 karakter',
                'errors' => $validator->errors()
            ], 400);
        }

        // Cache search results untuk 30 detik
        $cacheKey = 'api_berita_search_' . md5($request->input('q'));

        return Cache::remember($cacheKey, 15, function () use ($request) {
            $query = $request->input('q');
            $beritas = Berita::published()
                            ->with('author')
                            ->where(function($q) use ($query) {
                                $q->where('judul', 'like', "%{$query}%")
                                  ->orWhere('konten', 'like', "%{$query}%")
                                  ->orWhere('kategori', 'like', "%{$query}%");
                            })
                            ->orderBy('published_at', 'desc')
                            ->paginate($request->get('per_page', 10));

            return response()->json([
                'success' => true,
                'data' => BeritaResource::collection($beritas->items()),
                'pagination' => [
                    'current_page' => $beritas->currentPage(),
                    'last_page' => $beritas->lastPage(),
                    'per_page' => $beritas->perPage(),
                    'total' => $beritas->total()
                ]
            ]);
        });
    }

    /**
     * Get berita by category
     */
    public function getByCategory($category)
    {
        // Cache berita by category untuk 30 detik
        $cacheKey = "api_berita_category_{$category}";

        return Cache::remember($cacheKey, 15, function () use ($category) {
            $beritas = Berita::published()
                            ->with('author')
                            ->where('kategori', $category)
                            ->orderBy('published_at', 'desc')
                            ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => BeritaResource::collection($beritas->items()),
                'pagination' => [
                    'current_page' => $beritas->currentPage(),
                    'last_page' => $beritas->lastPage(),
                    'per_page' => $beritas->perPage(),
                    'total' => $beritas->total()
                ]
            ]);
        });
    }
}

