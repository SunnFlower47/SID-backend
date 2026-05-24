<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Umkm;
use App\Http\Resources\UmkmResource;
use App\Traits\ApiResponse;

class UmkmController extends Controller
{
    use ApiResponse;

    /**
     * Get list of UMKM
     */
    public function index(Request $request)
    {
        try {
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

            // Transform the paginated items using the resource to preserve pagination structure
            $umkms->setCollection(
                $umkms->getCollection()->map(fn($item) => new UmkmResource($item))
            );

            return $this->successResponse($umkms, 'Data UMKM berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil data UMKM', 500, $e->getMessage());
        }
    }

    /**
     * Get UMKM by ID
     */
    public function show($id)
    {
        try {
            $umkm = Umkm::aktif()->find($id);

            if (!$umkm) {
                return $this->errorResponse('UMKM tidak ditemukan', 404);
            }

            return $this->successResponse(new UmkmResource($umkm), 'Data UMKM berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil data UMKM', 500, $e->getMessage());
        }
    }

    /**
     * Get produk unggulan desa
     */
    public function unggulan()
    {
        try {
            $umkms = Umkm::aktif()
                ->unggulan()
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse(UmkmResource::collection($umkms), 'Data produk unggulan berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil data produk unggulan', 500, $e->getMessage());
        }
    }

    /**
     * Get UMKM by jenis usaha
     */
    public function byJenisUsaha($jenis)
    {
        try {
            $umkms = Umkm::aktif()
                ->jenisUsaha($jenis)
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse(UmkmResource::collection($umkms), "Data UMKM jenis {$jenis} berhasil diambil");
        } catch (\Exception $e) {
            return $this->errorResponse("Gagal mengambil data UMKM jenis {$jenis}", 500, $e->getMessage());
        }
    }

    /**
     * Get UMKM statistics
     */
    public function statistics()
    {
        try {
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

            return $this->successResponse($stats, 'Statistik UMKM berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil statistik UMKM', 500, $e->getMessage());
        }
    }
}
