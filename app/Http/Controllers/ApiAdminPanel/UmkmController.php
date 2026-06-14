<?php

namespace App\Http\Controllers\ApiAdminPanel;

use Illuminate\Http\Request;
use App\Models\Umkm;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class UmkmController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Umkm::query();

        if ($request->filled('status')) $query->where('status_usaha', $request->status);
        if ($request->filled('jenis_usaha')) $query->where('jenis_usaha', $request->jenis_usaha);
        if ($request->filled('is_unggulan')) $query->where('is_unggulan', $request->is_unggulan);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q->where('nama_usaha', 'like', "%{$search}%")->orWhere('nama_pemilik', 'like', "%{$search}%"));
        }

        $umkms = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $umkms,
            'meta' => [
                'stats' => [
                    'total' => Umkm::count(),
                    'aktif' => Umkm::where('status_usaha', 'aktif')->count(),
                    'unggulan' => Umkm::where('is_unggulan', true)->count(),
                ]
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'nama_pemilik' => 'required|string|max:255',
            'alamat_usaha' => 'required|string',
            'jenis_usaha' => 'required|in:makanan,minuman,kerajinan,jasa,perdagangan,pertanian,peternakan,lainnya',
            'jumlah_karyawan' => 'integer|min:0',
            'status_usaha' => 'required|in:aktif,tutup,pindah',
            'foto_usaha.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();
        if ($request->hasFile('foto_usaha')) {
            $paths = [];
            foreach ($request->file('foto_usaha') as $file) {
                $paths[] = $file->store('umkm/fotos');
            }
            $data['foto_usaha'] = $paths;
        }

        $umkm = Umkm::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data UMKM berhasil ditambahkan',
            'data' => $umkm
        ], 201);
    }

    public function show(Umkm $umkm): JsonResponse
    {
        return response()->json(['status' => 'success', 'data' => $umkm]);
    }

    public function update(Request $request, Umkm $umkm): JsonResponse
    {
        $data = $request->all();
        if ($request->hasFile('foto_usaha')) {
            if ($umkm->foto_usaha) {
                foreach ($umkm->foto_usaha as $foto) Storage::disk('s3')->delete($foto);
            }
            $paths = [];
            foreach ($request->file('foto_usaha') as $file) {
                $paths[] = $file->store('umkm/fotos');
            }
            $data['foto_usaha'] = $paths;
        }

        $umkm->update($data);
        return response()->json(['status' => 'success', 'message' => 'Data UMKM berhasil diperbarui', 'data' => $umkm]);
    }

    public function destroy(Umkm $umkm): JsonResponse
    {
        if ($umkm->foto_usaha) {
            foreach ($umkm->foto_usaha as $foto) Storage::disk('s3')->delete($foto);
        }
        $umkm->delete();
        return response()->json(['status' => 'success', 'message' => 'Data UMKM berhasil dihapus']);
    }
}
