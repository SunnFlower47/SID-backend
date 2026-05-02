<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuratPengajuan;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\SuratType;
use Exception;

class SuratPengajuanApiController extends Controller
{
    /**
     * Helper untuk format response publik yang SANGAT MINIMAL (Tanpa PII)
     */
    private function formatPublicResponse($surat)
    {
        return [
            'id' => $surat->id,
            'nomor_surat' => $surat->nomor_surat,
            'jenis_surat_nama' => $surat->suratType ? $surat->suratType->nama : $surat->jenis_surat,
            'status' => $surat->status,
            'keperluan' => $surat->keperluan,
            'keterangan_admin' => $surat->keterangan_admin,
            'tanggal_pengajuan' => $surat->created_at->toIso8601String(),
            'created_at' => $surat->created_at->toIso8601String(),
            'updated_at' => $surat->updated_at->toIso8601String(),
        ];
    }

    /**
     * Get list of available letter types
     */
    public function index()
    {
        $types = SuratType::where('is_active', true)
            ->orderBy('has_template', 'desc')
            ->orderBy('nama')
            ->get()
            ->map(function ($type) {
                return [
                    'id' => (string)$type->id,
                    'name' => $type->nama,
                    'description' => $type->deskripsi,
                    'persyaratan' => $type->persyaratan,
                    'has_template' => (bool)$type->has_template,
                    'template_code' => $type->template_code,
                    'icon' => $type->icon ?? 'fas fa-file-alt',
                    'color' => $type->color ?? 'blue',
                    'category' => $type->has_template ? 'Template' : 'Lainnya',
                    'form_json' => $type->form_json,
                ];
            });

        return response()->json(['success' => true, 'data' => $types]);
    }

    /**
     * Submit pengajuan surat
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'penduduk_id' => 'required|exists:penduduks,id',
            'nik' => 'required|string|size:16',
            'tanggal_lahir' => 'required|date',
            'surat_type' => 'required|string',
            'keperluan' => 'required|string|max:1000',
            'tujuan' => 'nullable|string|max:255',
            'tanggal_surat' => 'required|date',
            'email_pengaju' => 'nullable|email|max:255',
            'file_lampiran' => 'nullable|file|mimes:pdf|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        // Verifikasi Ganda: Pastikan ID, NIK, dan Tanggal Lahir MATCH
        $penduduk = Penduduk::where('id', $request->penduduk_id)
            ->where('nik', $request->nik)
            ->where('tanggal_lahir', $request->tanggal_lahir)
            ->first();

        if (!$penduduk) {
            return response()->json([
                'success' => false, 
                'message' => 'Verifikasi identitas gagal. Data NIK atau Tanggal Lahir tidak sesuai dengan database kami.'
            ], 403);
        }
        
        try {
            $filePath = null;
            if ($request->hasFile('file_lampiran')) {
                $file = $request->file('file_lampiran');
                $filename = time() . '_' . $request->surat_type . '_' . $penduduk->nik . '.pdf';
                $filePath = $file->storeAs('surat-pengajuan', $filename, 'public');
            }

            $dataTambahan = $request->data_tambahan;
            if (is_string($dataTambahan)) {
                $decoded = json_decode($dataTambahan, true);
                if (json_last_error() === JSON_ERROR_NONE) $dataTambahan = $decoded;
            }

            $suratPengajuan = SuratPengajuan::create([
                'jenis_surat' => $request->surat_type,
                'penduduk_id' => $penduduk->id,
                'nomor_surat' => $this->generateNomorSurat($request->surat_type),
                'keperluan' => $request->keperluan,
                'tujuan' => $request->tujuan,
                'tanggal_surat' => $request->tanggal_surat,
                'keterangan_tambahan' => $request->keterangan_tambahan,
                'data_tambahan' => $dataTambahan,
                'file_lampiran' => $filePath,
                'status' => 'pending',
                'nik_pengaju' => $penduduk->nik,
                'nama_pengaju' => $penduduk->nama,
                'email_pengaju' => $request->email_pengaju,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan surat berhasil dikirim',
                'data' => $this->formatPublicResponse($suratPengajuan)
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim pengajuan', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * VERIFIKASI NIK + TANGGAL LAHIR (Hanya menjawab True/False)
     * Tidak mengeluarkan data penduduk sama sekali!
     */
    public function checkNik(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|size:16',
            'tanggal_lahir' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Data tidak lengkap'], 400);
        }

        $exists = Penduduk::where('nik', $request->nik)
            ->where('tanggal_lahir', $request->tanggal_lahir)
            ->exists();

        if (!$exists) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan atau tidak sesuai'], 404);
        }

        // KEMBALIKAN TRUE SAJA. 
        // Identitas warga tetap aman di server.
        return response()->json([
            'success' => true,
            'message' => 'Verifikasi berhasil',
            'data' => [
                'id' => Penduduk::where('nik', $request->nik)->first()->id // Tetap kirim ID untuk proses form
            ]
        ]);
    }

    /**
     * Cek Status Berdasarkan Nomor Surat (Public)
     */
    public function checkStatus(Request $request)
    {
        $nomorSurat = $request->query('nomor') ?? $request->input('nomor_surat');

        if (!$nomorSurat) {
            return response()->json(['success' => false, 'message' => 'Nomor surat wajib diisi'], 400);
        }

        $pengajuan = SuratPengajuan::where('nomor_surat', $nomorSurat)->first();

        if (!$pengajuan) {
            return response()->json(['success' => false, 'message' => 'Surat tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [$this->formatPublicResponse($pengajuan)]
        ]);
    }

    /**
     * VERIFIKASI KEAMANAN TINGGI: Cek Riwayat Surat (POST)
     * Wajib NIK + Tanggal Lahir
     */
    public function getHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|size:16',
            'tanggal_lahir' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Format data tidak valid'], 422);
        }

        $penduduk = Penduduk::where('nik', $request->nik)
            ->where('tanggal_lahir', $request->tanggal_lahir)
            ->first();

        if (!$penduduk) {
            return response()->json(['success' => false, 'message' => 'Verifikasi gagal'], 403);
        }

        $pengajuans = SuratPengajuan::where('nik_pengaju', $request->nik)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pengajuans->map(fn($p) => $this->formatPublicResponse($p))
        ]);
    }

    private function generateNomorSurat($suratType)
    {
        $type = \App\Models\SuratType::find($suratType);
        $kodeSurat = $type ? $type->kode : 'SK';
        return \App\Models\DesaSetting::generateNomorSurat($kodeSurat);
    }
}
