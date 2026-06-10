<?php

namespace App\Http\Controllers\Tenant\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\BukuEkspedisi;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BukuEkspedisiController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $query = BukuEkspedisi::query()->latest('tanggal_pengiriman');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('isi_singkat', 'like', "%{$search}%")
                  ->orWhere('tujuan', 'like', "%{$search}%")
                  ->orWhere('penerima', 'like', "%{$search}%");
            });
        }

        $bukuEkspedisi = $query->paginate(10)->withQueryString();

        return Inertia::render('Tenant/Sekretariat/BukuEkspedisi/Index', [
            'data' => $bukuEkspedisi,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Tenant/Sekretariat/BukuEkspedisi/Form', [
            'bukuEkspedisi' => new BukuEkspedisi([
                'tanggal_pengiriman' => date('Y-m-d'),
                'tanggal_surat' => date('Y-m-d')
            ]),
            'is_edit' => false
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_pengiriman' => 'required|date',
            'tanggal_surat' => 'required|date',
            'nomor_surat' => 'required|string|max:255',
            'isi_singkat' => 'required|string',
            'tujuan' => 'required|string|max:255',
            'penerima' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        BukuEkspedisi::create($validated);

        return redirect()->route('sekretariat.buku-ekspedisi.index')->with('success', 'Data buku ekspedisi berhasil ditambahkan.');
    }

    public function show(BukuEkspedisi $bukuEkspedisi)
    {
        return Inertia::render('Tenant/Sekretariat/BukuEkspedisi/Show', [
            'bukuEkspedisi' => $bukuEkspedisi
        ]);
    }

    public function edit(BukuEkspedisi $bukuEkspedisi)
    {
        return Inertia::render('Tenant/Sekretariat/BukuEkspedisi/Form', [
            'bukuEkspedisi' => $bukuEkspedisi,
            'is_edit' => true
        ]);
    }

    public function update(Request $request, BukuEkspedisi $bukuEkspedisi)
    {
        $validated = $request->validate([
            'tanggal_pengiriman' => 'required|date',
            'tanggal_surat' => 'required|date',
            'nomor_surat' => 'required|string|max:255',
            'isi_singkat' => 'required|string',
            'tujuan' => 'required|string|max:255',
            'penerima' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $bukuEkspedisi->update($validated);

        return redirect()->route('sekretariat.buku-ekspedisi.index')->with('success', 'Data buku ekspedisi berhasil diperbarui.');
    }

    public function destroy(BukuEkspedisi $bukuEkspedisi)
    {
        $bukuEkspedisi->delete();

        return redirect()->back()->with('success', 'Data buku ekspedisi berhasil dihapus.');
    }
}
