<?php

namespace App\Http\Controllers\Tenant\Pelayanan;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\KontakDesa;
use App\Models\MasterJabatan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
class KontakDesaController extends Controller
{
        public function __construct()
    {
        $this->middleware(['auth', 'can:surat.view']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = KontakDesa::withWilayah();

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        // Filter by jenis
        if ($request->has('jenis') && $request->jenis) {
            $query->where('jenis', $request->jenis);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $status = $request->status === 'aktif';
            $query->where('status_aktif', $status);
        }

        $kontak = $query->byOrder()->paginate(20)->withQueryString();

        $stats = [
            'total' => KontakDesa::count(),
            'aktif' => KontakDesa::where('status_aktif', true)->count(),
            'kantor_desa' => KontakDesa::where('jenis', 'kantor_desa')->count(),
            'kontak_utama' => KontakDesa::whereIn('jenis', ['kantor_desa', 'kepala_desa'])->count(),
        ];

        return \Inertia\Inertia::render('Tenant/KontakDesa/Index', [
            'kontak' => $kontak,
            'stats' => $stats,
            'filters' => $request->all(['search', 'jenis', 'status']),
            'jenisOptions' => MasterJabatan::forKontak()->get()->map(fn($j) => [
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
        return \Inertia\Inertia::render('Tenant/KontakDesa/Create', [
            'jenisOptions' => MasterJabatan::forKontak()->get()->map(fn($j) => [
                'value' => $j->slug,
                'label' => $j->nama
            ]),
            'wilayah' => [
                'dusun' => \App\Models\Dusun::all(),
                'rw' => \App\Models\Rw::all(),
                'rt' => \App\Models\Rt::all(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'jenis' => 'required|in:kantor_desa,kepala_desa,sekretaris,bendahara,kasi_pemerintahan,kasi_kesejahteraan,kasi_pelayanan,kepala_dusun,ketua_rw,ketua_rt,ketua_bumdes,puskesmas,posyandu,sekolah,masjid,lainnya',
            'jabatan' => 'nullable|string|max:255',
            'alamat' => 'required|string',
            'rt_id' => 'nullable|exists:rts,id',
            'rw_id' => 'nullable|exists:rws,id',
            'dusun_id' => 'nullable|exists:dusuns,id',
            'no_telepon' => 'nullable|string|max:15',
            'no_hp' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'whatsapp' => 'nullable|string|max:15',
            'jam_operasional' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'status_aktif' => 'boolean',
            'urutan' => 'nullable|integer|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['status_aktif'] = $request->boolean('status_aktif');
        $data['urutan'] = $request->urutan ?? 0;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('kontak-desa');
        }

        KontakDesa::create($data);

        return redirect()->route('kontak-desa.index')
            ->with('success', 'Data kontak desa berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(KontakDesa $kontakDesa)
    {
        $kontakDesa->load(['rt', 'rw', 'dusun']);
        return \Inertia\Inertia::render('Tenant/KontakDesa/Show', [
            'kontak' => $kontakDesa
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KontakDesa $kontakDesa)
    {
        return \Inertia\Inertia::render('Tenant/KontakDesa/Edit', [
            'kontak' => $kontakDesa,
            'jenisOptions' => MasterJabatan::forKontak()->get()->map(fn($j) => [
                'value' => $j->slug,
                'label' => $j->nama
            ]),
            'wilayah' => [
                'dusun' => \App\Models\Dusun::all(),
                'rw' => \App\Models\Rw::all(),
                'rt' => \App\Models\Rt::all(),
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KontakDesa $kontakDesa)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'jenis' => 'required|in:kantor_desa,kepala_desa,sekretaris,bendahara,kasi_pemerintahan,kasi_kesejahteraan,kasi_pelayanan,kepala_dusun,ketua_rw,ketua_rt,ketua_bumdes,puskesmas,posyandu,sekolah,masjid,lainnya',
            'jabatan' => 'nullable|string|max:255',
            'alamat' => 'required|string',
            'rt_id' => 'nullable|exists:rts,id',
            'rw_id' => 'nullable|exists:rws,id',
            'dusun_id' => 'nullable|exists:dusuns,id',
            'no_telepon' => 'nullable|string|max:15',
            'no_hp' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'whatsapp' => 'nullable|string|max:15',
            'jam_operasional' => 'nullable|string',
            'deskripsi' => 'nullable|string',
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
            // Delete old photo
            if ($kontakDesa->foto) {
                Storage::disk('s3')->delete($kontakDesa->foto);
            }
            $data['foto'] = $request->file('foto')->store('kontak-desa');
        } else {
            unset($data['foto']);
        }

        $kontakDesa->update($data);

        // Clear relevant caches after updating
        return redirect()->route('kontak-desa.index')
            ->with('success', 'Data kontak desa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KontakDesa $kontakDesa)
    {
        if ($kontakDesa->foto) {
            Storage::disk('s3')->delete($kontakDesa->foto);
        }

        $kontakDesa->delete();

        // Clear relevant caches after deleting
        return redirect()->route('kontak-desa.index')
            ->with('success', 'Data kontak desa berhasil dihapus.');
    }
}
