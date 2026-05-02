<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Pengaduan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PengaduanController extends Controller
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
        $query = Pengaduan::with('user');

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by prioritas
        if ($request->has('prioritas') && $request->prioritas) {
            $query->where('prioritas', $request->prioritas);
        }

        // Filter by kategori
        if ($request->has('kategori') && $request->kategori) {
            $query->where('kategori', $request->kategori);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('judul', 'like', "%{$request->search}%")
                  ->orWhere('deskripsi', 'like', "%{$request->search}%")
                  ->orWhere('nama_pelapor', 'like', "%{$request->search}%");
            });
        }

        $pengaduans = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get statistics
        $stats = [
            'total' => Pengaduan::count(),
            'baru' => Pengaduan::where('status', 'baru')->count(),
            'diproses' => Pengaduan::where('status', 'diproses')->count(),
            'selesai' => Pengaduan::where('status', 'selesai')->count(),
            'darurat' => Pengaduan::where('prioritas', 'darurat')->count(),
        ];

        return view('pengaduan.index', compact('pengaduans', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('pelayanan_informasi');

        return view('pengaduan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('pelayanan_informasi');

        $validator = Validator::make($request->all(), [
            'nama_pelapor' => 'required|string|max:255',
            'nik_pelapor' => 'nullable|string|size:16',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'alamat' => 'required|string|max:500',
            'kategori' => 'required|string|in:infrastruktur,keamanan,kebersihan,administrasi,lainnya',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'lokasi' => 'nullable|string|max:255',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'prioritas' => 'required|in:rendah,sedang,tinggi,darurat'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $data = $request->all();
            $data['status'] = 'baru';

            // Handle photo uploads
            if ($request->hasFile('foto')) {
                $photos = [];
                foreach ($request->file('foto') as $photo) {
                    $path = $photo->store('pengaduan', 'public');
                    $photos[] = $path;
                }
                $data['foto'] = $photos;
            }

            Pengaduan::create($data);

            DB::commit();

            return redirect()->route('pengaduan.index')
                ->with('success', 'Pengaduan berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan pengaduan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pengaduan $pengaduan)
    {
        return view('pengaduan.show', compact('pengaduan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pengaduan $pengaduan)
    {
        Gate::authorize('pelayanan_informasi');

        return view('pengaduan.edit', compact('pengaduan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pengaduan $pengaduan)
    {
        Gate::authorize('pelayanan_informasi');

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:baru,diproses,selesai,ditolak',
            'prioritas' => 'required|in:rendah,sedang,tinggi,darurat',
            'tanggapan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $data = $request->only(['status', 'prioritas', 'tanggapan']);
            $data['user_id'] = Auth::user()->id;

            if ($request->status === 'selesai' || $request->status === 'ditolak') {
                $data['tanggal_tanggapan'] = now();
            }

            $pengaduan->update($data);

            DB::commit();

            return redirect()->route('pengaduan.index')
                ->with('success', 'Pengaduan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui pengaduan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pengaduan $pengaduan)
    {
        Gate::authorize('pelayanan_informasi');

        try {
            DB::beginTransaction();

            // Delete photos
            if ($pengaduan->foto) {
                foreach ($pengaduan->foto as $photo) {
                    Storage::disk('public')->delete($photo);
                }
            }

            $pengaduan->delete();

            DB::commit();

            return redirect()->route('pengaduan.index')
                ->with('success', 'Pengaduan berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghapus pengaduan: ' . $e->getMessage());
        }
    }

}
