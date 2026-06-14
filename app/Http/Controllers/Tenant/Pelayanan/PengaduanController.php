<?php

namespace App\Http\Controllers\Tenant\Pelayanan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengaduan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Requests\Pengaduan\StorePengaduanRequest;
use App\Http\Requests\Pengaduan\UpdatePengaduanRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\PengaduanReply;

class PengaduanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pengaduan::with('user');

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by prioritas
        if ($request->has('prioritas') && $request->prioritas) {
            $query->where('prioritas', $request->prioritas);
        }

        // Filter by kategori
        if ($request->has('kategori') && $request->kategori) {
            $query->where('kategori', $request->kategori);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('judul', 'like', "%{$request->search}%")
                  ->orWhere('deskripsi', 'like', "%{$request->search}%")
                  ->orWhere('nama_pelapor', 'like', "%{$request->search}%");
            });
        }

        $pengaduans = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Get statistics
        $stats = [
            'total'    => Pengaduan::count(),
            'baru'     => Pengaduan::where('status', 'baru')->count(),
            'diproses' => Pengaduan::where('status', 'diproses')->count(),
            'selesai'  => Pengaduan::where('status', 'selesai')->count(),
            'darurat'  => Pengaduan::where('prioritas', 'darurat')->count(),
        ];

        return Inertia::render('Tenant/Pengaduan/Index', [
            'pengaduans' => $pengaduans,
            'stats'      => $stats,
            'filters'    => $request->only(['search', 'status', 'prioritas', 'kategori']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Tenant/Pengaduan/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePengaduanRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
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

            Pengaduan::create($data);

            DB::commit();

            return redirect()->route('pengaduan.index')
                ->with('success', 'Pengaduan berhasil ditambahkan secara manual!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan pengaduan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pengaduan $pengaduan)
    {
        $pengaduan->load('user');

        return Inertia::render('Tenant/Pengaduan/Show', [
            'pengaduan' => $pengaduan,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pengaduan $pengaduan)
    {
        $pengaduan->load('user');

        return Inertia::render('Tenant/Pengaduan/Edit', [
            'pengaduan' => $pengaduan,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePengaduanRequest $request, Pengaduan $pengaduan)
    {
        try {
            // Cek jika sudah selesai/ditolak agar tidak double tanggapan/email
            if (in_array($pengaduan->status, ['selesai', 'ditolak'])) {
                return back()->with('error', 'Pengaduan ini sudah berstatus ' . $pengaduan->status . ' dan tidak dapat diubah lagi.');
            }

            DB::beginTransaction();

            $data = $request->validated();
            $data['user_id'] = Auth::id();

            if ($request->status === 'selesai' || $request->status === 'ditolak') {
                $data['tanggal_tanggapan'] = now();
            }

            $pengaduan->update($data);

            // Kirim Email jika ada alamat email pelapor
            Log::info('Mencoba kirim email pengaduan', [
                'email' => $pengaduan->email,
                'tanggapan_filled' => $request->filled('tanggapan')
            ]);

            if ($pengaduan->email && $request->filled('tanggapan')) {
                try {
                    Mail::to($pengaduan->email)->send(new PengaduanReply($pengaduan, $request->tanggapan));
                    Log::info('Email pengaduan berhasil dikirim ke antrean/mail.');
                } catch (\Exception $e) {
                    Log::error('Gagal kirim email pengaduan: ' . $e->getMessage());
                    report($e);
                }
            }

            DB::commit();

            return redirect()->route('pengaduan.index')
                ->with('success', 'Tanggapan pengaduan berhasil disimpan' . ($pengaduan->email ? ' dan email balasan telah dikirim.' : '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui pengaduan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pengaduan $pengaduan)
    {
        try {
            DB::beginTransaction();

            // Delete photos
            if ($pengaduan->foto) {
                foreach ($pengaduan->foto as $photo) {
                    Storage::disk('s3')->delete($photo);
                }
            }

            $pengaduan->delete();

            DB::commit();

            return redirect()->route('pengaduan.index')
                ->with('success', 'Pengaduan berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus pengaduan: ' . $e->getMessage());
        }
    }
}
