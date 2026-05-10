<?php

namespace App\Http\Controllers\Tenant\Konten;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\FasilitasDesa;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class FasilitasDesaController extends Controller
{
        public function __construct()
    {
        $this->middleware(['auth', 'can:pelayanan_informasi']);
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'jenis' => 'required|in:sekolah,posyandu,masjid,gereja,puskesmas,pos_ronda,balai_desa,lapangan,pasar,lainnya',
            'alamat' => 'required|string',
            'rt_id' => 'nullable|exists:rts,id',
            'rw_id' => 'nullable|exists:rws,id',
            'dusun_id' => 'nullable|exists:dusuns,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'deskripsi' => 'nullable|string',
            'kontak' => 'nullable|string|max:50',
            'jam_operasional' => 'nullable|string|max:100',
            'status_aktif' => 'boolean',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['status_aktif'] = $request->has('status_aktif');

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('fasilitas-desa', 'public');
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
    public function update(Request $request, FasilitasDesa $fasilitasDesa)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'jenis' => 'required|in:sekolah,posyandu,masjid,gereja,puskesmas,pos_ronda,balai_desa,lapangan,pasar,lainnya',
            'alamat' => 'required|string',
            'rt_id' => 'nullable|exists:rts,id',
            'rw_id' => 'nullable|exists:rws,id',
            'dusun_id' => 'nullable|exists:dusuns,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'deskripsi' => 'nullable|string',
            'kontak' => 'nullable|string|max:50',
            'jam_operasional' => 'nullable|string|max:100',
            'status_aktif' => 'boolean',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['status_aktif'] = $request->has('status_aktif');

        if ($request->hasFile('foto')) {
            // Delete old photo
            if ($fasilitasDesa->foto) {
                Storage::disk('public')->delete($fasilitasDesa->foto);
            }
            $data['foto'] = $request->file('foto')->store('fasilitas-desa', 'public');
        }

        $fasilitasDesa->update($data);

        return redirect()->route('fasilitas-desa.index')
            ->with('success', 'Fasilitas desa berhasil diperbarui.');
    }

    public function destroy(FasilitasDesa $fasilitasDesa)
    {
        if ($fasilitasDesa->foto) {
            Storage::disk('public')->delete($fasilitasDesa->foto);
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
