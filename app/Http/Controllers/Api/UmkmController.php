<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Umkm;

class UmkmController extends Controller
{
    /**
     * Get list of UMKM
     */
    public function index(Request $request)
    {
        $query = Umkm::aktif();

        // Filter by jenis usaha
        if ($request->has('jenis_usaha') && $request->jenis_usaha) {
            $query->where('jenis_usaha', $request->jenis_usaha);
        }

        // Filter by unggulan
        if ($request->has('is_unggulan') && $request->is_unggulan) {
            $query->where('is_unggulan', true);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_usaha', 'like', "%{$search}%")
                  ->orWhere('nama_pemilik', 'like', "%{$search}%")
                  ->orWhere('alamat_usaha', 'like', "%{$search}%");
            });
        }

        $umkms = $query->orderBy('is_unggulan', 'desc')
                      ->orderBy('created_at', 'desc')
                      ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $umkms,
            'message' => 'Data UMKM berhasil diambil'
        ]);
    }

    /**
     * Get UMKM by ID
     */
    public function show($id)
    {
        $umkm = Umkm::aktif()->find($id);

        if (!$umkm) {
            return response()->json([
                'success' => false,
                'message' => 'UMKM tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $umkm,
            'message' => 'Data UMKM berhasil diambil'
        ]);
    }

    /**
     * Get produk unggulan desa
     */
    public function unggulan()
    {
        $umkms = Umkm::aktif()
            ->unggulan()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $umkms,
            'message' => 'Data produk unggulan berhasil diambil'
        ]);
    }

    /**
     * Get UMKM by jenis usaha
     */
    public function byJenisUsaha($jenis)
    {
        $umkms = Umkm::aktif()
            ->jenisUsaha($jenis)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $umkms,
            'message' => "Data UMKM jenis {$jenis} berhasil diambil"
        ]);
    }

    /**
     * Get UMKM statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => Umkm::aktif()->count(),
            'unggulan' => Umkm::aktif()->unggulan()->count(),
            'verified' => Umkm::aktif()->verified()->count(),
            'by_jenis' => Umkm::aktif()
                ->selectRaw('jenis_usaha, count(*) as total')
                ->groupBy('jenis_usaha')
                ->get()
                ->pluck('total', 'jenis_usaha')
                ->toArray(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Statistik UMKM berhasil diambil'
        ]);
    }
}
