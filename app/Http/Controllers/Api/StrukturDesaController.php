<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StrukturDesa;
use App\Models\Penduduk;
use Illuminate\Http\Request;

class StrukturDesaController extends Controller
{
    /**
     * Get struktur desa data grouped by category
     */
    public function index()
    {
        try {
            $struktur = StrukturDesa::orderBy('urutan')
                ->where('status_aktif', true)
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nama' => $item->nama,
                        'jabatan' => $item->jabatan,
                        'kategori' => $item->kategori,
                        'email' => $item->email,
                        'alamat' => $item->alamat,
                        'rt' => $item->rt,
                        'rw' => $item->rw,
                        'dusun' => $item->dusun,
                        'foto' => $item->foto,
                        'urutan' => $item->urutan
                    ];
                });

            // Group by category for better organization
            $groupedStruktur = $struktur->groupBy('kategori');

            return response()->json([
                'success' => true,
                'data' => $struktur,
                'grouped' => $groupedStruktur
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data struktur desa',
                'error' => $e->getMessage()
            ], 500);
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

            // If no data from struktur desa, fallback to penduduk table
            if ($strukturRtRw->isEmpty()) {
                $rtRw = Penduduk::select('rt', 'rw')
                    ->whereNotNull('rt')
                    ->whereNotNull('rw')
                    ->where('rt', '!=', '')
                    ->where('rw', '!=', '')
                    ->distinct()
                    ->orderBy('rt')
                    ->orderBy('rw')
                    ->get()
                    ->map(function($item) {
                        return [
                            'rt' => $item->rt,
                            'rw' => $item->rw,
                            'ketua_rt' => 'Ketua RT ' . $item->rt,
                            'ketua_rw' => 'Ketua RW ' . $item->rw,
                            'alamat' => 'RT ' . $item->rt . ' RW ' . $item->rw
                        ];
                    });
            } else {
                $rtRw = $strukturRtRw;
            }

            return response()->json([
                'success' => true,
                'data' => $rtRw
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data RT/RW',
                'error' => $e->getMessage()
            ], 500);
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
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nama' => $item->nama,
                        'jabatan' => $item->jabatan,
                        'email' => $item->email,
                        'alamat' => $item->alamat,
                        'rt' => $item->rt,
                        'rw' => $item->rw,
                        'dusun' => $item->dusun,
                        'foto' => $item->foto,
                        'urutan' => $item->urutan
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $bumdes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data BUMDes',
                'error' => $e->getMessage()
            ], 500);
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
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nama' => $item->nama,
                        'jabatan' => $item->jabatan,
                        'kategori' => $item->kategori,
                        'email' => $item->email,
                        'alamat' => $item->alamat,
                        'rt' => $item->rt,
                        'rw' => $item->rw,
                        'dusun' => $item->dusun,
                        'foto' => $item->foto,
                        'urutan' => $item->urutan
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $struktur
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data struktur desa',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
