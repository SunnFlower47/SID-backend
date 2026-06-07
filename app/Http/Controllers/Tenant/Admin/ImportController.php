<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Kependudukan\ImportService;
use App\Exports\PendudukTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Gate;

class ImportController extends Controller
{
    protected $importService;

    public function __construct(ImportService $importService)
    {
        $this->middleware('auth');
        $this->middleware('can:settings.view');
        $this->importService = $importService;
    }

    /**
     * Display the import form.
     */
    public function index()
    {
        Gate::authorize('penduduk.view');
        return \Inertia\Inertia::render('Tenant/Import/Import');
    }

    /**
     * Process Excel import (Basic).
     */
    public function excel(Request $request)
    {
        Gate::authorize('penduduk.view');

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
        ]);

        try {
            $this->importService->importBasicExcel($request->file('file'));
            return redirect()->route('import.index')
                ->with('success', 'Data berhasil diimport dari Excel!');
        } catch (\Exception $e) {
            return redirect()->route('import.index')
                ->with('error', 'Terjadi kesalahan saat mengimport data: ' . $e->getMessage());
        }
    }

    /**
     * Import Bantuan Sosial
     */
    public function importBantuanSosial(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        try {
            $this->importService->importBantuanSosial($request->file('file'));
            return redirect()->back()->with('success', 'Data bantuan sosial berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing data: ' . $e->getMessage());
        }
    }

    /**
     * Import UMKM
     */
    public function importUmkm(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        try {
            $this->importService->importUmkm($request->file('file'));
            return redirect()->back()->with('success', 'Data UMKM berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing data: ' . $e->getMessage());
        }
    }

    /**
     * Import Pajak PBB
     */
    public function importPajakPbb(Request $request)
    {
        Gate::authorize('pajak_pbb.sync');

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        try {
            $this->importService->importPajakPbb($request->file('file'));
            return redirect()->back()->with('success', 'Data Pajak PBB berhasil diimport dan sistem sedang mengunduh tagihan Mapagbumi di latar belakang!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing data: ' . $e->getMessage());
        }
    }

    /**
     * Preview Import Penduduk (valid/invalid summary)
     */
    public function previewPenduduk(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        try {
            $previewResult = $this->importService->previewPenduduk($request->file('file'));
            return response()->json(array_merge(['success' => true], $previewResult));
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview Import Pajak PBB
     */
    public function previewPajakPbb(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        try {
            $previewResult = $this->importService->previewPajakPbb($request->file('file'));
            return response()->json(array_merge(['success' => true], $previewResult));
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download invalid rows report from penduduk preview file
     */
    public function downloadPendudukInvalidReport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        try {
            $invalidRows = $this->importService->getPendudukInvalidRows($request->file('file'));

            if (empty($invalidRows)) {
                return redirect()->back()->with('success', 'Tidak ada baris invalid.');
            }

            $filename = 'invalid_rows_penduduk_' . now()->format('Ymd_His') . '.xlsx';
            return Excel::download(new class($invalidRows) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                public function __construct(private array $rows) {}
                public function headings(): array { return ['Baris', 'NIK', 'Nama', 'No. KK', 'Error Details']; }
                public function array(): array {
                    return array_map(function ($r) {
                        return [ $r['baris'], $r['nik'], $r['nama'], $r['nkk'], json_encode($r['errors']) ];
                    }, $this->rows);
                }
            }, $filename);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal membuat laporan: ' . $e->getMessage());
        }
    }

    /**
     * Download invalid rows report from Pajak PBB preview file
     */
    public function downloadPajakPbbInvalidReport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        try {
            $invalidRows = $this->importService->getPajakPbbInvalidRows($request->file('file'));

            if (empty($invalidRows)) {
                return redirect()->back()->with('success', 'Tidak ada baris invalid.');
            }

            $filename = 'invalid_rows_pajak_pbb_' . now()->format('Ymd_His') . '.xlsx';
            return Excel::download(new class($invalidRows) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                public function __construct(private array $rows) {}
                public function headings(): array { return ['Baris', 'NOP', 'Nama Wajib Pajak', 'Error Details']; }
                public function array(): array {
                    return array_map(function ($r) {
                        return [ $r['row'], $r['nop'], $r['nama'], json_encode($r['errors']) ];
                    }, $this->rows);
                }
            }, $filename);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal membuat laporan: ' . $e->getMessage());
        }
    }

    /**
     * Import Penduduk
     */
    public function importPenduduk(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        set_time_limit(0);
        ini_set('memory_limit', '512M');

        try {
            $summary = $this->importService->importPenduduk(
                $request->file('file'),
                $request->file('file')->getClientOriginalName()
            );

            $msg = "Import selesai! ✅ Baru: {$summary['imported']} | Diperbarui: {$summary['updated']} | Issues: {$summary['issues']}";
            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing data: ' . $e->getMessage());
        }
    }

    /**
     * Download Import Template
     */
    public function downloadTemplate($type)
    {
        if ($type === 'penduduk') {
            return Excel::download(new PendudukTemplateExport, 'template_penduduk.xlsx');
        }

        if ($type === 'pajak_pbb') {
            return Excel::download(new \App\Exports\PajakPbbTemplateExport, 'template_pajak_pbb.xlsx');
        }

        try {
            $templatePath = $this->importService->getTemplatePath($type);
            return response()->download($templatePath);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
