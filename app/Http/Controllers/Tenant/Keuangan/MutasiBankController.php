<?php

namespace App\Http\Controllers\Tenant\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\MutasiBank;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MutasiBankController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $query = MutasiBank::query()->latest('tanggal_mutasi')->latest('id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('uraian', 'like', "%{$search}%")
                  ->orWhere('no_bukti', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('jenis_mutasi')) {
            $query->where('jenis_mutasi', $request->jenis_mutasi);
        }

        $mutasi = $query->paginate(15)->withQueryString();

        // Calculate total saldo up to now for overview
        $totalPenerimaan = MutasiBank::where('jenis_mutasi', 'masuk')->sum('jumlah');
        $totalPengeluaran = MutasiBank::where('jenis_mutasi', 'keluar')->sum('jumlah');
        $saldoBank = $totalPenerimaan - $totalPengeluaran;

        return Inertia::render('Tenant/Keuangan/MutasiBank/Index', [
            'data' => $mutasi,
            'filters' => $request->only(['search', 'jenis_mutasi']),
            'summary' => [
                'total_penerimaan' => $totalPenerimaan,
                'total_pengeluaran' => $totalPengeluaran,
                'saldo' => $saldoBank
            ]
        ]);
    }

    public function create()
    {
        return Inertia::render('Tenant/Keuangan/MutasiBank/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_mutasi' => 'required|date',
            'jenis_mutasi' => 'required|in:masuk,keluar',
            'uraian' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'no_bukti' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = auth()->id();

        MutasiBank::create($validated);

        return redirect()->route('keuangan.mutasi-bank.index')->with('success', 'Transaksi bank berhasil ditambahkan.');
    }

    public function edit(MutasiBank $mutasiBank)
    {
        return Inertia::render('Tenant/Keuangan/MutasiBank/Edit', [
            'mutasiBank' => $mutasiBank
        ]);
    }

    public function update(Request $request, MutasiBank $mutasiBank)
    {
        $validated = $request->validate([
            'tanggal_mutasi' => 'required|date',
            'jenis_mutasi' => 'required|in:masuk,keluar',
            'uraian' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'no_bukti' => 'nullable|string|max:255',
        ]);

        $mutasiBank->update($validated);

        return redirect()->route('keuangan.mutasi-bank.index')->with('success', 'Transaksi bank berhasil diperbarui.');
    }

    public function destroy(MutasiBank $mutasiBank)
    {
        $mutasiBank->delete();

        return redirect()->back()->with('success', 'Transaksi bank berhasil dihapus.');
    }
}
