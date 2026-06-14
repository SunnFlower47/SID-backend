<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengaduan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PengaduanController extends Controller
{
    /**
     * Get list of pengaduan (public)
     */
    public function index()
    {
        $pengaduans = Pengaduan::select('id', 'judul', 'kategori', 'status', 'created_at')
            ->where('status', '!=', 'draft')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pengaduans
        ]);
    }

    /**
     * Submit pengaduan from web desa
     */
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_pelapor' => 'required|string|max:255',
            'nik_pelapor' => 'nullable|string|size:16',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'alamat' => 'required|string|max:500',
            'kategori' => 'required|string|in:infrastruktur,keamanan,kebersihan,administrasi,lainnya',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'lokasi' => 'nullable|string|max:255',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'prioritas' => 'required|in:rendah,sedang,tinggi,darurat'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            DB::beginTransaction();

            $data = $request->all();
            $data['status'] = 'baru';

            // Handle photo uploads
            if ($request->hasFile('foto')) {
                $photos = [];
                foreach ($request->file('foto') as $photo) {
                    $path = $photo->store('pengaduan');
                    $photos[] = $path;
                }
                $data['foto'] = $photos;
            }

            $pengaduan = Pengaduan::create($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengaduan berhasil dikirim',
                'data' => [
                    'id' => $pengaduan->id,
                    'nomor_pengaduan' => 'PENG-' . str_pad($pengaduan->id, 6, '0', STR_PAD_LEFT),
                    'status' => $pengaduan->status
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim pengaduan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
