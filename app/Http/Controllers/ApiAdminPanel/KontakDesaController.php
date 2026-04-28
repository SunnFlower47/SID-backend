<?php

namespace App\Http\Controllers\ApiAdminPanel;

use Illuminate\Http\Request;
use App\Models\KontakDesa;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class KontakDesaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = KontakDesa::query();
        if ($request->filled('jenis')) $query->where('jenis', $request->jenis);
        if ($request->filled('status')) $query->where('status_aktif', $request->status === 'aktif');

        $kontaks = $query->orderBy('urutan')->orderBy('nama')->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'items' => $kontaks,
                'grouped' => $kontaks->groupBy('jenis'),
                'stats' => [
                    'total' => KontakDesa::count(),
                    'aktif' => KontakDesa::where('status_aktif', true)->count(),
                ]
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|in:kantor_desa,kepala_desa,sekretaris,bendahara,kasi_pemerintahan,kasi_kesejahteraan,kasi_pelayanan,kepala_dusun,ketua_rw,ketua_rt,ketua_bumdes,puskesmas,posyandu,sekolah,masjid,lainnya',
            'no_hp' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('kontak-desa', 'public');
        }

        $kontak = KontakDesa::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Kontak desa berhasil ditambahkan',
            'data' => $kontak
        ], 201);
    }

    public function show(KontakDesa $kontakDesa): JsonResponse
    {
        return response()->json(['status' => 'success', 'data' => $kontakDesa]);
    }

    public function update(Request $request, KontakDesa $kontakDesa): JsonResponse
    {
        $data = $request->all();
        if ($request->hasFile('foto')) {
            if ($kontakDesa->foto) Storage::disk('public')->delete($kontakDesa->foto);
            $data['foto'] = $request->file('foto')->store('kontak-desa', 'public');
        }

        $kontakDesa->update($data);
        return response()->json(['status' => 'success', 'message' => 'Kontak desa diperbarui', 'data' => $kontakDesa]);
    }

    public function destroy(KontakDesa $kontakDesa): JsonResponse
    {
        if ($kontakDesa->foto) Storage::disk('public')->delete($kontakDesa->foto);
        $kontakDesa->delete();
        return response()->json(['status' => 'success', 'message' => 'Kontak desa dihapus']);
    }
}
