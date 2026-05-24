<?php

namespace App\Http\Controllers\Tenant\Konten;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FasilitasDesa;
use Inertia\Inertia;
use App\Http\Requests\Konten\StoreFasilitasDesaRequest;
use App\Http\Requests\Konten\UpdateFasilitasDesaRequest;
use App\Services\System\FileUploadService;

class FasilitasDesaController extends Controller
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
        return Inertia::render('Tenant/FasilitasDesa/Index', [
            'fasilitas' => Inertia::defer(fn() => FasilitasDesa::query()
                ->when($request->search, function($query, $search) {
                    $query->where('nama', 'like', "%{$search}%")
                          ->orWhere('alamat', 'like', "%{$search}%");
                })
                ->when($request->jenis, function($query, $jenis) {
                    $query->where('jenis', $jenis);
                })
                ->when($request->status, function($query, $status) {
                    $query->where('status_aktif', $status === 'aktif');
                })
                ->with(['rt', 'rw', 'dusun'])
                ->orderBy('nama')
                ->paginate(15)
                ->withQueryString()
            ),
            'stats' => Inertia::defer(fn() => [
                'total' => FasilitasDesa::count(),
                'aktif' => FasilitasDesa::where('status_aktif', true)->count(),
                'pendidikan' => FasilitasDesa::where('jenis', 'sekolah')->count(),
                'kesehatan' => FasilitasDesa::whereIn('jenis', ['puskesmas', 'posyandu'])->count(),
                'ibadah' => FasilitasDesa::whereIn('jenis', ['masjid', 'gereja'])->count(),
            ]),
            'filters' => $request->all(['search', 'jenis', 'status']),
            'jenisOptions' => $this->getJenisOptions()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Tenant/FasilitasDesa/Create', [
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
    public function store(StoreFasilitasDesaRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            $uploadPath = $this->fileUploadService->upload($request->file('foto'), 'fasilitas-desa');
            if ($uploadPath) {
                $data['foto'] = $uploadPath;
            }
        }

        FasilitasDesa::create($data);

        return redirect()->route('fasilitas-desa.index')
            ->with('success', 'Fasilitas desa berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FasilitasDesa $fasilitasDesa)
    {
        $fasilitasDesa->load(['rt', 'rw', 'dusun']);
        return Inertia::render('Tenant/FasilitasDesa/Show', [
            'fasilitas' => $fasilitasDesa
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FasilitasDesa $fasilitasDesa)
    {
        return Inertia::render('Tenant/FasilitasDesa/Edit', [
            'fasilitas' => $fasilitasDesa,
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
    public function update(UpdateFasilitasDesaRequest $request, FasilitasDesa $fasilitasDesa)
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            $uploadPath = $this->fileUploadService->replace($request->file('foto'), $fasilitasDesa->foto, 'fasilitas-desa');
            if ($uploadPath) {
                $data['foto'] = $uploadPath;
            }
        }

        $fasilitasDesa->update($data);

        return redirect()->route('fasilitas-desa.index')
            ->with('success', 'Fasilitas desa berhasil diperbarui.');
    }

    public function destroy(FasilitasDesa $fasilitasDesa)
    {
        if ($fasilitasDesa->foto) {
            $this->fileUploadService->delete($fasilitasDesa->foto);
        }

        $fasilitasDesa->delete();

        return redirect()->route('fasilitas-desa.index')
            ->with('success', 'Fasilitas desa berhasil dihapus.');
    }

    private function getJenisOptions()
    {
        return [
            ['value' => 'sekolah', 'label' => 'Sekolah'],
            ['value' => 'posyandu', 'label' => 'Posyandu'],
            ['value' => 'masjid', 'label' => 'Masjid'],
            ['value' => 'gereja', 'label' => 'Gereja'],
            ['value' => 'puskesmas', 'label' => 'Puskesmas'],
            ['value' => 'pos_ronda', 'label' => 'Pos Ronda'],
            ['value' => 'balai_desa', 'label' => 'Balai Desa'],
            ['value' => 'lapangan', 'label' => 'Lapangan'],
            ['value' => 'pasar', 'label' => 'Pasar'],
            ['value' => 'lainnya', 'label' => 'Lainnya'],
        ];
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
