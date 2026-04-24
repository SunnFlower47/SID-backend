<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Umkm;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UmkmController extends Controller
{
        public function __construct()
    {
        $this->middleware(['auth', 'can:umkm.view']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Umkm::query();

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status_usaha', $request->status);
        }

        // Filter by jenis usaha
        if ($request->has('jenis_usaha') && $request->jenis_usaha) {
            $query->where('jenis_usaha', $request->jenis_usaha);
        }

        // Filter by unggulan
        if ($request->has('is_unggulan') && $request->is_unggulan !== '') {
            $query->where('is_unggulan', $request->is_unggulan);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_usaha', 'like', "%{$search}%")
                  ->orWhere('nama_pemilik', 'like', "%{$search}%")
                  ->orWhere('alamat_usaha', 'like', "%{$search}%");
            });
        }

        $umkms = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $stats = [
            'total' => Umkm::count(),
            'aktif' => Umkm::where('status_usaha', 'aktif')->count(),
            'unggulan' => Umkm::where('is_unggulan', true)->count(),
            'verified' => Umkm::where('is_verified', true)->count(),
        ];

        return view('umkm.index', compact('umkms', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('umkm.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_usaha' => 'required|string|max:255',
            'nama_pemilik' => 'required|string|max:255',
            'nik_pemilik' => 'nullable|string|size:16',
            'alamat_usaha' => 'required|string|max:500',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'dusun' => 'nullable|string|max:100',
            'no_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'jenis_usaha' => 'required|in:makanan,minuman,kerajinan,jasa,perdagangan,pertanian,peternakan,lainnya',
            'deskripsi_usaha' => 'nullable|string',
            'jumlah_karyawan' => 'required|integer|min:0',
            'status_usaha' => 'required|in:aktif,tutup,pindah',
            'tanggal_berdiri' => 'nullable|date',
            'produk_unggulan' => 'nullable|array',
            'foto_usaha' => 'nullable|array',
            'foto_usaha.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'is_unggulan' => 'boolean',
            'is_verified' => 'boolean',
        ], [
            'nama_usaha.required' => 'Nama usaha harus diisi.',
            'nama_pemilik.required' => 'Nama pemilik harus diisi.',
            'alamat_usaha.required' => 'Alamat usaha harus diisi.',
            'jenis_usaha.required' => 'Jenis usaha harus dipilih.',
            'jumlah_karyawan.required' => 'Jumlah karyawan harus diisi.',
            'jumlah_karyawan.integer' => 'Jumlah karyawan harus berupa angka.',
            'jumlah_karyawan.min' => 'Jumlah karyawan minimal 0.',
            'status_usaha.required' => 'Status usaha harus dipilih.',
            'nik_pemilik.size' => 'NIK harus 16 digit.',
            'email.email' => 'Format email tidak valid.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        // Set default value for jumlah_karyawan if empty
        if (empty($data['jumlah_karyawan'])) {
            $data['jumlah_karyawan'] = 0;
        }

        // Handle file uploads
        if ($request->hasFile('foto_usaha')) {
            $fotoPaths = [];
            foreach ($request->file('foto_usaha') as $file) {
                $path = $file->store('umkm/fotos', 'public');
                $fotoPaths[] = $path;
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
        return view('umkm.show', compact('umkm'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Umkm $umkm)
    {
        return view('umkm.edit', compact('umkm'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Umkm $umkm)
    {
        $validator = Validator::make($request->all(), [
            'nama_usaha' => 'required|string|max:255',
            'nama_pemilik' => 'required|string|max:255',
            'nik_pemilik' => 'nullable|string|size:16',
            'alamat_usaha' => 'required|string|max:500',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'dusun' => 'nullable|string|max:100',
            'no_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'jenis_usaha' => 'required|in:makanan,minuman,kerajinan,jasa,perdagangan,pertanian,peternakan,lainnya',
            'deskripsi_usaha' => 'nullable|string',
            'jumlah_karyawan' => 'required|integer|min:0',
            'status_usaha' => 'required|in:aktif,tutup,pindah',
            'tanggal_berdiri' => 'nullable|date',
            'produk_unggulan' => 'nullable|array',
            'foto_usaha' => 'nullable|array',
            'foto_usaha.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'is_unggulan' => 'boolean',
            'is_verified' => 'boolean',
        ], [
            'nama_usaha.required' => 'Nama usaha harus diisi.',
            'nama_pemilik.required' => 'Nama pemilik harus diisi.',
            'alamat_usaha.required' => 'Alamat usaha harus diisi.',
            'jenis_usaha.required' => 'Jenis usaha harus dipilih.',
            'jumlah_karyawan.required' => 'Jumlah karyawan harus diisi.',
            'jumlah_karyawan.integer' => 'Jumlah karyawan harus berupa angka.',
            'jumlah_karyawan.min' => 'Jumlah karyawan minimal 0.',
            'status_usaha.required' => 'Status usaha harus dipilih.',
            'nik_pemilik.size' => 'NIK harus 16 digit.',
            'email.email' => 'Format email tidak valid.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        // Set default value for jumlah_karyawan if empty
        if (empty($data['jumlah_karyawan'])) {
            $data['jumlah_karyawan'] = 0;
        }

        // Handle file uploads
        if ($request->hasFile('foto_usaha')) {
            // Delete old photos
            if ($umkm->foto_usaha) {
                foreach ($umkm->foto_usaha as $foto) {
                    Storage::disk('public')->delete($foto);
                }
            }

            $fotoPaths = [];
            foreach ($request->file('foto_usaha') as $file) {
                $path = $file->store('umkm/fotos', 'public');
                $fotoPaths[] = $path;
            }
            $data['foto_usaha'] = $fotoPaths;
        }

        $umkm->update($data);

        return redirect()->route('umkm.index')
            ->with('success', 'Data UMKM berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Umkm $umkm)
    {
        // Delete photos
        if ($umkm->foto_usaha) {
            foreach ($umkm->foto_usaha as $foto) {
                Storage::disk('public')->delete($foto);
            }
        }

        $umkm->delete();

        return redirect()->route('umkm.index')
            ->with('success', 'Data UMKM berhasil dihapus!');
    }
}
