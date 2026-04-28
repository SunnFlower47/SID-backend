<?php

namespace App\Http\Controllers\ApiAdminPanel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\BantuanSosial;
use App\Models\PenerimaBantuanSosial;
use App\Models\Penduduk;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class BantuanSosialController extends Controller
{
    /**
     * Display a listing of social aid programs.
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('bantuan_sosial.view');

        $query = BantuanSosial::withCount('penerima');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_program', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $programs = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $programs,
            'meta' => [
                'stats' => [
                    'total_program' => BantuanSosial::count(),
                    'program_aktif' => BantuanSosial::where('status', 'aktif')->count(),
                    'total_penerima' => PenerimaBantuanSosial::count(),
                ]
            ]
        ]);
    }

    /**
     * Store a new program.
     */
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('bantuan_sosial.create');

        $validated = $request->validate([
            'nama_program' => 'required|string|max:255',
            'jenis_bantuan' => 'required|string|in:BLT,PKH,BPNT,Bansos Lainnya',
            'deskripsi' => 'required|string',
            'nilai_bantuan' => 'nullable|numeric|min:0',
            'periode' => 'required|string|max:50',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status' => 'required|in:aktif,selesai,ditangguhkan',
            'sumber_dana' => 'required|string|max:255',
        ]);

        $program = BantuanSosial::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Program Bansos berhasil dibuat',
            'data' => $program
        ], 201);
    }

    /**
     * Check recipient by NIK.
     */
    public function checkByNik(Request $request): JsonResponse
    {
        $request->validate(['nik' => 'required|string|size:16']);

        $penduduk = Penduduk::where('nik', $request->nik)->first();

        if (!$penduduk) {
            return response()->json(['status' => 'error', 'message' => 'NIK tidak ditemukan'], 404);
        }

        $bantuan = PenerimaBantuanSosial::with('bantuanSosial')
            ->where('penduduk_id', $penduduk->id)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'penduduk' => $penduduk,
                'history' => $bantuan
            ]
        ]);
    }

    /**
     * Add recipient to program.
     */
    public function addPenerima(Request $request, BantuanSosial $bantuanSosial): JsonResponse
    {
        Gate::authorize('bantuan_sosial.manage_penerima');

        $validated = $request->validate([
            'penduduk_id' => 'required|exists:penduduks,id',
            'status_penerimaan' => 'required|in:aktif,ditangguhkan,berhenti',
            'nilai_diterima' => 'nullable|numeric|min:0',
            'tanggal_penerimaan' => 'nullable|date',
        ]);

        // Check duplicate
        $exists = PenerimaBantuanSosial::where('bantuan_sosial_id', $bantuanSosial->id)
            ->where('penduduk_id', $validated['penduduk_id'])
            ->exists();

        if ($exists) {
            return response()->json(['status' => 'error', 'message' => 'Penduduk sudah terdaftar di program ini'], 422);
        }

        $penerima = $bantuanSosial->penerima()->create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Penerima berhasil ditambahkan',
            'data' => $penerima
        ], 201);
    }
}
