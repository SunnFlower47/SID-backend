<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Http\Resources\AgendaDesaResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AgendaDesaController extends Controller
{
    use ApiResponse;

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

            $agenda = $query->orderBy('published_at', 'desc')->get();

            return $this->successResponse(AgendaDesaResource::collection($agenda));
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil data agenda desa', 500, $e->getMessage());
        }
    }

    /**
     * Get agenda categories
     */
    public function categories()
    {
        try {
            $categories = ['Pemerintahan', 'Kegiatan', 'Pembangunan', 'Sosial', 'Lainnya'];

            return $this->successResponse($categories);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil kategori agenda', 500, $e->getMessage());
        }
    }
}
