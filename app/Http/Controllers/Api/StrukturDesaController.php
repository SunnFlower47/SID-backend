<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StrukturDesa;
use App\Models\Penduduk;
use App\Http\Resources\StrukturDesaResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class StrukturDesaController extends Controller
{
    use ApiResponse;

    /**
     * Get struktur desa data grouped by category
     */
    public function index()
    {
        try {
            $strukturRaw = StrukturDesa::orderBy('urutan')
                ->where('status_aktif', true)
                ->get();

            $struktur = StrukturDesaResource::collection($strukturRaw);
            $resolvedStruktur = collect($struktur->resolve());
            $groupedStruktur = $resolvedStruktur->groupBy('kategori');

            return response()->json([
                'success' => true,
                'data' => $resolvedStruktur,
                'grouped' => $groupedStruktur
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil data struktur desa', 500, $e->getMessage());
        }
    }

    /**
     * Get perangkat desa data (alias for struktur desa)
     */
    public function perangkatDesa()
    {
        return $this->index();
    }

    /**
     * Get RT/RW data from struktur desa and penduduk table
     */
    public function rtRw()
    {
        try {
            // Get RT/RW from struktur desa first
            $strukturRtRw = StrukturDesa::where('status_aktif', true)
                ->whereIn('kategori', ['ketua_rt', 'ketua_rw'])
                ->get()
                ->map(function($item) {
                    return [
                        'rt' => $item->rt,
                        'rw' => $item->rw,
                        'ketua_rt' => $item->kategori === 'ketua_rt' ? $item->nama : null,
                        'ketua_rw' => $item->kategori === 'ketua_rw' ? $item->nama : null,
                        'alamat' => $item->alamat,
                        'no_hp' => $item->no_hp,
                        'jabatan' => $item->jabatan
                    ];
                });

            // If no data from struktur desa, fallback to master table
            if ($strukturRtRw->isEmpty()) {
                $rtRw = \App\Models\Rt::with(['rwMaster', 'dusunMaster'])
                    ->get()
                    ->map(function($rt) {
                        return [
                            'rt' => $rt->kode,
                            'rw' => optional($rt->rwMaster)->kode,
                            'ketua_rt' => 'Ketua RT ' . $rt->kode,
                            'ketua_rw' => 'Ketua RW ' . optional($rt->rwMaster)->kode,
                            'alamat' => 'RT ' . $rt->kode . ' RW ' . optional($rt->rwMaster)->kode,
                            'dusun' => optional($rt->dusunMaster)->nama
                        ];
                    });
            } else {
                $rtRw = $strukturRtRw;
            }

            return $this->successResponse($rtRw);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil data RT/RW', 500, $e->getMessage());
        }
    }

    /**
     * Get raw master wilayah (Dusun, RW, RT) for form dropdowns
     */
    public function masterWilayah()
    {
        try {
            $dusun = \App\Models\Dusun::where('is_active', true)->get(['id', 'kode', 'nama']);
            $rw = \App\Models\Rw::where('is_active', true)->get(['id', 'kode', 'nama']);
            $rt = \App\Models\Rt::where('is_active', true)->get(['id', 'rw_id', 'dusun_id', 'kode', 'nama']);

            return $this->successResponse([
                'dusun' => $dusun,
                'rw' => $rw,
                'rt' => $rt
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil data master wilayah', 500, $e->getMessage());
        }
    }

    /**
     * Get BUMDes data from struktur desa
     */
    public function bumdes()
    {
        try {
            $bumdes = StrukturDesa::where('status_aktif', true)
                ->where('kategori', 'ketua_bumdes')
                ->orderBy('urutan')
                ->get();

            return $this->successResponse(StrukturDesaResource::collection($bumdes));
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil data BUMDes', 500, $e->getMessage());
        }
    }

    /**
     * Get struktur desa by category
     */
    public function byCategory($category)
    {
        try {
            $struktur = StrukturDesa::where('status_aktif', true)
                ->where('kategori', $category)
                ->orderBy('urutan')
                ->get();

            return $this->successResponse(StrukturDesaResource::collection($struktur));
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil data struktur desa', 500, $e->getMessage());
        }
    }
}
