<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FasilitasDesa;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
class FasilitasDesaController extends Controller
{
        public function __construct()
    {
        $this->middleware(['auth', 'can:fasilitas-desa.view']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fasilitas = FasilitasDesa::orderBy('nama')->paginate(15);

        $stats = [
            'total' => FasilitasDesa::count(),
            'aktif' => FasilitasDesa::where('status_aktif', true)->count(),
            'sekolah' => FasilitasDesa::where('jenis', 'sekolah')->count(),
            'masjid' => FasilitasDesa::where('jenis', 'masjid')->count(),
            'posyandu' => FasilitasDesa::where('jenis', 'posyandu')->count(),
            'puskesmas' => FasilitasDesa::where('jenis', 'puskesmas')->count(),
        ];

        return view('fasilitas-desa.index', compact('fasilitas', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jenisOptions = [
            'sekolah' => 'Sekolah',
            'posyandu' => 'Posyandu',
            'masjid' => 'Masjid',
            'gereja' => 'Gereja',
            'puskesmas' => 'Puskesmas',
            'pos_ronda' => 'Pos Ronda',
            'balai_desa' => 'Balai Desa',
            'lapangan' => 'Lapangan',
            'pasar' => 'Pasar',
            'lainnya' => 'Lainnya',
        ];

        return view('fasilitas-desa.create', compact('jenisOptions'));
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
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'dusun' => 'nullable|string|max:100',
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
        return view('fasilitas-desa.show', compact('fasilitasDesa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FasilitasDesa $fasilitasDesa)
    {
        $jenisOptions = [
            'sekolah' => 'Sekolah',
            'posyandu' => 'Posyandu',
            'masjid' => 'Masjid',
            'gereja' => 'Gereja',
            'puskesmas' => 'Puskesmas',
            'pos_ronda' => 'Pos Ronda',
            'balai_desa' => 'Balai Desa',
            'lapangan' => 'Lapangan',
            'pasar' => 'Pasar',
            'lainnya' => 'Lainnya',
        ];

        return view('fasilitas-desa.edit', compact('fasilitasDesa', 'jenisOptions'));
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
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'dusun' => 'nullable|string|max:100',
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FasilitasDesa $fasilitasDesa)
    {
        if ($fasilitasDesa->foto) {
            Storage::disk('public')->delete($fasilitasDesa->foto);
        }

        $fasilitasDesa->delete();

        return redirect()->route('fasilitas-desa.index')
            ->with('success', 'Fasilitas desa berhasil dihapus.');
    }
}
