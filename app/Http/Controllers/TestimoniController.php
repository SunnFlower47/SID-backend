<?php

namespace App\Http\Controllers;

use App\Models\Testimoni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
class TestimoniController extends Controller
{
        /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('testimoni.view');

        $query = Testimoni::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('testimoni', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $testimonis = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get statistics
        $stats = [
            'total' => Testimoni::count(),
            'approved' => Testimoni::where('status', 'approved')->count(),
            'pending' => Testimoni::where('status', 'pending')->count(),
            'rejected' => Testimoni::where('status', 'rejected')->count(),
            'avg_rating' => Testimoni::whereNotNull('rating')->avg('rating') ?? 0,
        ];

        return view('testimoni.index', compact('testimonis', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('testimoni.create');
        return view('testimoni.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('testimoni.create');

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telepon' => 'nullable|string|max:20',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'testimoni' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'kategori' => 'nullable|string|max:100',
            'is_anonymous' => 'boolean',
        ]);

        Testimoni::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'telepon' => $request->telepon,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'testimoni' => $request->testimoni,
            'rating' => $request->rating,
            'kategori' => $request->kategori,
            'is_anonymous' => $request->boolean('is_anonymous'),
            'status' => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Clear relevant caches after creating testimoni
        return redirect()->route('testimoni.index')
            ->with('success', 'Testimoni berhasil ditambahkan dan menunggu persetujuan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Testimoni $testimoni)
    {
        Gate::authorize('testimoni.view');
        return view('testimoni.show', compact('testimoni'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Testimoni $testimoni)
    {
        Gate::authorize('testimoni.update');
        return view('testimoni.edit', compact('testimoni'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Testimoni $testimoni)
    {
        Gate::authorize('testimoni.update');

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telepon' => 'nullable|string|max:20',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'testimoni' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'kategori' => 'nullable|string|max:100',
            'status' => 'required|in:pending,approved,rejected',
            'is_anonymous' => 'boolean',
        ]);

        $testimoni->update([
            'nama' => $request->nama,
            'email' => $request->email,
            'telepon' => $request->telepon,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'testimoni' => $request->testimoni,
            'rating' => $request->rating,
            'kategori' => $request->kategori,
            'status' => $request->status,
            'is_anonymous' => $request->boolean('is_anonymous'),
        ]);

        return redirect()->route('testimoni.index')
            ->with('success', 'Testimoni berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Testimoni $testimoni)
    {
        Gate::authorize('testimoni.delete');
        $testimoni->delete();

        // Clear relevant caches after deleting testimoni
        return redirect()->route('testimoni.index')
            ->with('success', 'Testimoni berhasil dihapus.');
    }

    /**
     * Update testimoni status
     */
    public function updateStatus(Request $request, Testimoni $testimoni)
    {
        Gate::authorize('testimoni.update');

        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $testimoni->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status testimoni berhasil diperbarui.',
            'status' => $testimoni->status
        ]);
    }

    /**
     * Approve testimoni
     */
    public function approve(Testimoni $testimoni)
    {
        Gate::authorize('testimoni.update');

        $testimoni->update(['status' => 'approved']);

        return redirect()->route('testimoni.index')
            ->with('success', 'Testimoni berhasil disetujui.');
    }

    /**
     * Reject testimoni
     */
    public function reject(Testimoni $testimoni)
    {
        Gate::authorize('testimoni.update');

        $testimoni->update(['status' => 'rejected']);

        return redirect()->route('testimoni.index')
            ->with('success', 'Testimoni berhasil ditolak.');
    }
}
