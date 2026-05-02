<?php

namespace App\Http\Controllers\ApiAdminPanel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Pengaduan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class PengaduanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('pelayanan_informasi');

        $query = Pengaduan::with('user');
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('prioritas')) $query->where('prioritas', $request->prioritas);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q->where('judul', 'like', "%{$search}%")->orWhere('nama_pelapor', 'like', "%{$search}%"));
        }

        $pengaduans = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $pengaduans,
            'meta' => [
                'stats' => [
                    'total' => Pengaduan::count(),
                    'baru' => Pengaduan::where('status', 'baru')->count(),
                    'diproses' => Pengaduan::where('status', 'diproses')->count(),
                    'selesai' => Pengaduan::where('status', 'selesai')->count(),
                    'darurat' => Pengaduan::where('prioritas', 'darurat')->count(),
                ]
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        Gate::authorize('pelayanan_informasi');

        $validated = $request->validate([
            'nama_pelapor' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kategori' => 'required|in:infrastruktur,keamanan,kebersihan,administrasi,lainnya',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'prioritas' => 'required|in:rendah,sedang,tinggi,darurat',
            'foto.*' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        $data['status'] = 'baru';

        if ($request->hasFile('foto')) {
            $paths = [];
            foreach ($request->file('foto') as $photo) {
                $paths[] = $photo->store('pengaduan', 'public');
            }
            $data['foto'] = $paths;
        }

        $pengaduan = Pengaduan::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengaduan berhasil disimpan',
            'data' => $pengaduan
        ], 201);
    }

    public function show(Pengaduan $pengaduan): JsonResponse
    {
        return response()->json(['status' => 'success', 'data' => $pengaduan->load('user')]);
    }

    public function update(Request $request, Pengaduan $pengaduan): JsonResponse
    {
        Gate::authorize('pelayanan_informasi');

        $validated = $request->validate([
            'status' => 'required|in:baru,diproses,selesai,ditolak',
            'prioritas' => 'required|in:rendah,sedang,tinggi,darurat',
            'tanggapan' => 'nullable|string'
        ]);

        $data = $request->only(['status', 'prioritas', 'tanggapan']);
        $data['user_id'] = Auth::id();

        if ($request->status === 'selesai' || $request->status === 'ditolak') {
            $data['tanggal_tanggapan'] = now();
        }

        $pengaduan->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengaduan berhasil diperbarui',
            'data' => $pengaduan
        ]);
    }

    public function destroy(Pengaduan $pengaduan): JsonResponse
    {
        Gate::authorize('pelayanan_informasi');
        if ($pengaduan->foto) {
            foreach ($pengaduan->foto as $photo) Storage::disk('public')->delete($photo);
        }
        $pengaduan->delete();
        return response()->json(['status' => 'success', 'message' => 'Pengaduan berhasil dihapus']);
    }
}
