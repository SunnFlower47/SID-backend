<?php

namespace App\Exports;

use App\Models\AsetInventaris;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Http\Request;

class AsetExport implements FromView, ShouldAutoSize, WithEvents
{
    protected $request;
    protected $tahun;
    protected $semester;

    public function __construct(Request $request = null)
    {
        $this->request = $request;
        $this->tahun = $request ? $request->input('tahun', date('Y')) : date('Y');
        $this->semester = $request ? $request->input('semester', 2) : 2;
    }

    public function view(): View
    {
        $asets = AsetInventaris::with(['barang.kategori', 'mutasis'])->get();
        
        $groupedAset = $asets->groupBy(function($item) {
            return $item->barang?->kategori?->nama ?? 'TANPA KATEGORI';
        });

        return view('exports.aset', [
            'groupedAset' => $groupedAset,
            'tahun' => $this->tahun,
            'semester' => $this->semester,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                
                // You can add more complex styling here if needed
                // For example, setting column widths manually if ShouldAutoSize isn't enough
                $sheet->getColumnDimension('A')->setWidth(15);
                $sheet->getColumnDimension('B')->setWidth(40);
                $sheet->getColumnDimension('C')->setWidth(12);
            },
        ];
    }
}
