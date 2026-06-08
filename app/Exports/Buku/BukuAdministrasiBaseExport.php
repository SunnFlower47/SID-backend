<?php

namespace App\Exports\Buku;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class BukuAdministrasiBaseExport implements FromView, WithEvents
{
    protected $data;
    protected $view;

    public function __construct(string $view, array $data)
    {
        $this->view = $view;
        $this->data = $data;
    }

    public function view(): View
    {
        return view($this->view, [
            'data' => $this->data['data'],
            'filters' => $this->data['filters'] ?? []
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                
                // We add a basic border to the entire sheet if needed
                // It's better if the view itself contains styles, but we can do it here.
            },
        ];
    }
}
