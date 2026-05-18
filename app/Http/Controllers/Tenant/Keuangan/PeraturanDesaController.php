<?php

namespace App\Http\Controllers\Tenant\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\PeraturanDesa;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class PeraturanDesaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:keuangan']);
    }

    /**
     * Halaman index persetujuan BPD
     */
    public function index(Request $request)
    {
        $query = PeraturanDesa::query()->orderBy('tahun_anggaran', 'desc')->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_peraturan', 'like', '%' . $request->search . '%');
        }

        if ($request->has('jenis')) {
            $query->where('jenis_peraturan', $request->jenis);
        }

        if ($request->has('tahun')) {
            $query->where('tahun_anggaran', $request->tahun);
        }

        $peraturans = $query->paginate(10)->withQueryString();

        return Inertia::render('Tenant/Keuangan/Peraturan/Index', [
            'peraturans' => $peraturans,
            'filters'    => $request->only(['search', 'jenis', 'tahun']),
        ]);
    }

    /**
     * Menyimpan draft pengajuan baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_peraturan' => 'required|in:APBDes,Perubahan APBDes,Lpj APBDes,Lainnya',
            'tahun_anggaran'  => 'required|integer|min:2020|max:2099',
            'judul'           => 'required|string|max:255',
        ]);

        // Cek apakah sudah ada untuk jenis dan tahun tersebut (hindari duplikat)
        $exists = PeraturanDesa::where('jenis_peraturan', $validated['jenis_peraturan'])
                               ->where('tahun_anggaran', $validated['tahun_anggaran'])
                               ->whereNotIn('status', ['ditolak'])
                               ->first();

        if ($exists) {
            return back()->with('error', 'Pengajuan untuk ' . $validated['jenis_peraturan'] . ' Tahun ' . $validated['tahun_anggaran'] . ' sudah ada dengan status ' . $exists->status);
        }

        PeraturanDesa::create([
            'jenis_peraturan' => $validated['jenis_peraturan'],
            'tahun_anggaran'  => $validated['tahun_anggaran'],
            'judul'           => $validated['judul'],
            'status'          => 'draft',
        ]);

        return back()->with('success', 'Draft pengajuan Peraturan Desa berhasil dibuat.');
    }

    /**
     * Update status & informasi penetapan
     */
    public function updateStatus(Request $request, $id)
    {
        $peraturan = PeraturanDesa::findOrFail($id);

        $validated = $request->validate([
            'status'             => 'required|in:draft,diajukan_bpd,dibahas,disetujui,ditolak',
            'keterangan_bpd'     => 'nullable|string',
            'nomor_peraturan'    => 'nullable|required_if:status,disetujui|string|max:100',
            'tanggal_ditetapkan' => 'nullable|required_if:status,disetujui|date',
        ]);

        $peraturan->status = $validated['status'];
        
        if ($request->has('keterangan_bpd')) {
            $peraturan->keterangan_bpd = $validated['keterangan_bpd'];
        }

        if ($validated['status'] === 'disetujui') {
            $peraturan->nomor_peraturan = $validated['nomor_peraturan'];
            $peraturan->tanggal_ditetapkan = $validated['tanggal_ditetapkan'];
        } else {
            // Jika dikembalikan ke draft/dibahas, bersihkan data sah
            if (in_array($validated['status'], ['draft', 'diajukan_bpd', 'dibahas'])) {
                $peraturan->nomor_peraturan = null;
                $peraturan->tanggal_ditetapkan = null;
            }
        }

        $peraturan->save();

        return back()->with('success', 'Status Peraturan Desa berhasil diperbarui.');
    }

    /**
     * Upload dokumen final (PDF)
     */
    public function uploadDokumen(Request $request, $id)
    {
        $peraturan = PeraturanDesa::findOrFail($id);

        $request->validate([
            'file_dokumen' => 'required|file|mimes:pdf|max:5120', // Max 5MB
        ]);

        if ($request->hasFile('file_dokumen')) {
            // Hapus file lama jika ada
            if ($peraturan->file_dokumen) {
                Storage::disk('public')->delete($peraturan->file_dokumen);
            }

            $file = $request->file('file_dokumen');
            $filename = 'PERDES_' . str_replace(' ', '_', $peraturan->jenis_peraturan) . '_' . $peraturan->tahun_anggaran . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('keuangan/peraturan', $filename, 'public');

            $peraturan->update(['file_dokumen' => $path]);

            return back()->with('success', 'Dokumen Peraturan Desa berhasil diunggah.');
        }

        return back()->with('error', 'Gagal mengunggah dokumen.');
    }

    /**
     * Hapus pengajuan
     */
    public function destroy($id)
    {
        $peraturan = PeraturanDesa::findOrFail($id);

        if ($peraturan->status === 'disetujui') {
            return back()->with('error', 'Peraturan Desa yang sudah disetujui tidak dapat dihapus. Silakan ubah statusnya terlebih dahulu jika ingin menghapus.');
        }

        if ($peraturan->file_dokumen) {
            Storage::disk('public')->delete($peraturan->file_dokumen);
        }

        $peraturan->delete();

        return back()->with('success', 'Pengajuan Peraturan Desa berhasil dihapus.');
    }
}
