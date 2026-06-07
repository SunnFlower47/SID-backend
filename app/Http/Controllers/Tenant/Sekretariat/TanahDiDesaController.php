<?php

namespace App\Http\Controllers\Tenant\Sekretariat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TanahDiDesa;
use App\Models\TanahDiDesaMutasi;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class TanahDiDesaController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('tanah_di_desa.view');

        $query = TanahDiDesa::query()->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('nama_pemilik', 'like', "%{$request->search}%")
                  ->orWhere('nop', 'like', "%{$request->search}%")
                  ->orWhere('lokasi_tanah', 'like', "%{$request->search}%");
        }

        $tanahDiDesa = $query->paginate(10)->withQueryString()->through(function($item) {
            $totalLuas = $item->luas_sawah + $item->luas_tegalan + $item->luas_kebun + 
                         $item->luas_perumahan + $item->luas_industri + $item->luas_fasilitas_umum + $item->luas_lain_lain;
            $item->total_luas = $totalLuas;
            return $item;
        });

        return Inertia::render('Tenant/Sekretariat/TanahDiDesa/Index', [
            'tanahDiDesa' => $tanahDiDesa,
            'filters' => $request->only('search')
        ]);
    }

    public function create()
    {
        Gate::authorize('tanah_di_desa.create');

        return Inertia::render('Tenant/Sekretariat/TanahDiDesa/Form');
    }

    public function store(Request $request)
    {
        Gate::authorize('tanah_di_desa.create');

        $validated = $this->validateTanahRequest($request);
        $validated['created_by'] = auth()->id();

        TanahDiDesa::create($validated);

        return redirect()->route('tenant.sekretariat.tanah-di-desa.index')
            ->with('success', 'Data Tanah di Desa berhasil ditambahkan.');
    }

    public function show($id)
    {
        Gate::authorize('tanah_di_desa.view');

        $tanahDiDesa = TanahDiDesa::findOrFail($id);
        
        $mutasi = TanahDiDesaMutasi::where('tanah_di_desa_id', $id)
                    ->orderBy('tanggal_mutasi', 'desc')
                    ->get();
                    
        $totalLuas = $tanahDiDesa->luas_sawah + $tanahDiDesa->luas_tegalan + $tanahDiDesa->luas_kebun + 
                     $tanahDiDesa->luas_perumahan + $tanahDiDesa->luas_industri + $tanahDiDesa->luas_fasilitas_umum + $tanahDiDesa->luas_lain_lain;
        $tanahDiDesa->total_luas = $totalLuas;

        return Inertia::render('Tenant/Sekretariat/TanahDiDesa/Show', [
            'tanahDiDesa' => $tanahDiDesa,
            'mutasi' => $mutasi
        ]);
    }

    public function edit($id)
    {
        Gate::authorize('tanah_di_desa.edit');

        $tanahDiDesa = TanahDiDesa::findOrFail($id);
        return Inertia::render('Tenant/Sekretariat/TanahDiDesa/Form', [
            'tanahDiDesa' => $tanahDiDesa
        ]);
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('tanah_di_desa.edit');

        $validated = $this->validateTanahRequest($request);
        
        $tanahDiDesa = TanahDiDesa::findOrFail($id);
        $tanahDiDesa->update($validated);

        return redirect()->route('tenant.sekretariat.tanah-di-desa.index')
            ->with('success', 'Data Tanah di Desa berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Gate::authorize('tanah_di_desa.delete');

        $tanahDiDesa = TanahDiDesa::findOrFail($id);
        $tanahDiDesa->delete();

        return redirect()->route('tenant.sekretariat.tanah-di-desa.index')
            ->with('success', 'Data Tanah di Desa berhasil dihapus.');
    }
    
    public function storeMutasi(Request $request, $id)
    {
        Gate::authorize('tanah_di_desa.mutasi');

        $validated = $request->validate([
            'pemilik_lama' => 'required|string|max:255',
            'pemilik_baru' => 'required|string|max:255',
            'tanggal_mutasi' => 'required|date',
            'keterangan' => 'nullable|string',
            
            // Kolom update identitas & legalitas baru
            'tempat_lahir_baru' => 'nullable|string|max:255',
            'tanggal_lahir_baru' => 'nullable|date',
            'status_kepemilikan_baru' => 'nullable|string|max:255',
            'no_sertifikat_baru' => 'nullable|string|max:255',
            'tanggal_penerbitan_sertifikat_baru' => 'nullable|date',
        ]);
        
        $tanah = TanahDiDesa::findOrFail($id);
        
        DB::transaction(function() use ($tanah, $validated) {
            // Save mutasi log
            TanahDiDesaMutasi::create([
                'tanah_di_desa_id' => $tanah->id,
                'pemilik_lama' => $validated['pemilik_lama'],
                'pemilik_baru' => $validated['pemilik_baru'],
                'tanggal_mutasi' => $validated['tanggal_mutasi'],
                'keterangan' => $validated['keterangan'],
                'created_by' => auth()->id()
            ]);
            
            // Siapkan data update utama
            $updateData = [
                'nama_pemilik' => $validated['pemilik_baru'],
                'tanggal_perolehan' => $validated['tanggal_mutasi'], // Tanggal mutasi menjadi tanggal perolehan baru
            ];

            if (isset($validated['tempat_lahir_baru'])) $updateData['tempat_lahir_berdiri'] = $validated['tempat_lahir_baru'];
            if (isset($validated['tanggal_lahir_baru'])) $updateData['tanggal_lahir_berdiri'] = $validated['tanggal_lahir_baru'];
            if (isset($validated['status_kepemilikan_baru'])) $updateData['status_kepemilikan'] = $validated['status_kepemilikan_baru'];
            if (isset($validated['no_sertifikat_baru'])) $updateData['no_sertifikat'] = $validated['no_sertifikat_baru'];
            if (isset($validated['tanggal_penerbitan_sertifikat_baru'])) $updateData['tanggal_penerbitan_sertifikat'] = $validated['tanggal_penerbitan_sertifikat_baru'];
            
            $tanah->update($updateData);
        });
        
        return redirect()->route('tenant.sekretariat.tanah-di-desa.show', $id)
            ->with('success', 'Mutasi Tanah berhasil dicatat. Data kepemilikan telah diperbarui.');
    }

    private function validateTanahRequest(Request $request)
    {
        return $request->validate([
            'nop' => 'nullable|string|max:50',
            'nama_pemilik' => 'required|string|max:255',
            'tempat_lahir_berdiri' => 'nullable|string|max:255',
            'tanggal_lahir_berdiri' => 'nullable|date',
            'status_kepemilikan' => 'required|string|max:255',
            'tanggal_perolehan' => 'nullable|date',
            'no_sertifikat' => 'nullable|string|max:255',
            'tanggal_penerbitan_sertifikat' => 'nullable|date',
            'no_buku_c' => 'nullable|string|max:255',
            'no_persil' => 'nullable|string|max:255',
            'no_kelas' => 'nullable|string|max:255',
            
            'luas_sawah' => 'nullable|numeric|min:0',
            'luas_tegalan' => 'nullable|numeric|min:0',
            'luas_kebun' => 'nullable|numeric|min:0',
            
            'luas_perumahan' => 'nullable|numeric|min:0',
            'luas_industri' => 'nullable|numeric|min:0',
            'luas_fasilitas_umum' => 'nullable|numeric|min:0',
            'luas_lain_lain' => 'nullable|numeric|min:0',
            
            'lokasi_tanah' => 'nullable|string',
            'batas_utara' => 'nullable|string|max:255',
            'batas_timur' => 'nullable|string|max:255',
            'batas_selatan' => 'nullable|string|max:255',
            'batas_barat' => 'nullable|string|max:255',
            
            'keterangan' => 'nullable|string',
        ]);
    }
}
