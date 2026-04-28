<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KontakDesa;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
class KontakDesaController extends Controller
{
        public function __construct()
    {
        $this->middleware(['auth', 'can:kontak-desa.view']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kontak = KontakDesa::byOrder()->paginate(20);

        $stats = [
            'total' => KontakDesa::count(),
            'aktif' => KontakDesa::where('status_aktif', true)->count(),
            'kantor_desa' => KontakDesa::where('jenis', 'kantor_desa')->count(),
            'kepala_desa' => KontakDesa::where('jenis', 'kepala_desa')->count(),
            'puskesmas' => KontakDesa::where('jenis', 'puskesmas')->count(),
            'sekolah' => KontakDesa::where('jenis', 'sekolah')->count(),
        ];

        // Group by jenis for display
        $kontakByJenis = KontakDesa::aktif()
            ->byOrder()
            ->get()
            ->groupBy('jenis');

        return view('kontak-desa.index', compact('kontak', 'stats', 'kontakByJenis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jenisOptions = [
            'kantor_desa' => 'Kantor Desa',
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
            'puskesmas' => 'Puskesmas',
            'posyandu' => 'Posyandu',
            'sekolah' => 'Sekolah',
            'masjid' => 'Masjid',
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

        return view('kontak-desa.create', compact('jenisOptions', 'masterRwOptions'));
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
        $data['status_aktif'] = $request->has('status_aktif');
        $data['urutan'] = $request->urutan ?? 0;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('kontak-desa', 'public');
        }

        KontakDesa::create($data);
            // Clear relevant caches after creating
            return redirect()->route('kontak-desa.index')
            ->with('success', 'Data kontak desa berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(KontakDesa $kontakDesa)
    {
        return view('kontak-desa.show', compact('kontakDesa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KontakDesa $kontakDesa)
    {
        $jenisOptions = [
            'kantor_desa' => 'Kantor Desa',
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
            'puskesmas' => 'Puskesmas',
            'posyandu' => 'Posyandu',
            'sekolah' => 'Sekolah',
            'masjid' => 'Masjid',
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

        return view('kontak-desa.edit', compact('kontakDesa', 'jenisOptions', 'masterRwOptions'));
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

        $data = $request->all();
        $data['status_aktif'] = $request->has('status_aktif');
        $data['urutan'] = $request->urutan ?? 0;

        if ($request->hasFile('foto')) {
            // Delete old photo
            if ($kontakDesa->foto) {
                Storage::disk('public')->delete($kontakDesa->foto);
            }
            $data['foto'] = $request->file('foto')->store('kontak-desa', 'public');
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
            Storage::disk('public')->delete($kontakDesa->foto);
        }

        $kontakDesa->delete();

        // Clear relevant caches after deleting
        return redirect()->route('kontak-desa.index')
            ->with('success', 'Data kontak desa berhasil dihapus.');
    }
}
