<?php

namespace App\Http\Controllers\Tenant\Konten;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Berita;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Inertia\Inertia;
use App\Http\Requests\Konten\StoreBeritaRequest;
use App\Http\Requests\Konten\UpdateBeritaRequest;
use App\Services\System\FileUploadService;

class BeritaController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->middleware(['auth', 'can:surat.view']);
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Inertia::render('Tenant/Berita/Index', [
            'berita' => Inertia::defer(fn() => Berita::with('author')
                ->when($request->search, function($query, $search) {
                    $query->where('judul', 'like', "%{$search}%")
                          ->orWhere('konten', 'like', "%{$search}%");
                })
                ->when($request->kategori, function($query, $kategori) {
                    $query->where('kategori', $kategori);
                })
                ->when($request->status, function($query, $status) {
                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString()
            ),
            'stats' => Inertia::defer(fn() => [
                'total' => Berita::count(),
                'published' => Berita::where('status', 'published')->count(),
                'draft' => Berita::where('status', 'draft')->count(),
                'categories_count' => Berita::distinct('kategori')->count('kategori'),
            ]),
            'filters' => $request->all(['search', 'kategori', 'status'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Tenant/Berita/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBeritaRequest $request)
    {
        try {
            $data = $request->validated();

            // Generate unique slug
            $baseSlug = Str::slug($request->judul);
            $slug = $baseSlug;
            $counter = 1;
            while (Berita::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $data['slug'] = $slug;

            $data['author_id'] = Auth::user()->id;
            $data['featured'] = $request->has('featured');

            if ($request->hasFile('gambar')) {
                $uploadPath = $this->fileUploadService->upload($request->file('gambar'), 'berita');
                if (!$uploadPath) {
                    return redirect()->back()
                        ->withErrors(['gambar' => 'Gagal mengupload gambar.'])
                        ->withInput();
                }
                $data['gambar'] = $uploadPath;
            }

            if ($request->status === 'published') {
                $data['published_at'] = now();
            }

            Berita::create($data);

            // Clear relevant caches after creating berita
            return redirect()->route('berita.index')
                ->with('success', 'Berita berhasil dibuat');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle duplicate slug error specifically
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'beritas_slug_unique') !== false) {
                return redirect()->back()
                    ->withErrors(['judul' => 'Judul berita sudah ada. Silakan gunakan judul yang berbeda.'])
                    ->withInput();
            }

            Log::error('Database error creating berita: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->except(['gambar']),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan database saat membuat berita. Silakan coba lagi.'])
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating berita: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->except(['gambar']),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat membuat berita. Silakan coba lagi.'])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Berita $berita)
    {
        $berita->load('author');
        return Inertia::render('Tenant/Berita/Show', [
            'berita' => $berita
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Berita $berita)
    {
        $berita->load('author');
        return Inertia::render('Tenant/Berita/Edit', [
            'berita' => $berita
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBeritaRequest $request, Berita $berita)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($request->judul);
        $data['featured'] = $request->has('featured');

        if ($request->hasFile('gambar')) {
            $uploadPath = $this->fileUploadService->replace($request->file('gambar'), $berita->gambar, 'berita');
            if ($uploadPath) {
                $data['gambar'] = $uploadPath;
            }
        }

        if ($request->status === 'published' && !$berita->published_at) {
            $data['published_at'] = now();
        }

        $berita->update($data);

        // Clear relevant caches after updating berita
        return redirect()->route('berita.index')
            ->with('success', 'Berita berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Berita $berita)
    {
        if ($berita->gambar) {
            $this->fileUploadService->delete($berita->gambar);
        }

        $berita->delete();

        // Clear relevant caches after deleting berita
        return redirect()->route('berita.index')
            ->with('success', 'Berita berhasil dihapus');
    }
}
