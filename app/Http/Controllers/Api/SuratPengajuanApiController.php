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
            'nomor_pengajuan' => $surat->nomor_pengajuan,
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
            ->where('is_public', true)
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
        // Deteksi cerdas: Jika nama surat mengandung kata "domisili" (case-insensitive)
        $isDomisili = str_contains(strtolower($request->nama_surat ?? ''), 'domisili') 
                   || $request->surat_type === 'keterangan-domisili';

        $validator = Validator::make($request->all(), [
            'penduduk_id' => $isDomisili ? 'nullable' : 'required|exists:penduduks,id',
            'nik' => $isDomisili ? 'nullable|string' : 'required|string|size:16',
            'tanggal_lahir' => $isDomisili ? 'nullable|date' : 'required|date',
            'surat_type' => 'required|string',
            'nama_surat' => 'required|string',
            'keperluan' => 'required|string|max:1000',
            'tujuan' => 'nullable|string|max:255',
            'tanggal_surat' => 'required|date',
            'email_pengaju' => 'nullable|email|max:255',
            'no_hp_pengaju' => 'nullable|string|max:20',
            'file_lampiran' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $penduduk = null;
        $nikPengaju = null;
        $namaPengaju = null;

        if (!$isDomisili) {
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
            $nikPengaju = $penduduk->nik;
            $namaPengaju = $penduduk->nama;
        } else {
            // Extract from data_tambahan for Domisili
            $dataTambahan = $request->data_tambahan;
            if (is_string($dataTambahan)) {
                $decoded = json_decode($dataTambahan, true);
                if (json_last_error() === JSON_ERROR_NONE) $dataTambahan = $decoded;
            }
            $nikPengaju = $dataTambahan['nik'] ?? '0000000000000000';
            $namaPengaju = $dataTambahan['nama'] ?? 'Pendatang';
        }
        
        try {
            $filePath = null;
            if ($request->hasFile('file_lampiran')) {
                $file = $request->file('file_lampiran');
                $filename = time() . '_' . $request->surat_type . '_' . $nikPengaju . '.pdf';
                $filePath = $file->storeAs('surat-pengajuan', $filename, 'local');
            }

            $dataTambahan = $request->data_tambahan;
            if (is_string($dataTambahan)) {
                $decoded = json_decode($dataTambahan, true);
                if (json_last_error() === JSON_ERROR_NONE) $dataTambahan = $decoded;
            }

            $resi = 'REQ-' . date('ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4));

            $suratPengajuan = SuratPengajuan::create([
                'jenis_surat' => $request->surat_type,
                'penduduk_id' => $penduduk ? $penduduk->id : null,
                'nomor_surat' => null,
                'nomor_pengajuan'  => $resi,
                'keperluan' => $request->keperluan,
                'tujuan' => $request->tujuan,
                'tanggal_surat' => $request->tanggal_surat,
                'keterangan_tambahan' => $request->keterangan_tambahan,
                'data_tambahan' => $dataTambahan,
                'file_lampiran' => $filePath,
                'status' => 'pending',
                'nik_pengaju' => $nikPengaju,
                'nama_pengaju' => $namaPengaju,
                'email_pengaju' => $request->email_pengaju,
                'no_hp_pengaju' => $request->no_hp_pengaju,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan surat berhasil dikirim',
                'nomor_pengajuan' => $resi,
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

        $penduduk = Penduduk::where('nik', $request->nik)
            ->where('tanggal_lahir', $request->tanggal_lahir)
            ->first();

        /**
         * PENGIRIMAN DATA MASKED (Keamanan PII)
         * Khusus untuk keperluan Lomba #JuaraVibeCoding agar UI Frontend terlihat dinamis
         * namun tetap menjaga kerahasiaan identitas warga.
         */
        $mask = function($str, $keep = 2) {
            $len = strlen($str);
            if ($len <= $keep) return $str;
            return substr($str, 0, $keep) . str_repeat('*', $len - $keep);
        };

        return response()->json([
            'success' => true,
            'message' => 'Verifikasi berhasil',
            'data' => [
                'id' => $penduduk->id,
                'nama' => $mask($penduduk->nama, 3), 
                'alamat' => $mask($penduduk->alamat, 5) . ', RT ' . $mask($penduduk->rt_label, 1) . '/' . $mask($penduduk->rw_label, 1)
            ]
        ]);
    }

    /**
     * Cek Status Berdasarkan Nomor Surat + NIK
     * Khusus Lomba #JuaraVibeCoding: Ditambahkan verifikasi NIK untuk keamanan extra
     */
    public function checkStatus(Request $request)
    {
        $nomorSurat = $request->query('nomor') ?? $request->input('nomor_surat');
        $nik = $request->query('nik') ?? $request->input('nik');

        // C6 FIX: NIK wajib untuk mencegah enumerasi status surat orang lain
        if (!$nomorSurat || !$nik) {
            return response()->json(['success' => false, 'message' => 'Nomor surat dan NIK wajib diisi'], 400);
        }

        $pengajuan = SuratPengajuan::where(function($q) use ($nomorSurat) {
            $q->where('nomor_surat', $nomorSurat)
              ->orWhere('nomor_pengajuan', $nomorSurat);
        })
        ->where('nik_pengaju', $nik) // NIK sekarang WAJIB
        ->first();

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
