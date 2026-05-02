<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Umkm;
use App\Models\Rw;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UmkmController extends Controller
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
        $query = Umkm::withWilayah();

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

        // Filter by Wilayah
        if ($request->has('rt_id') && $request->rt_id) {
            $query->where('rt_id', $request->rt_id);
        }
        if ($request->has('rw_id') && $request->rw_id) {
            $query->where('rw_id', $request->rw_id);
        }
        if ($request->has('dusun_id') && $request->dusun_id) {
            $query->where('dusun_id', $request->dusun_id);
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
        $rws = Rw::orderBy('kode')->get();
        $masterRwOptions = Rw::with('rts')->orderBy('kode')->get()->map(function($rw) {
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

        return view('umkm.create', compact('rws', 'masterRwOptions'));
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
            'rt_id' => 'nullable|exists:rts,id',
            'rw_id' => 'nullable|exists:rws,id',
            'dusun_id' => 'nullable|exists:dusuns,id',
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
        $rws = Rw::orderBy('kode')->get();
        $masterRwOptions = Rw::with('rts')->orderBy('kode')->get()->map(function($rw) {
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

        return view('umkm.edit', compact('umkm', 'rws', 'masterRwOptions'));
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
            'rt_id' => 'nullable|exists:rts,id',
            'rw_id' => 'nullable|exists:rws,id',
            'dusun_id' => 'nullable|exists:dusuns,id',
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
