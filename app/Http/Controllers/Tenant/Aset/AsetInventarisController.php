<?php

namespace App\Http\Controllers\Tenant\Aset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Aset\StoreAsetInventarisRequest;
use App\Http\Requests\Aset\UpdateAsetInventarisRequest;
use App\Models\AsetInventaris;
use App\Models\AsetMutasi;
use App\Models\AsetKategori;
use App\Services\Aset\AsetInventarisService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AsetInventarisController extends Controller
{
    // ── Buku Inventaris (per periode) ────────────────────────────────────────

    public function index(Request $request)
    {
        $tahun    = (int) ($request->tahun    ?? now()->year);
        $semester = (int) ($request->semester ?? (now()->month <= 6 ? 1 : 2));

        $grouped    = AsetInventarisService::getGroupedByKategori($tahun, $semester);
        $grandTotal = AsetInventarisService::getGrandTotal($grouped);
        $tahunList  = AsetInventarisService::getTahunList();

        return Inertia::render('Tenant/Aset/Index', [
            'grouped'    => $grouped,
            'grandTotal' => $grandTotal,
            'tahun'      => $tahun,
            'semester'   => $semester,
            'tahunList'  => $tahunList,
        ]);
    }

    // ── Tambah Aset Baru ─────────────────────────────────────────────────────

    public function create(Request $request)
    {
        return Inertia::render('Tenant/Aset/Create', [
            'kategoris' => AsetKategori::ordered()->with('barangs')->get(),
            'tahun'     => (int) ($request->tahun    ?? now()->year),
            'semester'  => (int) ($request->semester ?? (now()->month <= 6 ? 1 : 2)),
        ]);
    }

    public function store(StoreAsetInventarisRequest $request)
    {
        $validated = $request->validated();

        // Buat data aset permanen
        $inventaris = AsetInventaris::create([
            'aset_barang_id'       => $validated['aset_barang_id'],
            'nama_barang_override' => $validated['nama_barang_override'],
            'satuan'               => $validated['satuan'],
            'kondisi'              => $validated['kondisi'],
            'lokasi'               => $validated['lokasi'] ?? null,
            'tanggal_perolehan'    => $validated['tanggal_perolehan'] ?? null,
            'asal_usul'            => $validated['asal_usul'],
            'keterangan'           => $validated['keterangan'] ?? null,
            'no_polisi'            => $validated['no_polisi'] ?? null,
            'no_mesin'             => $validated['no_mesin'] ?? null,
            'no_rangka'            => $validated['no_rangka'] ?? null,
            'no_bpkb'              => $validated['no_bpkb'] ?? null,
            'no_sertifikat'        => $validated['no_sertifikat'] ?? null,
        ]);

        // Otomatis buat mutasi "tambah" pertama (perolehan awal)
        AsetMutasi::create([
            'aset_inventaris_id' => $inventaris->id,
            'tahun'              => $validated['tahun'],
            'semester'           => $validated['semester'],
            'tanggal'            => $validated['tanggal'],
            'jenis'              => 'tambah',
            'kwantitas'          => $validated['kwantitas'],
            'nilai'              => $validated['nilai'],
            'keterangan'         => $validated['keterangan_mutasi']
                                 ?? 'Perolehan awal / ' . $validated['asal_usul'],
        ]);

        return redirect()
            ->route('aset.inventaris.index', [
                'tahun'    => $validated['tahun'],
                'semester' => $validated['semester'],
            ])
            ->with('success', 'Aset baru berhasil ditambahkan.');
    }

    // ── Edit Data Aset ───────────────────────────────────────────────────────

    public function edit(AsetInventaris $inventaris)
    {
        $inventaris->load('barang.kategori', 'mutasis');

        return Inertia::render('Tenant/Aset/Edit', [
            'inventaris' => [
                'id'                   => $inventaris->id,
                'aset_barang_id'       => $inventaris->aset_barang_id,
                'nama_barang_override' => $inventaris->nama_barang_override,
                'satuan'               => $inventaris->satuan,
                'kondisi'              => $inventaris->kondisi,
                'lokasi'               => $inventaris->lokasi,
                'tanggal_perolehan'    => $inventaris->tanggal_perolehan?->format('Y-m-d'),
                'asal_usul'            => $inventaris->asal_usul,
                'keterangan'           => $inventaris->keterangan,
                'no_polisi'            => $inventaris->no_polisi,
                'no_mesin'             => $inventaris->no_mesin,
                'no_rangka'            => $inventaris->no_rangka,
                'no_bpkb'              => $inventaris->no_bpkb,
                'no_sertifikat'        => $inventaris->no_sertifikat,
                'nama_display'         => $inventaris->nama_display,
                'barang'               => $inventaris->barang,
                'saldo_kwantitas'      => $inventaris->saldo_kwantitas,
                'saldo_nilai'          => $inventaris->saldo_nilai,
            ],
            'kategoris' => AsetKategori::ordered()->with('barangs')->get(),
        ]);
    }

    public function update(UpdateAsetInventarisRequest $request, AsetInventaris $inventaris)
    {
        $inventaris->update($request->validated());

        // ✅ Redirect ke index, bukan back() — agar tidak stuck di form edit
        return redirect()
            ->route('aset.inventaris.index')
            ->with('success', "Data aset \"{$inventaris->nama_display}\" berhasil diperbarui.");
    }

    // ── Hapus Aset ───────────────────────────────────────────────────────────

    public function destroy(AsetInventaris $inventaris)
    {
        $nama = $inventaris->nama_display;
        $inventaris->delete(); // cascadeOnDelete otomatis hapus mutasis

        return redirect()
            ->route('aset.inventaris.index')
            ->with('success', "Aset \"{$nama}\" berhasil dihapus.");
    }
}
