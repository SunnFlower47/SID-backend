<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FasilitasDesa;
use App\Http\Resources\FasilitasDesaResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class FasilitasDesaController extends Controller
{
    use ApiResponse;

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

            $fasilitas = $query->orderBy('nama')->get();

            return $this->successResponse(FasilitasDesaResource::collection($fasilitas));
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil data fasilitas desa', 500, $e->getMessage());
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
                ->get();

            return $this->successResponse(FasilitasDesaResource::collection($fasilitas));
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil data fasilitas desa', 500, $e->getMessage());
        }
    }
}
