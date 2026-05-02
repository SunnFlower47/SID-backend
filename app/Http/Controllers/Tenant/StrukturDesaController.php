<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\StrukturDesa;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
class StrukturDesaController extends Controller
{
        public function __construct()
    {
        $this->middleware(['auth', 'can:pelayanan_informasi']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $struktur = StrukturDesa::byHierarchy()->paginate(20);

        $stats = [
            'total' => StrukturDesa::count(),
            'aktif' => StrukturDesa::where('status_aktif', true)->count(),
            'kepala_desa' => StrukturDesa::where('kategori', 'kepala_desa')->count(),
            'sekretaris' => StrukturDesa::where('kategori', 'sekretaris')->count(),
            'kepala_dusun' => StrukturDesa::where('kategori', 'kepala_dusun')->count(),
            'ketua_rw' => StrukturDesa::where('kategori', 'ketua_rw')->count(),
            'ketua_rt' => StrukturDesa::where('kategori', 'ketua_rt')->count(),
        ];

        // Group by category for display
        $strukturByCategory = StrukturDesa::aktif()
            ->byHierarchy()
            ->get()
            ->groupBy('kategori');

        return view('struktur-desa.index', compact('struktur', 'stats', 'strukturByCategory'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategoriOptions = [
            'kepala_desa' => 'Kepala Desa',
            'sekretaris' => 'Sekretaris Desa',
            'bendahara' => 'Bendahara Desa',
            'kasi_pemerintahan' => 'Kasi Pemerintahan',
            'kasi_kesejahteraan' => 'Kasi Kesejahteraan',
            'kasi_pelayanan' => 'Kasi Pelayanan',
            'kepala_dusun' => 'Kepala Dusun',
            'ketua_rw' => 'Ketua RW',
            'ketua_rt' => 'Ketua RT',
            'ketua_bumdes' => 'Ketua BUMDes',
            'staf_kaur' => 'Staf KAUR',
            'lainnya' => 'Lainnya',
        ];

        $masterRwOptions = \App\Models\Rw::with('rts')->orderBy('kode')->get()->map(function($rw) {
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

        return view('struktur-desa.create', compact('kategoriOptions', 'masterRwOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'kategori' => 'required|in:kepala_desa,sekretaris,bendahara,kasi_pemerintahan,kasi_kesejahteraan,kasi_pelayanan,kepala_dusun,ketua_rw,ketua_rt,ketua_bumdes,staf_kaur,lainnya',
            'nik' => 'nullable|string|max:16',
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

        $data = $request->all();
        $data['status_aktif'] = $request->has('status_aktif');
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
        return view('struktur-desa.show', compact('strukturDesa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StrukturDesa $strukturDesa)
    {
        $kategoriOptions = [
            'kepala_desa' => 'Kepala Desa',
            'sekretaris' => 'Sekretaris Desa',
            'bendahara' => 'Bendahara Desa',
            'kasi_pemerintahan' => 'Kasi Pemerintahan',
            'kasi_kesejahteraan' => 'Kasi Kesejahteraan',
            'kasi_pelayanan' => 'Kasi Pelayanan',
            'kepala_dusun' => 'Kepala Dusun',
            'ketua_rw' => 'Ketua RW',
            'ketua_rt' => 'Ketua RT',
            'ketua_bumdes' => 'Ketua BUMDes',
            'staf_kaur' => 'Staf KAUR',
            'lainnya' => 'Lainnya',
        ];

        $masterRwOptions = \App\Models\Rw::with('rts')->orderBy('kode')->get()->map(function($rw) {
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

        return view('struktur-desa.edit', compact('strukturDesa', 'kategoriOptions', 'masterRwOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StrukturDesa $strukturDesa)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'kategori' => 'required|in:kepala_desa,sekretaris,bendahara,kasi_pemerintahan,kasi_kesejahteraan,kasi_pelayanan,kepala_dusun,ketua_rw,ketua_rt,ketua_bumdes,staf_kaur,lainnya',
            'nik' => 'nullable|string|max:16',
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

        $data = $request->all();
        $data['status_aktif'] = $request->has('status_aktif');
        $data['urutan'] = $request->urutan ?? 0;

        if ($request->hasFile('foto')) {
            // Delete old photo
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
}
