<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FasilitasDesa;
use Illuminate\Http\Request;

class FasilitasDesaController extends Controller
{
    /**
     * Get fasilitas desa data
     */
    public function index(Request $request)
    {
        try {
            $query = FasilitasDesa::query();

            if ($request->has('jenis')) {
                $query->where('jenis', $request->jenis);
            }

            $fasilitas = $query->orderBy('nama')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nama' => $item->nama,
                        'jenis' => $item->jenis,
                        'alamat' => $item->alamat,
                        'koordinat' => [
                            'lat' => $item->latitude,
                            'lng' => $item->longitude
                        ],
                        'keterangan' => $item->keterangan
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $fasilitas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data fasilitas desa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get fasilitas by jenis
     */
    public function byJenis($jenis)
    {
        try {
            $fasilitas = FasilitasDesa::where('jenis', $jenis)
                ->orderBy('nama')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nama' => $item->nama,
                        'jenis' => $item->jenis,
                        'alamat' => $item->alamat,
                        'koordinat' => [
                            'lat' => $item->latitude,
                            'lng' => $item->longitude
                        ],
                        'keterangan' => $item->keterangan
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $fasilitas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data fasilitas desa',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
