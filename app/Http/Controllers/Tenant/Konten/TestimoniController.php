<?php

namespace App\Http\Controllers\Tenant\Konten;

use App\Http\Controllers\Controller;
use App\Models\Testimoni;
use App\Models\Rw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Traits\WilayahResolver;
use Inertia\Inertia;

class TestimoniController extends Controller
{
    use WilayahResolver;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('surat.view');

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

        $testimonis = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Get statistics
        $stats = [
            'total' => Testimoni::count(),
            'approved' => Testimoni::where('status', 'approved')->count(),
            'pending' => Testimoni::where('status', 'pending')->count(),
            'rejected' => Testimoni::where('status', 'rejected')->count(),
            'avg_rating' => round(Testimoni::whereNotNull('rating')->avg('rating') ?? 0, 1),
        ];

        return Inertia::render('Tenant/Testimoni/Index', [
            'testimonis' => $testimonis,
            'stats' => $stats,
            'filters' => $request->only(['search', 'status', 'rating']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('surat.view');

        $masterRwOptions = Rw::with('rts')->orderBy('kode')->get()->map(function($rw) {
            return [
                'id' => $rw->id,
                'kode' => $rw->kode,
                'nama' => $rw->nama,
                'rts' => $rw->rts->map(function($rt) {
                    return [
                        'id' => $rt->id,
                        'kode' => $rt->kode,
                        'dusun_id' => $rt->dusun_id,
                        'dusun' => optional($rt->dusun)->nama
                    ];
                })
            ];
        });

        return Inertia::render('Tenant/Testimoni/Create', [
            'masterRwOptions' => $masterRwOptions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('surat.view');

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telepon' => 'nullable|string|max:20',
            'rt_id' => 'required|exists:rts,id',
            'rw_id' => 'required|exists:rws,id',
            'dusun_id' => 'required|exists:dusuns,id',
            'testimoni' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'kategori' => 'nullable|string|max:100',
            'is_anonymous' => 'boolean',
        ]);

        Testimoni::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'telepon' => $request->telepon,
            'rt_id' => $request->rt_id,
            'rw_id' => $request->rw_id,
            'dusun_id' => $request->dusun_id,
            'testimoni' => $request->testimoni,
            'rating' => $request->rating,
            'kategori' => $request->kategori,
            'is_anonymous' => $request->boolean('is_anonymous'),
            'status' => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('testimoni.index')
            ->with('success', 'Testimoni berhasil ditambahkan dan menunggu persetujuan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Testimoni $testimoni)
    {
        Gate::authorize('surat.view');
        return Inertia::render('Tenant/Testimoni/Show', [
            'testimoni' => $testimoni->load(['rt', 'rw', 'dusun'])
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Testimoni $testimoni)
    {
        Gate::authorize('surat.view');

        $masterRwOptions = Rw::with('rts')->orderBy('kode')->get()->map(function($rw) {
            return [
                'id' => $rw->id,
                'kode' => $rw->kode,
                'nama' => $rw->nama,
                'rts' => $rw->rts->map(function($rt) {
                    return [
                        'id' => $rt->id,
                        'kode' => $rt->kode,
                        'dusun_id' => $rt->dusun_id,
                        'dusun' => optional($rt->dusun)->nama
                    ];
                })
            ];
        });

        return Inertia::render('Tenant/Testimoni/Edit', [
            'testimoni' => $testimoni,
            'masterRwOptions' => $masterRwOptions
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Testimoni $testimoni)
    {
        Gate::authorize('surat.view');

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telepon' => 'nullable|string|max:20',
            'rt_id' => 'required|exists:rts,id',
            'rw_id' => 'required|exists:rws,id',
            'dusun_id' => 'required|exists:dusuns,id',
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
            'rt_id' => $request->rt_id,
            'rw_id' => $request->rw_id,
            'dusun_id' => $request->dusun_id,
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
        Gate::authorize('surat.view');
        $testimoni->delete();

        return redirect()->route('testimoni.index')
            ->with('success', 'Testimoni berhasil dihapus.');
    }

    /**
     * Update testimoni status
     */
    public function updateStatus(Request $request, Testimoni $testimoni)
    {
        Gate::authorize('surat.view');

        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $testimoni->update(['status' => $request->status]);

        return back()->with('success', 'Status testimoni berhasil diperbarui.');
    }
}
