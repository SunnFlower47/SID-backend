<?php

namespace App\Http\Controllers\Tenant\Laporan;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Penduduk;
use App\Models\Mutasi;
use App\Models\Berita;
use App\Models\SuratPengajuan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class LaporanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:laporan.view']);
    }

    /**
     * Dashboard utama modul laporan
     */
    public function index()
    {
        $today = today();

        return Inertia::render('Tenant/Laporan/Dashboard', [
            'stats' => Cache::remember('laporan_dashboard_stats', 3600, fn() => [
                'total_penduduk'    => Penduduk::count(),
                'total_kk'         => \App\Models\KartuKeluarga::count(),
                'total_mutasi'      => Mutasi::count(),
                'total_surat'       => SuratPengajuan::count(),
                'total_berita'      => Berita::count(),
                'penduduk_hari_ini' => Penduduk::whereDate('created_at', $today)->count(),
                'mutasi_hari_ini'   => Mutasi::whereDate('created_at', $today)->count(),
                'surat_hari_ini'    => SuratPengajuan::whereDate('created_at', $today)->count(),
            ]),
            'monthlyTrends' => Cache::remember('laporan_monthly_trends', 3600, fn() => Penduduk::selectRaw(
                    'DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total'
                )->where('created_at', '>=', now()->subMonths(12))
                 ->groupBy('month')->orderBy('month')->get()),
            'recentMutasi' => Cache::remember('laporan_recent_mutasi', 600, fn() => Mutasi::with('penduduk')
                ->orderBy('created_at', 'desc')->limit(5)->get()),
        ]);
    }

    /**
     * Generate PDF/Excel report (download endpoint — tidak diubah)
     */
    public function generate(Request $request)
    {
        $request->validate([
            'type'       => 'required|in:penduduk,kk,mutasi,berita,surat',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'format'     => 'required|in:pdf,excel',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);
        $data      = $this->getReportData($request->type, $startDate, $endDate);

        return $request->format === 'pdf'
            ? $this->generatePdf($data, $request->type, $startDate, $endDate)
            : $this->generateExcel($data, $request->type, $startDate, $endDate);
    }

    public function getReportData($type, $startDate, $endDate)
    {
        return match ($type) {
            'penduduk' => Penduduk::whereBetween('created_at', [$startDate, $endDate])->get(),
            'kk'       => \App\Models\KartuKeluarga::whereBetween('created_at', [$startDate, $endDate])->get(),
            'mutasi'   => Mutasi::whereBetween('created_at', [$startDate, $endDate])->with('penduduk')->get(),
            'berita'   => Berita::whereBetween('created_at', [$startDate, $endDate])->with('user')->get(),
            'surat'    => SuratPengajuan::whereBetween('created_at', [$startDate, $endDate])->with(['penduduk', 'admin'])->get(),
            default    => collect(),
        };
    }

    public function generatePdf($data, $type, $startDate, $endDate)
    {
        $pdf      = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.pdf_export', compact('data', 'type', 'startDate', 'endDate'));
        $filename = 'laporan_' . $type . '_' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.pdf';
        return $pdf->download($filename);
    }

    public function generateExcel($data, $type, $startDate, $endDate)
    {
        $filename = 'laporan_' . $type . '_' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LaporanExport($data, $type, $startDate, $endDate),
            $filename
        );
    }

    /**
     * Laporan Data Penduduk
     */
    public function penduduk(Request $request)
    {
        $query = Penduduk::query();

        if ($request->filled('dusun_id'))          $query->whereHas('kartuKeluarga', fn($q) => $q->where('dusun_id', $request->dusun_id));
        if ($request->filled('rt_id'))             $query->whereHas('kartuKeluarga', fn($q) => $q->where('rt_id', $request->rt_id));
        if ($request->filled('jenis_kelamin'))     $query->where('jenis_kelamin', $request->jenis_kelamin);
        if ($request->filled('usia_min'))          $query->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= ?', [$request->usia_min]);
        if ($request->filled('usia_max'))          $query->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) <= ?', [$request->usia_max]);
        if ($request->filled('status_perkawinan')) $query->where('status_perkawinan', $request->status_perkawinan);
        if ($request->filled('pendidikan'))        $query->where('pendidikan', 'like', '%' . $request->pendidikan . '%');
        if ($request->filled('pekerjaan'))         $query->where('pekerjaan', 'like', '%' . $request->pekerjaan . '%');
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('nama', 'like', "%$s%")->orWhere('nik', 'like', "%$s%"));
        }

        return Inertia::render('Tenant/Laporan/Penduduk/Index', [
            'penduduks'               => Inertia::defer(fn() => $query->orderBy('nama')->paginate(50)->withQueryString()),
            'totalPenduduk'           => Penduduk::count(),
            'filters'                 => $request->only(['search','dusun_id','rt_id','jenis_kelamin','usia_min','usia_max','status_perkawinan','pendidikan','pekerjaan']),
            'dusunOptions'            => \App\Models\Dusun::orderBy('nama')->get(['id','nama']),
            'rtOptions'               => \App\Models\Rt::orderBy('kode')->get(['id','kode']),
            'jenisKelaminOptions'     => Penduduk::select('jenis_kelamin')->whereNotNull('jenis_kelamin')->where('jenis_kelamin','!=','')->distinct()->orderBy('jenis_kelamin')->pluck('jenis_kelamin'),
            'statusPerkawinanOptions' => Penduduk::select('status_perkawinan')->whereNotNull('status_perkawinan')->where('status_perkawinan','!=','')->distinct()->orderBy('status_perkawinan')->pluck('status_perkawinan'),
        ]);
    }

    /**
     * Laporan Kartu Keluarga
     */
    public function kk(Request $request)
    {
        $query = \App\Models\KartuKeluarga::withWilayah()->withCount('penduduks');

        if ($request->filled('dusun_id')) $query->where('dusun_id', $request->dusun_id);
        if ($request->filled('rt_id'))    $query->where('rt_id', $request->rt_id);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('nama_kepala_keluarga', 'like', "%$s%")->orWhere('nkk', 'like', "%$s%"));
        }

        return Inertia::render('Tenant/Laporan/KK/Index', [
            'kks'          => Inertia::defer(fn() => $query->orderBy('nkk')->paginate(50)->withQueryString()),
            'totalKK'      => \App\Models\KartuKeluarga::count(),
            'filters'      => $request->only(['search','dusun_id','rt_id']),
            'dusunOptions' => \App\Models\Dusun::orderBy('nama')->get(['id','nama']),
            'rtOptions'    => \App\Models\Rt::orderBy('kode')->get(['id','kode']),
        ]);
    }

    /**
     * Laporan Mutasi
     */
    public function mutasi(Request $request)
    {
        $query = Mutasi::with('penduduk');

        if ($request->filled('jenis_mutasi')) $query->where('jenis_mutasi', $request->jenis_mutasi);
        if ($request->filled('start_date'))   $query->whereDate('tanggal_mutasi', '>=', $request->start_date);
        if ($request->filled('end_date'))     $query->whereDate('tanggal_mutasi', '<=', $request->end_date);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('penduduk', fn($q) => $q->where('nama', 'like', "%$s%"));
        }

        return Inertia::render('Tenant/Laporan/Mutasi/Index', [
            'mutasis'            => Inertia::defer(fn() => $query->orderBy('tanggal_mutasi', 'desc')->paginate(50)->withQueryString()),
            'stats'              => Inertia::defer(fn() => [
                'total'         => Mutasi::count(),
                'kematian'      => Mutasi::where('jenis_mutasi', 'kematian')->count(),
                'kelahiran'     => Mutasi::where('jenis_mutasi', 'kelahiran')->count(),
                'pindah_keluar' => Mutasi::where('jenis_mutasi', 'pindah_keluar')->count(),
                'pindah_masuk'  => Mutasi::where('jenis_mutasi', 'pindah_masuk')->count(),
                'pisah_kk'      => Mutasi::where('jenis_mutasi', 'pisah_kk')->count(),
            ]),
            'filters'            => $request->only(['search','jenis_mutasi','start_date','end_date']),
            'jenisMutasiOptions' => Mutasi::select('jenis_mutasi')->distinct()->orderBy('jenis_mutasi')->pluck('jenis_mutasi'),
        ]);
    }

    /**
     * Laporan Berita
     */
    public function berita(Request $request)
    {
        $query = Berita::with('author');

        if ($request->filled('kategori'))   $query->where('kategori', $request->kategori);
        if ($request->filled('start_date')) $query->whereDate('created_at', '>=', $request->start_date);
        if ($request->filled('end_date'))   $query->whereDate('created_at', '<=', $request->end_date);
        if ($request->filled('search'))     $query->where('judul', 'like', '%' . $request->search . '%');

        return Inertia::render('Tenant/Laporan/Berita/Index', [
            'beritas'         => Inertia::defer(fn() => $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString()),
            'totalBerita'     => Berita::count(),
            'filters'         => $request->only(['search','kategori','start_date','end_date']),
            'kategoriOptions' => Berita::select('kategori')->whereNotNull('kategori')->where('kategori','!=','')->distinct()->orderBy('kategori')->pluck('kategori'),
        ]);
    }

    /**
     * Laporan Surat Pengajuan
     */
    public function surat(Request $request)
    {
        $query = SuratPengajuan::with(['penduduk', 'admin']);

        if ($request->filled('jenis_surat')) $query->where('jenis_surat', $request->jenis_surat);
        if ($request->filled('status'))      $query->where('status', $request->status);
        if ($request->filled('start_date'))  $query->whereDate('created_at', '>=', $request->start_date);
        if ($request->filled('end_date'))    $query->whereDate('created_at', '<=', $request->end_date);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('penduduk', fn($q) => $q->where('nama', 'like', "%$s%")->orWhere('nik', 'like', "%$s%"));
        }

        return Inertia::render('Tenant/Laporan/Surat/Index', [
            'surats'            => Inertia::defer(fn() => $query->orderBy('created_at', 'desc')->paginate(50)->withQueryString()),
            'totalSurat'        => SuratPengajuan::count(),
            'filters'           => $request->only(['search','jenis_surat','status','start_date','end_date']),
            'jenisSuratOptions' => SuratPengajuan::select('jenis_surat')->distinct()->orderBy('jenis_surat')->pluck('jenis_surat'),
            'statusOptions'     => SuratPengajuan::select('status')->distinct()->orderBy('status')->pluck('status'),
        ]);
    }

    /**
     * Export Mutasi to Excel
     */
    public function exportMutasiExcel(Request $request)
    {
        $query = Mutasi::with('penduduk');

        if ($request->filled('jenis_mutasi')) $query->where('jenis_mutasi', $request->jenis_mutasi);
        if ($request->filled('start_date'))   $query->whereDate('tanggal_mutasi', '>=', $request->start_date);
        if ($request->filled('end_date'))     $query->whereDate('tanggal_mutasi', '<=', $request->end_date);

        $mutasis = $query->orderBy('tanggal_mutasi', 'desc')->get();

        return response()->json([
            'message'       => 'Export Excel untuk mutasi akan segera tersedia',
            'total_records' => $mutasis->count(),
        ]);
    }
}
