<?php

namespace App\Http\Controllers\ApiAdminPanel;

use Illuminate\Http\Request;
use App\Models\StrukturDesa;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class StrukturDesaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = StrukturDesa::query();
        if ($request->filled('kategori')) $query->where('kategori', $request->kategori);
        if ($request->filled('status')) $query->where('status_aktif', $request->status === 'aktif');

        $struktur = $query->orderBy('urutan')->orderBy('nama')->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'items' => $struktur,
                'grouped' => $struktur->groupBy('kategori'),
                'stats' => [
                    'total' => StrukturDesa::count(),
                    'aktif' => StrukturDesa::where('status_aktif', true)->count(),
                ]
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'kategori' => 'required|in:kepala_desa,sekretaris,bendahara,kasi_pemerintahan,kasi_kesejahteraan,kasi_pelayanan,kepala_dusun,ketua_rw,ketua_rt,ketua_bumdes,staf_kaur,lainnya',
            'status_aktif' => 'boolean',
            'urutan' => 'integer|min:0',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('struktur-desa', 'public');
        }

        $struktur = StrukturDesa::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Perangkat desa berhasil ditambahkan',
            'data' => $struktur
        ], 201);
    }

    public function show(StrukturDesa $strukturDesa): JsonResponse
    {
        return response()->json(['status' => 'success', 'data' => $strukturDesa]);
    }

    public function update(Request $request, StrukturDesa $strukturDesa): JsonResponse
    {
        $data = $request->all();
        if ($request->hasFile('foto')) {
            if ($strukturDesa->foto) Storage::disk('public')->delete($strukturDesa->foto);
            $data['foto'] = $request->file('foto')->store('struktur-desa', 'public');
        }

        $strukturDesa->update($data);
        return response()->json(['status' => 'success', 'message' => 'Data perangkat desa diperbarui', 'data' => $strukturDesa]);
    }

    public function destroy(StrukturDesa $strukturDesa): JsonResponse
    {
        if ($strukturDesa->foto) Storage::disk('public')->delete($strukturDesa->foto);
        $strukturDesa->delete();
        return response()->json(['status' => 'success', 'message' => 'Data perangkat desa dihapus']);
    }
}
