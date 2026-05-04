<?php

namespace App\Http\Controllers\Tenant\Pelayanan;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\BantuanSosial;
use App\Models\PenerimaBantuanSosial;
use App\Models\Penduduk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class BantuanSosialController extends Controller
{
        public function __construct()
    {
        $this->middleware(['auth', 'can:pelayanan_informasi']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BantuanSosial::withCount('penerima');

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by jenis bantuan
        if ($request->has('jenis_bantuan') && $request->jenis_bantuan) {
            $query->where('jenis_bantuan', $request->jenis_bantuan);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama_program', 'like', "%{$request->search}%")
                  ->orWhere('deskripsi', 'like', "%{$request->search}%");
            });
        }

        $bantuanSosials = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get statistics
        $stats = [
            'total_program' => BantuanSosial::count(),
            'program_aktif' => BantuanSosial::where('status', 'aktif')->count(),
            'total_penerima' => PenerimaBantuanSosial::count(),
            'penerima_aktif' => PenerimaBantuanSosial::where('status_penerimaan', 'aktif')->count(),
        ];

        return view('bantuan-sosial.index', compact('bantuanSosials', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('pelayanan_informasi');

        return view('bantuan-sosial.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('pelayanan_informasi');

        $validator = Validator::make($request->all(), [
            'nama_program' => 'required|string|max:255',
            'jenis_bantuan' => 'required|string|in:BLT,PKH,BPNT,Bansos Lainnya',
            'deskripsi' => 'required|string',
            'nilai_bantuan' => 'nullable|numeric|min:0',
            'periode' => 'required|string|max:50',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status' => 'required|in:aktif,selesai,ditangguhkan',
            'kriteria_penerima' => 'required',
            'sumber_dana' => 'required|string|max:255',
            'kuota_penerima' => 'nullable|integer|min:0'
        ], [
            'nama_program.required' => 'Nama program wajib diisi.',
            'jenis_bantuan.required' => 'Jenis bantuan wajib dipilih.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'periode.required' => 'Periode wajib diisi.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'status.required' => 'Status wajib dipilih.',
            'kriteria_penerima.required' => 'Kriteria penerima wajib diisi.',
            'sumber_dana.required' => 'Sumber dana wajib diisi.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            BantuanSosial::create($request->all());

            DB::commit();

            return redirect()->route('bantuan-sosial.index')
                ->with('success', 'Program bantuan sosial berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan program bantuan sosial: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BantuanSosial $bantuanSosial)
    {
        $bantuanSosial->load(['penerima.penduduk']);

        return view('bantuan-sosial.show', compact('bantuanSosial'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BantuanSosial $bantuanSosial)
    {
        Gate::authorize('pelayanan_informasi');

        return view('bantuan-sosial.edit', compact('bantuanSosial'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BantuanSosial $bantuanSosial)
    {
        Gate::authorize('pelayanan_informasi');

        $validator = Validator::make($request->all(), [
            'nama_program' => 'required|string|max:255',
            'jenis_bantuan' => 'required|string|in:BLT,PKH,BPNT,Bansos Lainnya',
            'deskripsi' => 'required|string',
            'nilai_bantuan' => 'nullable|numeric|min:0',
            'periode' => 'required|string|max:50',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status' => 'required|in:aktif,selesai,ditangguhkan',
            'kriteria_penerima' => 'required',
            'sumber_dana' => 'required|string|max:255',
            'kuota_penerima' => 'nullable|integer|min:0'
        ], [
            'nama_program.required' => 'Nama program wajib diisi.',
            'jenis_bantuan.required' => 'Jenis bantuan wajib dipilih.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'periode.required' => 'Periode wajib diisi.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'status.required' => 'Status wajib dipilih.',
            'kriteria_penerima.required' => 'Kriteria penerima wajib diisi.',
            'sumber_dana.required' => 'Sumber dana wajib diisi.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $bantuanSosial->update($request->all());

            DB::commit();

            return redirect()->route('bantuan-sosial.index')
                ->with('success', 'Program bantuan sosial berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui program bantuan sosial: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BantuanSosial $bantuanSosial)
    {
        Gate::authorize('pelayanan_informasi');

        try {
            DB::beginTransaction();

            $bantuanSosial->delete();

            DB::commit();

            return redirect()->route('bantuan-sosial.index')
                ->with('success', 'Program bantuan sosial berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghapus program bantuan sosial: ' . $e->getMessage());
        }
    }

    /**
     * Check bantuan sosial by NIK
     */
    public function checkByNik(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|size:16'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'NIK harus 16 digit',
                'errors' => $validator->errors()
            ], 400);
        }

        $penduduk = Penduduk::where('nik', $request->nik)->first();

        if (!$penduduk) {
            return response()->json([
                'success' => false,
                'message' => 'NIK tidak ditemukan'
            ], 404);
        }

        $bantuanSosials = PenerimaBantuanSosial::with('bantuanSosial')
            ->where('penduduk_id', $penduduk->id)
            ->where('status_penerimaan', 'aktif')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'penduduk' => $penduduk,
                'bantuan_sosials' => $bantuanSosials
            ]
        ]);
    }

    /**
     * Display a listing of penerima for specific bantuan sosial
     */
    public function penerimaIndex(BantuanSosial $bantuanSosial)
    {
        Gate::authorize('pelayanan_informasi');

        $penerima = $bantuanSosial->penerima()->with('penduduk')->paginate(20);

        return view('bantuan-sosial.penerima.index', compact('bantuanSosial', 'penerima'));
    }

    /**
     * Show the form for creating a new penerima
     */
    public function penerimaCreate(BantuanSosial $bantuanSosial)
    {
        Gate::authorize('pelayanan_informasi');

        return view('bantuan-sosial.penerima.create', compact('bantuanSosial'));
    }

    /**
     * Store a newly created penerima
     */
    public function penerimaStore(Request $request, BantuanSosial $bantuanSosial)
    {
        Gate::authorize('pelayanan_informasi');

        $validator = Validator::make($request->all(), [
            'penduduk_id' => 'required|exists:penduduks,id',
            'sistem_pembayaran' => 'required|in:sekali,triwulanan',
            'nilai_diterima' => 'required_if:sistem_pembayaran,sekali|numeric|min:0',
            'nilai_total_triwulanan' => 'required_if:sistem_pembayaran,triwulanan|numeric|min:0',
            'tanggal_penerimaan' => 'required_if:sistem_pembayaran,sekali|date',
            'tanggal_triwulan_1' => 'required_if:sistem_pembayaran,triwulanan|date',
            'tanggal_triwulan_2' => 'required_if:sistem_pembayaran,triwulanan|date',
            'tanggal_triwulan_3' => 'required_if:sistem_pembayaran,triwulanan|date',
            'status_penerimaan' => 'required|in:aktif,ditangguhkan,berhenti'
        ]);

        // Custom validation: check if penduduk already exists for this bantuan sosial
        $validator->after(function ($validator) use ($request, $bantuanSosial) {
            $existingPenerima = PenerimaBantuanSosial::where('bantuan_sosial_id', $bantuanSosial->id)
                ->where('penduduk_id', $request->penduduk_id)
                ->first();

            if ($existingPenerima) {
                $penduduk = Penduduk::find($request->penduduk_id);
                $validator->errors()->add('penduduk_id',
                    "Penduduk {$penduduk->nama} (NIK: {$penduduk->nik}) sudah terdaftar sebagai penerima bantuan sosial '{$bantuanSosial->nama_program}' periode {$bantuanSosial->periode}."
                );
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            if ($request->sistem_pembayaran === 'sekali') {
                // Sistem pembayaran sekali
                $bantuanSosial->penerima()->create([
                    'penduduk_id' => $request->penduduk_id,
                    'nilai_diterima' => $request->nilai_diterima,
                    'tanggal_penerimaan' => $request->tanggal_penerimaan,
                    'status_penerimaan' => $request->status_penerimaan,
                    'keterangan' => $request->keterangan ?? null,
                    'data_tambahan' => json_encode(['sistem_pembayaran' => 'sekali'])
                ]);
            } else {
                // Sistem pembayaran triwulanan - 1 record dengan 3 tanggal
                $totalAmount = $request->nilai_total_triwulanan;
                $perTriwulan = floor($totalAmount / 3);
                $remainder = $totalAmount % 3;

                $triwulan1Amount = $perTriwulan + ($remainder >= 1 ? 1 : 0);
                $triwulan2Amount = $perTriwulan + ($remainder >= 2 ? 1 : 0);
                $triwulan3Amount = $perTriwulan;

                $bantuanSosial->penerima()->create([
                    'penduduk_id' => $request->penduduk_id,
                    'nilai_diterima' => $totalAmount, // Total amount
                    'tanggal_penerimaan' => $request->tanggal_triwulan_1, // Tanggal pertama
                    'status_penerimaan' => $request->status_penerimaan,
                    'keterangan' => $request->keterangan ?? null,
                    'data_tambahan' => json_encode([
                        'sistem_pembayaran' => 'triwulanan',
                        'total_amount' => $totalAmount,
                        'triwulan_1' => [
                            'tanggal' => $request->tanggal_triwulan_1,
                            'jumlah' => $triwulan1Amount
                        ],
                        'triwulan_2' => [
                            'tanggal' => $request->tanggal_triwulan_2,
                            'jumlah' => $triwulan2Amount
                        ],
                        'triwulan_3' => [
                            'tanggal' => $request->tanggal_triwulan_3,
                            'jumlah' => $triwulan3Amount
                        ]
                    ])
                ]);
            }

            DB::commit();

            $message = $request->sistem_pembayaran === 'sekali'
                ? 'Penerima bantuan berhasil ditambahkan!'
                : 'Penerima bantuan triwulanan berhasil ditambahkan!';

            return redirect()->route('bantuan-sosial.penerima.index', $bantuanSosial)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan penerima bantuan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified penerima
     */
    public function penerimaShow(BantuanSosial $bantuanSosial, PenerimaBantuanSosial $penerima)
    {
        Gate::authorize('pelayanan_informasi');

        $penerima->load('penduduk');

        return view('bantuan-sosial.penerima.show', compact('bantuanSosial', 'penerima'));
    }

    /**
     * Show the form for editing the specified penerima
     */
    public function penerimaEdit(BantuanSosial $bantuanSosial, PenerimaBantuanSosial $penerima)
    {
        Gate::authorize('pelayanan_informasi');

        return view('bantuan-sosial.penerima.edit', compact('bantuanSosial', 'penerima'));
    }

    /**
     * Update the specified penerima
     */
    public function penerimaUpdate(Request $request, BantuanSosial $bantuanSosial, PenerimaBantuanSosial $penerima)
    {
        Gate::authorize('pelayanan_informasi');

        $validator = Validator::make($request->all(), [
            'penduduk_id' => 'required|exists:penduduks,id',
            'sistem_pembayaran' => 'required|in:sekali,triwulanan',
            'nilai_diterima' => 'required_if:sistem_pembayaran,sekali|numeric|min:0',
            'nilai_total_triwulanan' => 'required_if:sistem_pembayaran,triwulanan|numeric|min:0',
            'tanggal_penerimaan' => 'required_if:sistem_pembayaran,sekali|date',
            'tanggal_triwulan_1' => 'required_if:sistem_pembayaran,triwulanan|date',
            'tanggal_triwulan_2' => 'required_if:sistem_pembayaran,triwulanan|date',
            'tanggal_triwulan_3' => 'required_if:sistem_pembayaran,triwulanan|date',
            'status_penerimaan' => 'required|in:aktif,ditangguhkan,berhenti'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            if ($request->sistem_pembayaran === 'sekali') {
                // Sistem pembayaran sekali
                $penerima->update([
                    'penduduk_id' => $request->penduduk_id,
                    'nilai_diterima' => $request->nilai_diterima,
                    'tanggal_penerimaan' => $request->tanggal_penerimaan,
                    'status_penerimaan' => $request->status_penerimaan,
                    'keterangan' => $request->keterangan ?? null,
                    'data_tambahan' => json_encode(['sistem_pembayaran' => 'sekali'])
                ]);
            } else {
                // Sistem pembayaran triwulanan - 1 record dengan 3 tanggal
                $totalAmount = $request->nilai_total_triwulanan;
                $perTriwulan = floor($totalAmount / 3);
                $remainder = $totalAmount % 3;

                $triwulan1Amount = $perTriwulan + ($remainder >= 1 ? 1 : 0);
                $triwulan2Amount = $perTriwulan + ($remainder >= 2 ? 1 : 0);
                $triwulan3Amount = $perTriwulan;

                $penerima->update([
                    'penduduk_id' => $request->penduduk_id,
                    'nilai_diterima' => $totalAmount, // Total amount
                    'tanggal_penerimaan' => $request->tanggal_triwulan_1, // Tanggal pertama
                    'status_penerimaan' => $request->status_penerimaan,
                    'keterangan' => $request->keterangan ?? null,
                    'data_tambahan' => json_encode([
                        'sistem_pembayaran' => 'triwulanan',
                        'total_amount' => $totalAmount,
                        'triwulan_1' => [
                            'tanggal' => $request->tanggal_triwulan_1,
                            'jumlah' => $triwulan1Amount
                        ],
                        'triwulan_2' => [
                            'tanggal' => $request->tanggal_triwulan_2,
                            'jumlah' => $triwulan2Amount
                        ],
                        'triwulan_3' => [
                            'tanggal' => $request->tanggal_triwulan_3,
                            'jumlah' => $triwulan3Amount
                        ]
                    ])
                ]);
            }

            DB::commit();

            $message = $request->sistem_pembayaran === 'sekali'
                ? 'Penerima bantuan berhasil diperbarui!'
                : 'Penerima bantuan triwulanan berhasil diperbarui!';

            return redirect()->route('bantuan-sosial.penerima.index', $bantuanSosial)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui penerima bantuan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified penerima
     */
    public function penerimaDestroy(BantuanSosial $bantuanSosial, PenerimaBantuanSosial $penerima)
    {
        Gate::authorize('pelayanan_informasi');

        try {
            DB::beginTransaction();

            $penerima->delete();

            DB::commit();

            return redirect()->route('bantuan-sosial.penerima.index', $bantuanSosial)
                ->with('success', 'Penerima bantuan berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghapus penerima bantuan: ' . $e->getMessage());
        }
    }
}
