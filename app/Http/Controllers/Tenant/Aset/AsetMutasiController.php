<?php

namespace App\Http\Controllers\Tenant\Aset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Aset\StoreAsetMutasiRequest;
use App\Models\AsetInventaris;
use App\Models\AsetMutasi;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AsetMutasiController extends Controller
{
    /**
     * Form tambah mutasi ke aset existing.
     */
    public function create(Request $request, AsetInventaris $inventaris)
    {
        $inventaris->load('barang.kategori', 'mutasis');

        return Inertia::render('Tenant/Aset/TambahMutasi', [
            'inventaris' => [
                'id'              => $inventaris->id,
                'nama_display'    => $inventaris->nama_display,
                'satuan'          => $inventaris->satuan,
                'kondisi'         => $inventaris->kondisi,
                'barang'          => $inventaris->barang,
                'saldo_kwantitas' => $inventaris->saldo_kwantitas,
                'saldo_nilai'     => $inventaris->saldo_nilai,
            ],
            'tahun'    => (int) ($request->tahun    ?? now()->year),
            'semester' => (int) ($request->semester ?? (now()->month <= 6 ? 1 : 2)),
        ]);
    }

    /**
     * Simpan mutasi baru (tambah atau kurang).
     * Validasi saldo melebihi sudah ditangani di StoreAsetMutasiRequest::withValidator().
     */
    public function store(StoreAsetMutasiRequest $request, AsetInventaris $inventaris)
    {
        $validated = $request->validated();

        AsetMutasi::create([
            'aset_inventaris_id' => $inventaris->id,
            'tahun'              => $validated['tahun'],
            'semester'           => $validated['semester'],
            'tanggal'            => $validated['tanggal'],
            'jenis'              => $validated['jenis'],
            'kwantitas'          => $validated['kwantitas'],
            'nilai'              => $validated['nilai'],
            'keterangan'         => $validated['keterangan'] ?? null,
        ]);

        // Update kondisi fisik aset jika diisi
        if (!empty($validated['kondisi'])) {
            $inventaris->update(['kondisi' => $validated['kondisi']]);
        }

        $jenis    = $validated['jenis'] === 'tambah' ? 'Penambahan' : 'Pengurangan';
        $tahun    = $validated['tahun'];
        $semester = $validated['semester'];

        return redirect()
            ->route('aset.inventaris.index', compact('tahun', 'semester'))
            ->with('success', "{$jenis} aset \"{$inventaris->nama_display}\" berhasil dicatat.");
    }

    /**
     * Hapus satu record mutasi.
     */
    public function destroy(AsetMutasi $mutasi)
    {
        $nama = $mutasi->inventaris?->nama_display ?? 'aset';
        $mutasi->delete();

        return back()->with('success', "Mutasi {$nama} berhasil dihapus.");
    }
}
