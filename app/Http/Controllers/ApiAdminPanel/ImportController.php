<?php

namespace App\Http\Controllers\ApiAdminPanel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PendudukImport;
use App\Models\Penduduk;

class ImportController extends Controller
{
        /**
     * Display the import form.
     */
    public function index()
    {
        Gate::authorize('kependudukan');
        
        return view('import.index');
    }

    /**
     * Process Excel import.
     */
    public function excel(Request $request)
    {
        Gate::authorize('kependudukan');

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
        ]);

        try {
            DB::beginTransaction();

            // Import data from Excel
            Excel::import(new PendudukImport, $request->file('file'));

            DB::commit();

            return redirect()->route('import.index')
                ->with('success', 'Data berhasil diimport dari Excel!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Import Excel Error: ' . $e->getMessage());
            
            return redirect()->route('import.index')
                ->with('error', 'Terjadi kesalahan saat mengimport data: ' . $e->getMessage());
        }
    }
}
