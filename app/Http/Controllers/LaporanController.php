<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Penduduk;
use App\Models\Mutasi;
use App\Models\Berita;
use App\Models\SuratPengajuan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
        public function __construct()
    {
        $this->middleware(['auth', 'can:laporan.view']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get basic statistics
        $totalPenduduk = Penduduk::count();
        $totalKK = Penduduk::select('nkk')->distinct()->count();
        $totalMutasi = Mutasi::count();
        $totalBerita = Berita::count();
        $totalSuratPengajuan = SuratPengajuan::count();
        $totalPisahKK = Mutasi::where('jenis_mutasi', 'pisah_kk')->count();

        // Get gender distribution
        $genderDistribution = Penduduk::select('jenis_kelamin', DB::raw('count(*) as total'))
            ->groupBy('jenis_kelamin')
            ->get();

        // Get age groups
        $ageGroups = Penduduk::selectRaw('
            CASE
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 17 THEN "Anak-anak (0-16)"
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 17 AND 30 THEN "Remaja (17-30)"
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 31 AND 50 THEN "Dewasa (31-50)"
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 51 AND 65 THEN "Lansia (51-65)"
                ELSE "Manula (65+)"
            END as age_group,
            COUNT(*) as total
        ')
        ->groupBy('age_group')
        ->get();

        // Get monthly trends for the last 12 months
        $monthlyTrends = Penduduk::selectRaw('
            DATE_FORMAT(created_at, "%Y-%m") as month,
            COUNT(*) as total
        ')
        ->where('created_at', '>=', now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // Get recent activities
        $recentActivities = collect([
            [
                'type' => 'penduduk',
                'action' => 'Data penduduk terbaru',
                'count' => Penduduk::whereDate('created_at', today())->count(),
                'icon' => 'fas fa-users',
                'color' => 'text-blue-600'
            ],
            [
                'type' => 'kk',
                'action' => 'Kartu keluarga baru',
                'count' => Penduduk::select('nkk')->distinct()->whereDate('created_at', today())->count(),
                'icon' => 'fas fa-home',
                'color' => 'text-green-600'
            ],
            [
                'type' => 'mutasi',
                'action' => 'Data mutasi',
                'count' => Mutasi::whereDate('created_at', today())->count(),
                'icon' => 'fas fa-exchange-alt',
                'color' => 'text-purple-600'
            ],
            [
                'type' => 'berita',
                'action' => 'Berita baru',
                'count' => Berita::whereDate('created_at', today())->count(),
                'icon' => 'fas fa-newspaper',
                'color' => 'text-orange-600'
            ],
            [
                'type' => 'surat',
                'action' => 'Surat pengajuan',
                'count' => SuratPengajuan::whereDate('created_at', today())->count(),
                'icon' => 'fas fa-file-alt',
                'color' => 'text-red-600'
            ]
        ]);

        // Get recent data for display
        $recentMutasi = Mutasi::with('penduduk')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();


        return view('laporan.index', compact(
            'totalPenduduk',
            'totalKK',
            'totalMutasi',
            'totalBerita',
            'totalSuratPengajuan',
            'totalPisahKK',
            'genderDistribution',
            'ageGroups',
            'monthlyTrends',
            'recentActivities',
            'recentMutasi'
        ));
    }

    /**
     * Generate detailed report
     */
    public function generate(Request $request)
    {
        $request->validate([
            'type' => 'required|in:penduduk,kk,mutasi,berita,surat',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,excel'
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $data = self::getReportData($request->type, $startDate, $endDate);

        if ($request->format === 'pdf') {
            return $this->generatePdf($data, $request->type, $startDate, $endDate);
        } else {
            return $this->generateExcel($data, $request->type, $startDate, $endDate);
        }
    }

    /**
     * Get report data based on type
     */
    public function getReportData($type, $startDate, $endDate)
    {
        switch ($type) {
            case 'penduduk':
                return Penduduk::whereBetween('created_at', [$startDate, $endDate])
                    ->get();

            case 'kk':
                return Penduduk::whereBetween('created_at', [$startDate, $endDate])
                    ->where('kedudukan_keluarga', 'Kepala Keluarga')
                    ->get();

            case 'mutasi':
                return Mutasi::whereBetween('created_at', [$startDate, $endDate])
                    ->with('penduduk')
                    ->get();

            case 'berita':
                return Berita::whereBetween('created_at', [$startDate, $endDate])
                    ->with('user')
                    ->get();

            case 'surat':
                return SuratPengajuan::whereBetween('created_at', [$startDate, $endDate])
                    ->with(['penduduk', 'admin'])
                    ->get();

            default:
                return collect();
        }
    }

    /**
     * Generate PDF report
     */
    public function generatePdf($data, $type, $startDate, $endDate)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.pdf_export', compact('data', 'type', 'startDate', 'endDate'));
        $filename = 'laporan_' . $type . '_' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Generate Excel report
     */
    public function generateExcel($data, $type, $startDate, $endDate)
    {
        $filename = 'laporan_' . $type . '_' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\LaporanExport($data, $type, $startDate, $endDate), $filename);
    }

    /**
     * Laporan Data Penduduk
     */
    public function penduduk(Request $request)
    {
        $query = Penduduk::query();

        // Filter berdasarkan dusun
        if ($request->filled('dusun_id')) {
            $query->where('dusun_id', $request->dusun_id);
        }

        // Filter berdasarkan RT
        if ($request->filled('rt_id')) {
            $query->where('rt_id', $request->rt_id);
        }

        // Filter berdasarkan jenis kelamin
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        // Filter berdasarkan usia
        if ($request->filled('usia_min')) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= ?', [$request->usia_min]);
        }

        if ($request->filled('usia_max')) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) <= ?', [$request->usia_max]);
        }

        // Filter berdasarkan status perkawinan
        if ($request->filled('status_perkawinan')) {
            $query->where('status_perkawinan', $request->status_perkawinan);
        }

        // Filter berdasarkan pendidikan
        if ($request->filled('pendidikan')) {
            $query->where('pendidikan', 'like', '%' . $request->pendidikan . '%');
        }

        // Filter berdasarkan pekerjaan
        if ($request->filled('pekerjaan')) {
            $query->where('pekerjaan', 'like', '%' . $request->pekerjaan . '%');
        }

        // Search by name or NIK
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('nik', 'like', '%' . $search . '%');
            });
        }

        // Get filter options from master tables
        $dusunOptions = \App\Models\Dusun::orderBy('nama')->get();
        $rtOptions = \App\Models\Rt::orderBy('kode')->get();

        $jenisKelaminOptions = Penduduk::select('jenis_kelamin')
            ->whereNotNull('jenis_kelamin')
            ->where('jenis_kelamin', '!=', '')
            ->distinct()
            ->orderBy('jenis_kelamin')
            ->pluck('jenis_kelamin');

        $statusPerkawinanOptions = Penduduk::select('status_perkawinan')
            ->whereNotNull('status_perkawinan')
            ->where('status_perkawinan', '!=', '')
            ->distinct()
            ->orderBy('status_perkawinan')
            ->pluck('status_perkawinan');

        $pendidikanOptions = Penduduk::select('pendidikan')
            ->whereNotNull('pendidikan')
            ->where('pendidikan', '!=', '')
            ->distinct()
            ->orderBy('pendidikan')
            ->pluck('pendidikan');

        $pekerjaanOptions = Penduduk::select('pekerjaan')
            ->whereNotNull('pekerjaan')
            ->where('pekerjaan', '!=', '')
            ->distinct()
            ->orderBy('pekerjaan')
            ->pluck('pekerjaan');

        // Paginate results
        $penduduks = $query->orderBy('nama')->paginate(50);

        // Get statistics
        $totalPenduduk = Penduduk::count();
        $totalFiltered = $query->count();

        return view('laporan.penduduk', compact(
            'penduduks',
            'totalPenduduk',
            'totalFiltered',
            'dusunOptions',
            'rtOptions',
            'jenisKelaminOptions',
            'statusPerkawinanOptions',
            'pendidikanOptions',
            'pekerjaanOptions'
        ));
    }

    /**
     * Laporan Kartu Keluarga
     */
    public function kk(Request $request)
    {
        // Get unique KK data
        $query = Penduduk::select('nkk', 'nama', 'jenis_kelamin', 'rt_id', 'rw_id', 'dusun_id', 'alamat')
            ->whereNotNull('nkk')
            ->where('nkk', '!=', '')
            ->where('kedudukan_keluarga', 'Kepala Keluarga');

        // Filter berdasarkan dusun
        if ($request->filled('dusun_id')) {
            $query->where('dusun_id', $request->dusun_id);
        }

        // Filter berdasarkan RT
        if ($request->filled('rt_id')) {
            $query->where('rt_id', $request->rt_id);
        }

        // Search by name or NKK
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('nkk', 'like', '%' . $search . '%');
            });
        }

        // Get filter options from master tables
        $dusunOptions = \App\Models\Dusun::orderBy('nama')->get();
        $rtOptions = \App\Models\Rt::orderBy('kode')->get();

        // Paginate results
        $kks = $query->orderBy('nkk')->paginate(50);

        // Get statistics
        $totalKK = Penduduk::select('nkk')->distinct()->count();
        $totalFiltered = $query->count();

        return view('laporan.kk', compact(
            'kks',
            'totalKK',
            'totalFiltered',
            'dusunOptions',
            'rtOptions'
        ));
    }

    /**
     * Laporan Mutasi
     */
    public function mutasi(Request $request)
    {
        $query = Mutasi::with('penduduk');

        // Filter berdasarkan jenis mutasi
        if ($request->filled('jenis_mutasi')) {
            $query->where('jenis_mutasi', $request->jenis_mutasi);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_mutasi', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_mutasi', '<=', $request->end_date);
        }

        // Search by name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('penduduk', function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%');
            });
        }

        // Get filter options
        $jenisMutasiOptions = Mutasi::select('jenis_mutasi')
            ->distinct()
            ->orderBy('jenis_mutasi')
            ->pluck('jenis_mutasi');

        $kategoriMutasiOptions = Mutasi::select('kategori_mutasi')
            ->distinct()
            ->orderBy('kategori_mutasi')
            ->pluck('kategori_mutasi');

        // Paginate results
        $mutasis = $query->orderBy('tanggal_mutasi', 'desc')->paginate(50);

        // Get statistics - calculate from all data, not just filtered
        $totalMutasi = Mutasi::count();
        $totalKematian = Mutasi::where('jenis_mutasi', 'kematian')->count();
        $totalKelahiran = Mutasi::where('jenis_mutasi', 'kelahiran')->count();
        $totalPindahKeluar = Mutasi::where('jenis_mutasi', 'pindah_keluar')->count();
        $totalPindahMasuk = Mutasi::where('jenis_mutasi', 'pindah_masuk')->count();
        $totalPindahRTRW = Mutasi::where('jenis_mutasi', 'pindah_rt_rw')->count();
        $totalPisahKK = Mutasi::where('jenis_mutasi', 'pisah_kk')->count();
        $totalFiltered = $query->count();

        return view('laporan.mutasi', compact(
            'mutasis',
            'totalMutasi',
            'totalKematian',
            'totalKelahiran',
            'totalPindahKeluar',
            'totalPindahMasuk',
            'totalPindahRTRW',
            'totalPisahKK',
            'totalFiltered',
            'jenisMutasiOptions',
            'kategoriMutasiOptions'
        ));
    }

    /**
     * Laporan Berita
     */
    public function berita(Request $request)
    {
        $query = Berita::with('user');

        // Filter berdasarkan kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search by title
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('judul', 'like', '%' . $search . '%');
        }

        // Get filter options
        $kategoriOptions = Berita::select('kategori')
            ->whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        // Paginate results
        $beritas = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get statistics
        $totalBerita = Berita::count();
        $totalFiltered = $query->count();

        return view('laporan.berita', compact(
            'beritas',
            'totalBerita',
            'totalFiltered',
            'kategoriOptions'
        ));
    }

    /**
     * Laporan Surat Pengajuan
     */
    public function surat(Request $request)
    {
        $query = SuratPengajuan::with(['penduduk', 'admin']);

        // Filter berdasarkan jenis surat
        if ($request->filled('jenis_surat')) {
            $query->where('jenis_surat', $request->jenis_surat);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search by name or NIK
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('penduduk', function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('nik', 'like', '%' . $search . '%');
            });
        }

        // Get filter options
        $jenisSuratOptions = SuratPengajuan::select('jenis_surat')
            ->distinct()
            ->orderBy('jenis_surat')
            ->pluck('jenis_surat');

        $statusOptions = SuratPengajuan::select('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        // Paginate results
        $surats = $query->orderBy('created_at', 'desc')->paginate(50);

        // Get statistics
        $totalSurat = SuratPengajuan::count();
        $totalFiltered = $query->count();

        return view('laporan.surat', compact(
            'surats',
            'totalSurat',
            'totalFiltered',
            'jenisSuratOptions',
            'statusOptions'
        ));
    }

    /**
     * Export Mutasi to Excel
     */
    public function exportMutasiExcel(Request $request)
    {
        $query = Mutasi::with('penduduk');

        // Apply same filters as mutasi method
        if ($request->filled('jenis_mutasi')) {
            $query->where('jenis_mutasi', $request->jenis_mutasi);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_mutasi', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_mutasi', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('penduduk', function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%');
            });
        }

        $mutasis = $query->orderBy('tanggal_mutasi', 'desc')->get();

        // For now, return a simple response
        // TODO: Implement actual Excel export using Laravel Excel
        return response()->json([
            'message' => 'Export Excel untuk mutasi akan segera tersedia',
            'total_records' => $mutasis->count(),
            'filters_applied' => $request->all()
        ]);
    }

}
