<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Pelayanan\SuratService;

class TestSuratController extends Controller
{
    protected $suratService;

    public function __construct(SuratService $suratService)
    {
        $this->suratService = $suratService;
    }

    public function testCetak()
    {
        // 1. Data yang mau kita suntikkan (Nanti ini dari database)
        $data = [
            'nama'   => 'SAM PROJECT',
            'nik'    => '3201019908870001',
            'alamat' => 'Desa Cibatu, RT 01/RW 02',
        ];

        try {
            // 2. Panggil service buat bikin file-nya
            $outputPath = $this->suratService->generate('template_domisili.docx', $data, 'Surat_Domisili_Sam.docx');

            // 3. Langsung kasih link download-nya ke browser
            return response()->download($outputPath);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
