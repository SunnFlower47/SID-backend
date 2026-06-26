<?php

namespace App\Exports;

use App\Models\Penduduk;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
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

class PendudukExport implements FromQuery, WithChunkReading, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    protected $request;
    protected $rowNumber = 0;
    protected $isDynamic = false;
    protected $selectedColumns = [];
    protected $availableColumns = [];

    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->availableColumns = [
            'nomor_urut' => ['header' => 'NOMOR URUT', 'map' => fn($p, $row) => $row],
            'nama' => ['header' => 'NAMA LENGKAP / PANGGILAN', 'map' => fn($p) => strtoupper($p->nama ?: '')],
            'jenis_kelamin' => ['header' => 'JENIS KELAMIN', 'map' => fn($p) => strtoupper($p->jenis_kelamin_label ?: '')],
            'status_perkawinan' => ['header' => 'STATUS PERKAWINAN', 'map' => fn($p) => strtoupper($p->status_perkawinan ?: '-')],
            'tempat_lahir' => ['header' => 'TEMPAT LAHIR', 'map' => fn($p) => strtoupper($p->tempat_lahir ?: '')],
            'tanggal_lahir' => ['header' => 'TANGGAL LAHIR', 'map' => fn($p) => $p->tanggal_lahir ? $p->tanggal_lahir->format('d/m/Y') : ''],
            'agama' => ['header' => 'AGAMA', 'map' => fn($p) => strtoupper($p->agama ?: '')],
            'pendidikan' => ['header' => 'PENDIDIKAN TERAKHIR', 'map' => fn($p) => strtoupper($p->pendidikan ?: '')],
            'pekerjaan' => ['header' => 'PEKERJAAN', 'map' => fn($p) => strtoupper($p->pekerjaan ?: '')],
            'membaca_huruf' => ['header' => 'DAPAT MEMBACA HURUF', 'map' => fn($p) => strtoupper($p->dapat_membaca_huruf ?: '-')],
            'warganegara' => ['header' => 'KEWARGANEGARAAN', 'map' => fn($p) => strtoupper($p->warganegara ?: 'WNI')],
            'alamat' => ['header' => 'ALAMAT', 'map' => fn($p) => strtoupper($p->alamat ?: '')],
            'rt' => ['header' => 'RT', 'map' => fn($p) => strtoupper($p->rt_label ?: '')],
            'rw' => ['header' => 'RW', 'map' => fn($p) => strtoupper($p->rw_label ?: '')],
            'kedudukan_keluarga' => ['header' => 'KEDUDUKAN DLM KELUARGA', 'map' => fn($p) => strtoupper($p->kedudukan_keluarga ?: '-')],
            'nik' => ['header' => 'NIK', 'map' => fn($p) => $p->nik ? "'" . $p->nik : '', 'is_text' => true],
            'nkk' => ['header' => 'NO. KK', 'map' => fn($p) => $p->nkk ? "'" . $p->nkk : '', 'is_text' => true],
            'nama_ayah' => ['header' => 'NAMA AYAH', 'map' => fn($p) => strtoupper($p->nama_ayah ?: '')],
            'nama_ibu' => ['header' => 'NAMA IBU', 'map' => fn($p) => strtoupper($p->nama_ibu ?: '')],
            'dusun' => ['header' => 'DUSUN', 'map' => fn($p) => strtoupper($p->dusun_label ?: '-')],
            'golongan_darah' => ['header' => 'GOLONGAN DARAH', 'map' => fn($p) => strtoupper($p->golongan_darah ?: '-')],
            'no_akta_lahir' => ['header' => 'NO. AKTA LAHIR', 'map' => fn($p) => $p->no_akta_lahir ?: '-'],
            'status_pendidikan' => ['header' => 'STATUS PENDIDIKAN', 'map' => fn($p) => strtoupper($p->status_pendidikan ?: '-')],
            'telepon' => ['header' => 'TELEPON', 'map' => fn($p) => $p->telepon ?: '-'],
            'jenis_cacat' => ['header' => 'JENIS CACAT', 'map' => fn($p) => strtoupper($p->cacat_type ?: '-')],
            'sakit_menahun' => ['header' => 'SAKIT MENAHUN', 'map' => fn($p) => strtoupper($p->sakit_menahun ?: '-')],
            'status_asuransi' => ['header' => 'STATUS ASURANSI', 'map' => fn($p) => strtoupper($p->status_asuransi ?: '-')],
            'keterangan' => ['header' => 'KETERANGAN', 'map' => fn($p) => strtoupper($p->keterangan ?: '')],
        ];

        $cols = $request->input('columns');
        if (!empty($cols) && is_string($cols)) {
            $cols = explode(',', $cols);
        }

        if (!empty($cols) && is_array($cols)) {
            $this->isDynamic = true;
            if (!in_array('nomor_urut', $cols)) {
                array_unshift($cols, 'nomor_urut');
            }
            $this->selectedColumns = $cols;
        }
    }

    public function query()
    {
        $query = Penduduk::withWilayah()->with('kartuKeluarga');

        // Mode: pilih penduduk individu secara spesifik
        if ($this->request->filled('penduduk_ids')) {
            $ids = explode(',', $this->request->penduduk_ids);
            return $query->whereIn('id', $ids);
        }

        // Apply same filters as controller
        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhereHas('kartuKeluarga', fn($sq) => $sq->where('nkk', 'like', "%{$search}%"));
            });
        }

        if ($this->request->filled('rt_id') && $this->request->rt_id !== 'all') {
            $query->whereHas('kartuKeluarga', fn($q) => $q->where('rt_id', $this->request->rt_id));
        }

        if ($this->request->filled('rw_id') && $this->request->rw_id !== 'all') {
            $query->whereHas('kartuKeluarga', fn($q) => $q->where('rw_id', $this->request->rw_id));
        }

        if ($this->request->filled('dusun_id') && $this->request->dusun_id !== 'all') {
            $query->whereHas('kartuKeluarga', fn($q) => $q->where('dusun_id', $this->request->dusun_id));
        }

        if ($this->request->filled('jenis_kelamin') && $this->request->jenis_kelamin !== 'all') {
            $query->where('jenis_kelamin', $this->request->jenis_kelamin);
        }

        if ($this->request->filled('status_perkawinan') && $this->request->status_perkawinan !== 'all') {
            $query->where('status_perkawinan', $this->request->status_perkawinan);
        }

        if ($this->request->filled('golongan_darah') && $this->request->golongan_darah !== 'all') {
            $query->where('golongan_darah', $this->request->golongan_darah);
        }

        if ($this->request->filled('kategori_usia') && $this->request->kategori_usia !== 'all') {
            $today = now();
            switch ($this->request->kategori_usia) {
                case 'balita':
                    $query->where('tanggal_lahir', '>=', $today->copy()->subYears(5));
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

        return $query->orderBy('kartu_keluarga_id')
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
                     ->orderBy('tanggal_lahir', 'asc');
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function title(): string
    {
        return 'Data Penduduk Desa Cibatu';
    }

    public function headings(): array
    {
        if ($this->isDynamic) {
            $headers = [];
            foreach ($this->selectedColumns as $col) {
                if (isset($this->availableColumns[$col])) {
                    $headers[] = $this->availableColumns[$col]['header'];
                }
            }
            return [$headers];
        }

        return [
            [
                'NOMOR URUT',
                'NAMA LENGKAP / PANGGILAN',
                'JENIS KELAMIN',
                'STATUS PERKAWINAN',
                'TEMPAT & TANGGAL LAHIR',
                '',
                'AGAMA',
                'PENDIDIKAN TERAKHIR',
                'PEKERJAAN',
                'DAPAT MEMBACA HURUF',
                'KEWARGANEGARAAN',
                'ALAMAT',
                'RT',
                'RW',
                'KEDUDUKAN DLM KELUARGA',
                'NIK',
                'NO. KK',
                'NAMA AYAH',
                'NAMA IBU',
                'DUSUN',
                'GOLONGAN DARAH',
                'NO. AKTA LAHIR',
                'STATUS PENDIDIKAN',
                'TELEPON',
                'JENIS CACAT',
                'SAKIT MENAHUN',
                'STATUS ASURANSI',
                'KETERANGAN'
            ],
            [
                '', '', '', '', 
                'TEMPAT LAHIR', 'TGL', 
                '', '', '', '', '', '', '', '', '', '',
                '', '', '', '', '', '', '', '', '', '', '', ''
            ]
        ];
    }

    public function map($penduduk): array
    {
        $this->rowNumber++;

        if ($this->isDynamic) {
            $row = [];
            foreach ($this->selectedColumns as $col) {
                if (isset($this->availableColumns[$col])) {
                    $row[] = $this->availableColumns[$col]['map']($penduduk, $this->rowNumber);
                }
            }
            return $row;
        }

        return [
            $this->rowNumber, // A
            strtoupper($penduduk->nama ?: ''), // B
            strtoupper($penduduk->jenis_kelamin_label ?: ''), // C
            strtoupper($penduduk->status_perkawinan ?: '-'), // D
            strtoupper($penduduk->tempat_lahir ?: ''), // E
            $penduduk->tanggal_lahir ? $penduduk->tanggal_lahir->format('d/m/Y') : '', // F
            strtoupper($penduduk->agama ?: ''), // G
            strtoupper($penduduk->pendidikan ?: ''), // H
            strtoupper($penduduk->pekerjaan ?: ''), // I
            strtoupper($penduduk->dapat_membaca_huruf ?: '-'), // J
            strtoupper($penduduk->warganegara ?: 'WNI'), // K
            strtoupper($penduduk->alamat ?: ''), // L (Alamat)
            strtoupper($penduduk->rt_label ?: ''), // M (RT)
            strtoupper($penduduk->rw_label ?: ''), // N (RW)
            strtoupper($penduduk->kedudukan_keluarga ?: '-'), // O
            $penduduk->nik ? "'" . $penduduk->nik : '', // P
            $penduduk->nkk ? "'" . $penduduk->nkk : '', // Q
            strtoupper($penduduk->nama_ayah ?: ''),
            strtoupper($penduduk->nama_ibu ?: ''),
            strtoupper($penduduk->dusun_label ?: '-'),
            strtoupper($penduduk->golongan_darah ?: '-'),
            $penduduk->no_akta_lahir ?: '-',
            strtoupper($penduduk->status_pendidikan ?: '-'),
            $penduduk->telepon ?: '-',
            strtoupper($penduduk->cacat_type ?: '-'),
            strtoupper($penduduk->sakit_menahun ?: '-'),
            strtoupper($penduduk->status_asuransi ?: '-'),
            strtoupper($penduduk->keterangan ?: ''),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $style = [
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2D5A27']
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
        ];

        if ($this->isDynamic) {
            return [
                1 => $style,
            ];
        }

        return [
            1 => $style,
            2 => $style,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

                if ($this->isDynamic) {
                    // Auto-fit column widths
                    for ($col = 1; $col <= $highestColumnIndex; $col++) {
                        $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                        $sheet->getColumnDimension($column)->setAutoSize(true);
                    }

                    $sheet->getRowDimension(1)->setRowHeight(25);

                    // Add borders
                    $sheet->getStyle('A1:' . $highestColumn . $highestRow)
                        ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                    // Center align data cells
                    $sheet->getStyle('A2:' . $highestColumn . $highestRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);

                    // Format text columns (like NIK, NKK)
                    $colIndex = 1;
                    foreach ($this->selectedColumns as $col) {
                        if (isset($this->availableColumns[$col]) && isset($this->availableColumns[$col]['is_text']) && $this->availableColumns[$col]['is_text']) {
                            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                            $sheet->getStyle($columnLetter . '2:' . $columnLetter . $highestRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                        }
                        $colIndex++;
                    }

                    $sheet->freezePane('A2');
                } else {
                    // Merge specific header columns (rowspan=2)
                    $columnsToMerge = ['A', 'B', 'C', 'D', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB'];
                    foreach ($columnsToMerge as $col) {
                        $sheet->mergeCells($col . '1:' . $col . '2');
                    }
                    
                    // Merge TEMPAT & TANGGAL LAHIR (colspan=2)
                    $sheet->mergeCells('E1:F1');

                    // Auto-fit column widths
                    for ($col = 1; $col <= $highestColumnIndex; $col++) {
                        $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                        $sheet->getColumnDimension($column)->setAutoSize(true);
                    }

                    // Header rows height
                    $sheet->getRowDimension(1)->setRowHeight(25);
                    $sheet->getRowDimension(2)->setRowHeight(20);

                    // Add borders to all data cells
                    $sheet->getStyle('A1:' . $highestColumn . $highestRow)
                        ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                    // Center align all data cells
                    $sheet->getStyle('A3:' . $highestColumn . $highestRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);

                    // Format NIK (P) and KK (Q) as text
                    $sheet->getStyle('P3:P' . $highestRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                    $sheet->getStyle('Q3:Q' . $highestRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

                    // Freeze below headers
                    $sheet->freezePane('A3');
                }
            },
        ];
    }
}
