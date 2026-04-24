<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DesaSetting;
use App\Models\KontakDesa;

class KontakDesaController extends Controller
{
    /**
     * Get contact information (only public info)
     */
    public function index()
    {
        try {
            $desaInfo = DesaSetting::getDesaInfo();

            // Get only public contact info (kantor desa, kepala desa, etc.)
            $kontakPublik = KontakDesa::aktif()
                ->whereIn('jenis', ['kantor_desa', 'kepala_desa', 'sekretaris'])
                ->byOrder()
                ->get()
                ->map(function($kontak) {
                    return [
                        'nama' => $kontak->nama,
                        'jabatan' => $kontak->jabatan,
                        'alamat' => $kontak->alamat_lengkap,
                        'telepon' => $kontak->no_telepon,
                        'email' => $kontak->email,
                        'jam_operasional' => $kontak->jam_operasional,
                        'deskripsi' => $kontak->deskripsi
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'desa' => $desaInfo,
                    'kontak_publik' => $kontakPublik
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil informasi kontak',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}





