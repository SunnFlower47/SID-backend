<?php

namespace App\Http\Controllers\Tenant\Konten;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StrukturDesa;
use App\Models\MasterJabatan;
use Inertia\Inertia;
use App\Http\Requests\Konten\StoreStrukturDesaRequest;
use App\Http\Requests\Konten\UpdateStrukturDesaRequest;
use App\Services\System\FileUploadService;

class StrukturDesaController extends Controller
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
        return Inertia::render('Tenant/StrukturDesa/Index', [
            'filters' => $request->all(['search', 'kategori']),
            'struktur' => Inertia::defer(fn() => 
                StrukturDesa::query()
                    ->when($request->search, fn($q) => $q->where('nama', 'like', "%{$request->search}%"))
                    ->when($request->kategori, fn($q) => $q->where('kategori', $request->kategori))
                    ->byHierarchy()
                    ->paginate(20)
                    ->withQueryString()
            ),
            'stats' => Inertia::defer(fn() => [
                'total' => StrukturDesa::count(),
                'aktif' => StrukturDesa::where('status_aktif', true)->count(),
                'kepala_desa' => StrukturDesa::where('kategori', 'kepala_desa')->count(),
                'sekretaris' => StrukturDesa::where('kategori', 'sekretaris')->count(),
                'kepala_dusun' => StrukturDesa::where('kategori', 'kepala_dusun')->count(),
                'ketua_rw' => StrukturDesa::where('kategori', 'ketua_rw')->count(),
                'ketua_rt' => StrukturDesa::where('kategori', 'ketua_rt')->count(),
            ]),
            'kategoriOptions' => MasterJabatan::forStruktur()->get()->map(fn($j) => [
                'value' => $j->slug,
                'label' => $j->nama
            ])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Tenant/StrukturDesa/Create', [
            'kategoriOptions' => MasterJabatan::forStruktur()->get()->map(fn($j) => [
                'value' => $j->slug,
                'label' => $j->nama
            ]),
            'masterRwOptions' => $this->getMasterRwOptions()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStrukturDesaRequest $request)
    {
        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            $uploadPath = $this->fileUploadService->upload($request->file('foto'), 'struktur-desa');
            if ($uploadPath) {
                $data['foto'] = $uploadPath;
            }
        }

        StrukturDesa::create($data);

        return redirect()->route('struktur-desa.index')
            ->with('success', 'Data struktur desa berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StrukturDesa $strukturDesa)
    {
        return Inertia::render('Tenant/StrukturDesa/Show', [
            'strukturDesa' => $strukturDesa->load(['rtMaster', 'rwMaster', 'dusunMaster'])
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StrukturDesa $strukturDesa)
    {
        return Inertia::render('Tenant/StrukturDesa/Edit', [
            'strukturDesa' => $strukturDesa,
            'kategoriOptions' => MasterJabatan::forStruktur()->get()->map(fn($j) => [
                'value' => $j->slug,
                'label' => $j->nama
            ]),
            'masterRwOptions' => $this->getMasterRwOptions()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStrukturDesaRequest $request, StrukturDesa $strukturDesa)
    {
        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            $uploadPath = $this->fileUploadService->replace($request->file('foto'), $strukturDesa->foto, 'struktur-desa');
            if ($uploadPath) {
                $data['foto'] = $uploadPath;
            }
        }

        $strukturDesa->update($data);

        return redirect()->route('struktur-desa.index')
            ->with('success', 'Data struktur desa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StrukturDesa $strukturDesa)
    {
        if ($strukturDesa->foto) {
            $this->fileUploadService->delete($strukturDesa->foto);
        }

        $strukturDesa->delete();

        return redirect()->route('struktur-desa.index')
            ->with('success', 'Data struktur desa berhasil dihapus.');
    }

    private function getMasterRwOptions()
    {
        return \App\Models\Rw::with('rts')->orderBy('kode')->get()->map(function($rw) {
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
    }
}

