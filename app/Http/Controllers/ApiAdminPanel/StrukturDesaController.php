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
            'kategori' => 'required|string',
            'nik' => 'nullable|string|max:16|unique:struktur_desas,nik',
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
            'urutan' => 'integer|min:0',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('foto');
        $data['status_aktif'] = $request->boolean('status_aktif');
        
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
        return response()->json([
            'status' => 'success', 
            'data' => $strukturDesa->load(['rtMaster', 'rwMaster', 'dusunMaster'])
        ]);
    }

    public function update(Request $request, StrukturDesa $strukturDesa): JsonResponse
    {
        $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'jabatan' => 'sometimes|required|string|max:255',
            'kategori' => 'sometimes|required|string',
            'nik' => 'nullable|string|max:16|unique:struktur_desas,nik,' . $strukturDesa->id,
            'status_aktif' => 'boolean',
            'urutan' => 'integer|min:0',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('foto');
        if ($request->has('status_aktif')) {
            $data['status_aktif'] = $request->boolean('status_aktif');
        }

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

