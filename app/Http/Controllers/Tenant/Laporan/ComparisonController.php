<?php

namespace App\Http\Controllers\Tenant\Laporan;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Penduduk;
use App\Models\Mutasi;
use App\Models\SuratPengajuan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ComparisonController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:laporan_statistik']);
    }

    public function index(Request $request)
    {
        $month1 = $request->month1 ? Carbon::parse($request->month1) : now()->subMonth();
        $month2 = $request->month2 ? Carbon::parse($request->month2) : now();

        return Inertia::render('Tenant/Laporan/Komparasi/Index', [
            'comparison' => Inertia::defer(fn() => [
                'period1' => [
                    'label'    => $month1->translatedFormat('F Y'),
                    'penduduk' => Penduduk::whereMonth('created_at', $month1->month)->whereYear('created_at', $month1->year)->count(),
                    'mutasi'   => Mutasi::whereMonth('tanggal_mutasi', $month1->month)->whereYear('tanggal_mutasi', $month1->year)->count(),
                    'surat'    => SuratPengajuan::whereMonth('created_at', $month1->month)->whereYear('created_at', $month1->year)->count(),
                ],
                'period2' => [
                    'label'    => $month2->translatedFormat('F Y'),
                    'penduduk' => Penduduk::whereMonth('created_at', $month2->month)->whereYear('created_at', $month2->year)->count(),
                    'mutasi'   => Mutasi::whereMonth('tanggal_mutasi', $month2->month)->whereYear('tanggal_mutasi', $month2->year)->count(),
                    'surat'    => SuratPengajuan::whereMonth('created_at', $month2->month)->whereYear('created_at', $month2->year)->count(),
                ],
            ]),
            'trends' => Inertia::defer(fn() => [
                'data' => collect(range(11, 0))->map(fn($i) => [
                    'name'     => now()->subMonths($i)->translatedFormat('M y'),
                    'penduduk' => Penduduk::whereMonth('created_at', now()->subMonths($i)->month)->whereYear('created_at', now()->subMonths($i)->year)->count(),
                    'mutasi'   => Mutasi::whereMonth('tanggal_mutasi', now()->subMonths($i)->month)->whereYear('tanggal_mutasi', now()->subMonths($i)->year)->count(),
                ]),
            ]),
            'filters' => [
                'month1' => $month1->format('Y-m'),
                'month2' => $month2->format('Y-m'),
            ]
        ]);
    }
}
