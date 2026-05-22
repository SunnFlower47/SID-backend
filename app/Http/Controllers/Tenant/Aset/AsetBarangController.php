<?php

namespace App\Http\Controllers\Tenant\Aset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Aset\StoreAsetBarangRequest;
use App\Http\Requests\Aset\UpdateAsetBarangRequest;
use App\Models\AsetBarang;
use App\Models\AsetKategori;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AsetBarangController extends Controller
{
    public function index(Request $request)
    {
        $query = AsetBarang::with('kategori')->ordered();

        if ($request->filled('kategori_id')) {
            $query->where('aset_kategori_id', $request->kategori_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_barang', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%");
            });
        }

        return Inertia::render('Tenant/Aset/MasterBarang/Index', [
            'barangs'   => $query->paginate(50)->withQueryString(),
            'kategoris' => AsetKategori::ordered()->get(),
            'filters'   => $request->only(['search', 'kategori_id']),
        ]);
    }

    public function store(StoreAsetBarangRequest $request)
    {
        AsetBarang::create($request->validated());
        return back()->with('success', 'Kode barang berhasil ditambahkan.');
    }

    public function update(UpdateAsetBarangRequest $request, AsetBarang $asetBarang)
    {
        $asetBarang->update($request->validated());
        return back()->with('success', 'Kode barang berhasil diperbarui.');
    }

    public function destroy(AsetBarang $asetBarang)
    {
        if ($asetBarang->inventaris()->exists()) {
            return back()->with('error', 'Kode barang tidak dapat dihapus karena sudah digunakan di inventaris.');
        }
        $asetBarang->delete();
        return back()->with('success', 'Kode barang berhasil dihapus.');
    }
}
