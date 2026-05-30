<?php

namespace App\Http\Controllers\Tenant\Konten;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Umkm;
use App\Models\Rw;
use Inertia\Inertia;
use App\Http\Requests\Konten\StoreUmkmRequest;
use App\Http\Requests\Konten\UpdateUmkmRequest;
use App\Services\System\FileUploadService;

class UmkmController extends Controller
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
        return Inertia::render('Tenant/Umkm/Index', [
            'umkm' => Inertia::defer(fn() => Umkm::query()
                ->when($request->search, function($query, $search) {
                    $query->where('nama_usaha', 'like', "%{$search}%")
                          ->orWhere('nama_pemilik', 'like', "%{$search}%")
                          ->orWhere('alamat_usaha', 'like', "%{$search}%");
                })
                ->when($request->status, function($query, $status) {
                    $query->where('status_usaha', $status);
                })
                ->when($request->jenis_usaha, function($query, $jenis) {
                    $query->where('jenis_usaha', $jenis);
                })
                ->when($request->is_unggulan, function($query, $is_unggulan) {
                    $query->where('is_unggulan', $is_unggulan === 'true');
                })
                ->with(['rt', 'rw', 'dusun'])
                ->orderBy('created_at', 'desc')
                ->paginate(15)
                ->withQueryString()
            ),
            'stats' => Inertia::defer(fn() => [
                'total' => Umkm::count(),
                'aktif' => Umkm::where('status_usaha', 'aktif')->count(),
                'unggulan' => Umkm::where('is_unggulan', true)->count(),
                'verified' => Umkm::where('is_verified', true)->count(),
            ]),
            'filters' => $request->all(['search', 'status', 'jenis_usaha', 'is_unggulan']),
            'jenisOptions' => $this->getJenisOptions()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Tenant/Umkm/Create', [
            'jenisOptions' => $this->getJenisOptions(),
            'wilayah' => [
                'dusun' => \App\Models\Dusun::all(),
                'rw' => \App\Models\Rw::all(),
                'rt' => \App\Models\Rt::all(),
            ],
            'masterRwOptions' => $this->getMasterRwOptions()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUmkmRequest $request)
    {
        $data = $request->validated();

        // Handle file uploads
        if ($request->hasFile('foto_usaha')) {
            $fotoPaths = [];
            foreach ($request->file('foto_usaha') as $file) {
                $path = $this->fileUploadService->upload($file, 'umkm/fotos');
                if ($path) {
                    $fotoPaths[] = $path;
                }
            }
            $data['foto_usaha'] = $fotoPaths;
        }

        Umkm::create($data);

        return redirect()->route('umkm.index')
            ->with('success', 'Data UMKM berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Umkm $umkm)
    {
        $umkm->load(['rt', 'rw', 'dusun']);
        return Inertia::render('Tenant/Umkm/Show', [
            'umkm' => $umkm
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Umkm $umkm)
    {
        return Inertia::render('Tenant/Umkm/Edit', [
            'umkm' => $umkm,
            'jenisOptions' => $this->getJenisOptions(),
            'wilayah' => [
                'dusun' => \App\Models\Dusun::all(),
                'rw' => \App\Models\Rw::all(),
                'rt' => \App\Models\Rt::all(),
            ],
            'masterRwOptions' => $this->getMasterRwOptions()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUmkmRequest $request, Umkm $umkm)
    {
        $data = $request->validated();

        // Handle file uploads
        if ($request->hasFile('foto_usaha')) {
            // Delete old photos
            if ($umkm->foto_usaha) {
                $oldFotos = is_array($umkm->foto_usaha) ? $umkm->foto_usaha : (is_string($umkm->foto_usaha) ? json_decode($umkm->foto_usaha, true) ?? [$umkm->foto_usaha] : []);
                foreach ($oldFotos as $foto) {
                    $this->fileUploadService->delete($foto);
                }
            }

            $fotoPaths = [];
            foreach ($request->file('foto_usaha') as $file) {
                $path = $this->fileUploadService->upload($file, 'umkm/fotos');
                if ($path) {
                    $fotoPaths[] = $path;
                }
            }
            $data['foto_usaha'] = $fotoPaths;
        } else {
            // Prevent overwriting existing photos with empty array if no new photos are uploaded
            unset($data['foto_usaha']);
        }

        $umkm->update($data);

        return redirect()->route('umkm.index')
            ->with('success', 'Data UMKM berhasil diperbarui!');
    }


    public function destroy(Umkm $umkm)
    {
        // Delete photos
        if ($umkm->foto_usaha) {
            $oldFotos = is_array($umkm->foto_usaha) ? $umkm->foto_usaha : (is_string($umkm->foto_usaha) ? json_decode($umkm->foto_usaha, true) ?? [$umkm->foto_usaha] : []);
            foreach ($oldFotos as $foto) {
                $this->fileUploadService->delete($foto);
            }
        }

        $umkm->delete();

        return redirect()->route('umkm.index')
            ->with('success', 'Data UMKM berhasil dihapus!');
    }

    private function getJenisOptions()
    {
        return [
            ['value' => 'makanan', 'label' => 'Makanan'],
            ['value' => 'minuman', 'label' => 'Minuman'],
            ['value' => 'kerajinan', 'label' => 'Kerajinan'],
            ['value' => 'jasa', 'label' => 'Jasa'],
            ['value' => 'perdagangan', 'label' => 'Perdagangan'],
            ['value' => 'pertanian', 'label' => 'Pertanian'],
            ['value' => 'peternakan', 'label' => 'Peternakan'],
            ['value' => 'lainnya', 'label' => 'Lainnya'],
        ];
    }

    private function getMasterRwOptions()
    {
        return Rw::with('rts')->orderBy('kode')->get()->map(function($rw) {
            return [
                'id' => $rw->id,
                'kode' => $rw->kode,
                'rts' => $rw->rts->map(function($rt) {
                    return [
                        'id' => $rt->id,
                        'kode' => $rt->kode,
                        'dusun' => optional($rt->dusunMaster)->nama,
                        'dusun_id' => $rt->dusun_id
                    ];
                })
            ];
        });
    }
}
