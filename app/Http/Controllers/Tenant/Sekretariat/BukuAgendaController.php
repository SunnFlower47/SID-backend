<?php

namespace App\Http\Controllers\Tenant\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\BukuAgenda;
use App\Http\Requests\Sekretariat\BukuAgendaRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BukuAgendaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BukuAgenda::query()->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('nomor_surat', 'like', '%' . $request->search . '%')
                  ->orWhere('pengirim_penerima', 'like', '%' . $request->search . '%')
                  ->orWhere('isi_singkat', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('jenis') && $request->jenis != '') {
            $query->where('jenis_surat', $request->jenis);
        }

        $agendas = $query->paginate(15)->withQueryString();

        return Inertia::render('Tenant/Sekretariat/BukuAgenda/Index', [
            'agendas' => $agendas,
            'filters' => $request->only(['search', 'jenis']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $jenis = $request->query('jenis', 'Masuk');
        
        return Inertia::render('Tenant/Sekretariat/BukuAgenda/Form', [
            'agenda' => new BukuAgenda([
                'tanggal' => date('Y-m-d'),
                'tanggal_surat' => date('Y-m-d'),
                'jenis_surat' => $jenis
            ]),
            'is_edit' => false
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BukuAgendaRequest $request)
    {
        BukuAgenda::create($request->validated());

        return redirect()->route('sekretariat.buku-agenda.index', ['jenis' => $request->jenis_surat])
            ->with('success', 'Data surat berhasil ditambahkan ke Buku Agenda.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $agenda = BukuAgenda::findOrFail($id);

        return Inertia::render('Tenant/Sekretariat/BukuAgenda/Form', [
            'agenda' => $agenda,
            'is_edit' => true
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BukuAgendaRequest $request, $id)
    {
        $agenda = BukuAgenda::findOrFail($id);
        $agenda->update($request->validated());

        return redirect()->route('sekretariat.buku-agenda.index', ['jenis' => $agenda->jenis_surat])
            ->with('success', 'Data surat berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $agenda = BukuAgenda::findOrFail($id);
        $jenis = $agenda->jenis_surat;
        $agenda->delete();

        return redirect()->route('sekretariat.buku-agenda.index', ['jenis' => $jenis])
            ->with('success', 'Data surat berhasil dihapus dari Buku Agenda.');
    }
}
