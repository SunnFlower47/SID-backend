<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProyekDesa;

class ProyekDesaController extends Controller
{
    /**
     * Get list of proyek desa
     */
    public function index()
    {
        try {
            $proyeks = ProyekDesa::select('id', 'nama_proyek', 'deskripsi', 'lokasi', 'anggaran', 'status', 'tanggal_mulai', 'tanggal_selesai', 'created_at')
                ->orderBy('tanggal_mulai', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $proyeks
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data proyek desa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single proyek desa
     */
    public function show($id)
    {
        try {
            $proyek = ProyekDesa::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $proyek
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Proyek tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get proyek by year
     */
    public function byYear($year)
    {
        try {
            $proyeks = ProyekDesa::whereYear('tanggal_mulai', $year)
                ->orderBy('tanggal_mulai', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $proyeks
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data proyek tahun ' . $year,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
