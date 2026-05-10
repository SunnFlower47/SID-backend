<?php

namespace App\Http\Controllers\Tenant\Konten;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\StrukturDesa;
use App\Models\MasterJabatan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class StrukturDesaController extends Controller
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'kategori' => 'required|string', // Validasi diperlonggar di backend, dicek di logic
            'nik' => [
                'nullable',
                'string',
                'max:16',
                Rule::unique('struktur_desas', 'nik')
            ],
            'no_hp' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string',
            'rt_id' => 'nullable|exists:rts,id',
            'rw_id' => 'nullable|exists:rws,id',
            'dusun_id' => 'nullable|exists:dusuns,id',
            'tugas_wewenang' => 'nullable|string',
            'tanggal_pengangkatan' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_pengangkatan',
            'status_aktif' => 'boolean',
            'urutan' => 'nullable|integer|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('foto');
        $data['status_aktif'] = $request->boolean('status_aktif');
        $data['urutan'] = $request->urutan ?? 0;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('struktur-desa', 'public');
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
    public function update(Request $request, StrukturDesa $strukturDesa)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'kategori' => 'required|string',
            'nik' => [
                'nullable',
                'string',
                'max:16',
                Rule::unique('struktur_desas', 'nik')->ignore($strukturDesa->id)
            ],
            'no_hp' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string',
            'rt_id' => 'nullable|exists:rts,id',
            'rw_id' => 'nullable|exists:rws,id',
            'dusun_id' => 'nullable|exists:dusuns,id',
            'tugas_wewenang' => 'nullable|string',
            'tanggal_pengangkatan' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_pengangkatan',
            'status_aktif' => 'boolean',
            'urutan' => 'nullable|integer|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('foto');
        $data['status_aktif'] = $request->boolean('status_aktif');
        $data['urutan'] = $request->urutan ?? 0;

        if ($request->hasFile('foto')) {
            if ($strukturDesa->foto) {
                Storage::disk('public')->delete($strukturDesa->foto);
            }
            $data['foto'] = $request->file('foto')->store('struktur-desa', 'public');
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
            Storage::disk('public')->delete($strukturDesa->foto);
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

