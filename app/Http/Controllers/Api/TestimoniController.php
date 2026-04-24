<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Testimoni;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TestimoniController extends Controller
{
    /**
     * Get testimonials with filtering
     */
    public function index(Request $request): JsonResponse
    {
        // Redis cache untuk 5 menit (testimoni jarang berubah)
        $cacheKey = 'api_testimoni_' . md5(serialize($request->all()));

        return Cache::remember($cacheKey, 300, function () use ($request) {
            try {
                $query = Testimoni::query();

                // Only show approved testimonials for public API
                $query->where('status', 'approved');

                // Filter by rating
                if ($request->has('rating') && $request->rating) {
                    $query->where('rating', $request->rating);
                }

                // Filter by category
                if ($request->has('kategori') && $request->kategori) {
                    $query->where('kategori', $request->kategori);
                }

                // Search - Sanitized input
                if ($request->has('search') && $request->search) {
                    $search = trim($request->search);
                    // Escape special characters for LIKE query
                    $search = str_replace(['%', '_', '\\'], ['\%', '\_', '\\\\'], $search);
                    $query->where(function($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%")
                          ->orWhere('testimoni', 'like', "%{$search}%");
                    });
                }

                // Limit results
                $limit = min($request->get('limit', 10), 50);
                $testimonials = $query->orderBy('created_at', 'desc')
                                     ->limit($limit)
                                     ->get();

                return response()->json([
                    'success' => true,
                    'data' => $testimonials,
                    'total' => $testimonials->count()
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data testimoni',
                    'error' => $e->getMessage()
                ], 500);
            }
        });
    }

    /**
     * Submit new testimonial
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Debug request data - DISABLED untuk production
            // Log::info('Testimoni submission data:', [
            //     'data' => $request->all(),
            //     'headers' => $request->headers->all(),
            //     'ip' => $request->ip(),
            //     'method' => $request->method()
            // ]);

            $request->validate([
                'nama' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'telepon' => 'nullable|string|max:20',
                'testimoni' => 'required|string|min:10',
                'rating' => 'nullable|integer|min:0|max:5',
                'kategori' => 'nullable|string|max:100',
                'is_anonymous' => 'boolean',
            ]);

            $testimoni = Testimoni::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'telepon' => $request->telepon,
                'testimoni' => $request->testimoni,
                'rating' => $request->rating,
                'kategori' => $request->kategori,
                'is_anonymous' => $request->boolean('is_anonymous'),
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Testimoni berhasil dikirim dan menunggu persetujuan',
                'data' => $testimoni
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim testimoni',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get testimonial statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total' => Testimoni::where('status', 'approved')->count(),
                'average_rating' => round(Testimoni::where('status', 'approved')->whereNotNull('rating')->avg('rating'), 2),
                'rating_distribution' => Testimoni::where('status', 'approved')
                    ->whereNotNull('rating')
                    ->selectRaw('rating, COUNT(*) as count')
                    ->groupBy('rating')
                    ->orderBy('rating')
                    ->get()
                    ->pluck('count', 'rating'),
                'category_distribution' => Testimoni::where('status', 'approved')
                    ->whereNotNull('kategori')
                    ->selectRaw('kategori, COUNT(*) as count')
                    ->groupBy('kategori')
                    ->get()
                    ->pluck('count', 'kategori'),
                'recent_count' => Testimoni::where('status', 'approved')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik testimoni',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get testimonial categories
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = Testimoni::where('status', 'approved')
                ->whereNotNull('kategori')
                ->select('kategori')
                ->distinct()
                ->pluck('kategori')
                ->map(function($category) {
                    return [
                        'value' => $category,
                        'label' => ucfirst(str_replace('_', ' ', $category))
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kategori testimoni',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
