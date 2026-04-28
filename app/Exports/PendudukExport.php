<?php

namespace App\Exports;

use App\Models\Penduduk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Http\Request;

class PendudukExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Penduduk::withWilayah()->with('kartuKeluarga');

        // Apply same filters as controller
        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('nkk', 'like', "%{$search}%");
            });
        }

        if ($this->request->filled('rt_id') && $this->request->rt_id !== 'all') {
            $query->where('rt_id', $this->request->rt_id);
        }

        if ($this->request->filled('rw_id') && $this->request->rw_id !== 'all') {
            $query->where('rw_id', $this->request->rw_id);
        }

        if ($this->request->filled('jenis_kelamin') && $this->request->jenis_kelamin !== 'all') {
            $query->where('jenis_kelamin', $this->request->jenis_kelamin);
        }

        if ($this->request->filled('dusun_id') && $this->request->dusun_id !== 'all') {
            $query->where('dusun_id', $this->request->dusun_id);
        }

        // Filter by age range
        if ($this->request->filled('filter_umur') && $this->request->filter_umur !== 'all') {
            $filterUmur = $this->request->filter_umur;
            $today = \Carbon\Carbon::now();

            switch ($filterUmur) {
                case 'bayi':
                    $query->where('tanggal_lahir', '>=', $today->copy()->subYears(2));
                    break;
                case 'balita':
                    $query->where('tanggal_lahir', '>=', $today->copy()->subYears(5))
                          ->where('tanggal_lahir', '<', $today->copy()->subYears(2));
                    break;
                case 'anak':
                    $query->where('tanggal_lahir', '>=', $today->copy()->subYears(12))
                          ->where('tanggal_lahir', '<', $today->copy()->subYears(5));
                    break;
                case 'remaja':
                    $query->where('tanggal_lahir', '>=', $today->copy()->subYears(18))
                          ->where('tanggal_lahir', '<', $today->copy()->subYears(12));
                    break;
                case 'dewasa_muda':
                    $query->where('tanggal_lahir', '>=', $today->copy()->subYears(30))
                          ->where('tanggal_lahir', '<', $today->copy()->subYears(18));
                    break;
                case 'dewasa':
                    $query->where('tanggal_lahir', '>=', $today->copy()->subYears(60))
                          ->where('tanggal_lahir', '<', $today->copy()->subYears(30));
                    break;
                case 'lansia':
                    $query->where('tanggal_lahir', '<=', $today->copy()->subYears(60));
                    break;
            }
        }

        return $query->orderBy('rt_id')
                     ->orderBy('rw_id')
                     ->orderBy('nkk')
                     ->orderByRaw("CASE
                         WHEN kedudukan_keluarga = 'Kepala Keluarga' THEN 1
                         WHEN kedudukan_keluarga = 'Istri' THEN 2
                         WHEN kedudukan_keluarga = 'Anak' THEN 3
                         WHEN kedudukan_keluarga = 'Menantu' THEN 4
                         WHEN kedudukan_keluarga = 'Cucu' THEN 5
                         WHEN kedudukan_keluarga = 'Orang Tua' THEN 6
                         WHEN kedudukan_keluarga = 'Mertua' THEN 7
                         WHEN kedudukan_keluarga = 'Saudara' THEN 8
                         ELSE 9
                     END")
                     ->orderBy('tanggal_lahir', 'asc')
                     ->get();
    }

    public function title(): string
    {
        return 'Data Penduduk Desa Cibatu';
    }

    public function headings(): array
    {
        return [
            'NIK',
            'Nama',
            'No. KK',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Usia',
            'Agama',
            'Status Perkawinan',
            'Kedudukan Keluarga',
            'Pendidikan',
            'Pekerjaan',
            'Nama Ayah',
            'Nama Ibu',
            'Alamat',
            'RT',
            'RW',
            'Dusun',
            'Keterangan'
        ];
    }

    public function map($penduduk): array
    {
        return [
            "'" . $penduduk->nik, 
            $penduduk->nama,
            "'" . $penduduk->nkk, 
            $penduduk->jenis_kelamin_label,
            $penduduk->tempat_lahir,
            $penduduk->tanggal_lahir ? $penduduk->tanggal_lahir->format('d/m/Y') : '',
            $penduduk->usia,
            $penduduk->agama,
            $penduduk->status_perkawinan,
            $penduduk->kedudukan_keluarga,
            $penduduk->pendidikan,
            $penduduk->pekerjaan,
            $penduduk->nama_ayah,
            $penduduk->nama_ibu,
            $penduduk->alamat,
            optional($penduduk->rtMaster)->kode,
            optional($penduduk->rwMaster)->kode,
            optional($penduduk->dusunMaster)->nama,
            $penduduk->keterangan,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (header)
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2D5A27'] // Dark green
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Get the highest row and column
                $highestRow = $event->sheet->getDelegate()->getHighestRow();
                $highestColumn = $event->sheet->getDelegate()->getHighestColumn();

                // Auto-fit column widths
                foreach (range('A', $highestColumn) as $column) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setAutoSize(true);
                }

                // Set row height for header
                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(25);

                // Add borders to all data cells
                $event->sheet->getDelegate()->getStyle('A1:' . $highestColumn . $highestRow)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // Center align all data cells
                $event->sheet->getDelegate()->getStyle('A2:' . $highestColumn . $highestRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // Format NIK column (A) as text
                $event->sheet->getDelegate()->getStyle('A2:A' . $highestRow)
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_TEXT);

                // Format No. KK column (C) as text
                $event->sheet->getDelegate()->getStyle('C2:C' . $highestRow)
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_TEXT);

                // Freeze the header row
                $event->sheet->getDelegate()->freezePane('A2');
            },
        ];
    }
}
