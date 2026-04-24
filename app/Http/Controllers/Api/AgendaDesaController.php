<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;

class AgendaDesaController extends Controller
{
    /**
     * Get agenda desa data (using berita with kategori agenda)
     */
    public function index(Request $request)
    {
        try {
            $query = Berita::where('kategori', 'agenda')
                ->where('status', 'published');

            if ($request->has('bulan')) {
                $query->whereMonth('published_at', $request->bulan);
            }

            if ($request->has('tahun')) {
                $query->whereYear('published_at', $request->tahun);
            }

            $agenda = $query->orderBy('published_at', 'desc')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'judul' => $item->judul,
                        'deskripsi' => $item->konten,
                        'tanggal' => $item->published_at ? $item->published_at->format('Y-m-d') : null,
                        'waktu' => $item->published_at ? $item->published_at->format('H:i') : null,
                        'lokasi' => 'Kantor Desa Cibatu',
                        'kategori' => $item->kategori,
                        'status' => 'upcoming'
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $agenda
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data agenda desa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get agenda categories
     */
    public function categories()
    {
        try {
            $categories = ['Pemerintahan', 'Kegiatan', 'Pembangunan', 'Sosial', 'Lainnya'];

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kategori agenda',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
