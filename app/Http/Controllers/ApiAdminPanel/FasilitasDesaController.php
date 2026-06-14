<?php

namespace App\Http\Controllers\ApiAdminPanel;

use Illuminate\Http\Request;
use App\Models\FasilitasDesa;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class FasilitasDesaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = FasilitasDesa::query();
        if ($request->filled('jenis')) $query->where('jenis', $request->jenis);
        if ($request->filled('status')) $query->where('status_aktif', $request->status === 'aktif');

        $fasilitas = $query->orderBy('nama')->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $fasilitas,
            'meta' => [
                'stats' => [
                    'total' => FasilitasDesa::count(),
                    'aktif' => FasilitasDesa::where('status_aktif', true)->count(),
                    'jenis_summary' => FasilitasDesa::selectRaw('jenis, count(*) as count')->groupBy('jenis')->get()
                ]
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|in:sekolah,posyandu,masjid,gereja,puskesmas,pos_ronda,balai_desa,lapangan,pasar,lainnya',
            'alamat' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('fasilitas-desa');
        }

        $fasilitas = FasilitasDesa::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Fasilitas desa berhasil ditambahkan',
            'data' => $fasilitas
        ], 201);
    }

    public function show(FasilitasDesa $fasilitasDesa): JsonResponse
    {
        return response()->json(['status' => 'success', 'data' => $fasilitasDesa]);
    }

    public function update(Request $request, FasilitasDesa $fasilitasDesa): JsonResponse
    {
        $data = $request->all();
        if ($request->hasFile('foto')) {
            if ($fasilitasDesa->foto) Storage::disk('s3')->delete($fasilitasDesa->foto);
            $data['foto'] = $request->file('foto')->store('fasilitas-desa');
        }

        $fasilitasDesa->update($data);
        return response()->json(['status' => 'success', 'message' => 'Fasilitas desa berhasil diperbarui', 'data' => $fasilitasDesa]);
    }

    public function destroy(FasilitasDesa $fasilitasDesa): JsonResponse
    {
        if ($fasilitasDesa->foto) Storage::disk('s3')->delete($fasilitasDesa->foto);
        $fasilitasDesa->delete();
        return response()->json(['status' => 'success', 'message' => 'Fasilitas desa berhasil dihapus']);
    }
}
