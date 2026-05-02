<?php

namespace App\Http\Controllers\ApiAdminPanel;

use App\Models\Testimoni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\JsonResponse;

class TestimoniController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('pelayanan_informasi');

        $query = Testimoni::query();
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('rating')) $query->where('rating', $request->rating);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q->where('nama', 'like', "%{$search}%")->orWhere('testimoni', 'like', "%{$search}%"));
        }

        $testimonis = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $testimonis,
            'meta' => [
                'stats' => [
                    'total' => Testimoni::count(),
                    'approved' => Testimoni::where('status', 'approved')->count(),
                    'pending' => Testimoni::where('status', 'pending')->count(),
                    'avg_rating' => round(Testimoni::whereNotNull('rating')->avg('rating') ?? 0, 1),
                ]
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        Gate::authorize('pelayanan_informasi');

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'testimoni' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'is_anonymous' => 'boolean',
        ]);

        $data = $request->all();
        $data['status'] = 'pending';
        $data['ip_address'] = $request->ip();

        $testimoni = Testimoni::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Testimoni berhasil dikirim, menunggu moderasi',
            'data' => $testimoni
        ], 201);
    }

    public function updateStatus(Request $request, Testimoni $testimoni): JsonResponse
    {
        Gate::authorize('pelayanan_informasi');
        $request->validate(['status' => 'required|in:pending,approved,rejected']);
        $testimoni->update(['status' => $request->status]);
        return response()->json(['status' => 'success', 'message' => 'Status testimoni diperbarui', 'data' => $testimoni]);
    }

    public function destroy(Testimoni $testimoni): JsonResponse
    {
        Gate::authorize('pelayanan_informasi');
        $testimoni->delete();
        return response()->json(['status' => 'success', 'message' => 'Testimoni dihapus']);
    }
}
