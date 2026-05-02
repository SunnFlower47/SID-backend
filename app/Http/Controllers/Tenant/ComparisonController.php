<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Penduduk;
use App\Models\Mutasi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ComparisonController extends Controller
{
        /**
     * Display data comparison dashboard.
     */
    public function index()
    {
        Gate::authorize('laporan_statistik');

        $currentMonth = now();
        $lastMonth = now()->subMonth();

        // Get current month data
        $currentData = $this->getMonthData($currentMonth);

        // Get last month data
        $lastData = $this->getMonthData($lastMonth);

        // Calculate changes
        $changes = $this->calculateChanges($currentData, $lastData);

        // Get monthly trends
        $monthlyTrends = $this->getMonthlyTrends();

        return view('comparison.index', compact(
            'currentData',
            'lastData',
            'changes',
            'monthlyTrends',
            'currentMonth',
            'lastMonth'
        ));
    }

    /**
     * Get data for specific month
     */
    private function getMonthData($month)
    {
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();

        return [
            'total_penduduk' => Penduduk::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
            'total_kk' => \App\Models\KartuKeluarga::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
            'total_mutasi' => Mutasi::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
            'penduduk_aktif' => Penduduk::whereNull('deleted_at')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count(),
            'penduduk_tidak_aktif' => Penduduk::whereNotNull('deleted_at')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count(),
            'mutasi_kematian' => Mutasi::where('kategori_mutasi', 'kematian')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count(),
            'mutasi_pindah' => Mutasi::where('kategori_mutasi', 'pindah_keluar')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count(),
            'mutasi_pindah_rt' => Mutasi::where('kategori_mutasi', 'pindah_rt_rw')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count(),
        ];
    }

    /**
     * Calculate changes between current and last month
     */
    private function calculateChanges($current, $last)
    {
        $changes = [];

        foreach ($current as $key => $value) {
            $lastValue = $last[$key] ?? 0;
            $difference = $value - $lastValue;
            $percentage = $lastValue > 0 ? round(($difference / $lastValue) * 100, 2) : 0;

            $changes[$key] = [
                'difference' => $difference,
                'percentage' => $percentage,
                'trend' => $difference > 0 ? 'up' : ($difference < 0 ? 'down' : 'stable')
            ];
        }

        return $changes;
    }

    /**
     * Get monthly trends for the last 12 months
     */
    private function getMonthlyTrends()
    {
        $trends = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();

            $trends[] = [
                'month' => $month->format('M Y'),
                'month_name' => $month->format('F Y'),
                'penduduk' => Penduduk::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
                'kk' => \App\Models\KartuKeluarga::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
                'mutasi' => Mutasi::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
            ];
        }

        return $trends;
    }

    /**
     * Get detailed comparison data
     */
    public function getDetailedComparison(Request $request)
    {
        Gate::authorize('laporan_statistik');

        $type = $request->get('type', 'penduduk');
        $currentMonth = now();
        $lastMonth = now()->subMonth();

        switch ($type) {
            case 'penduduk':
                return $this->getPendudukComparison($currentMonth, $lastMonth);
            case 'mutasi':
                return $this->getMutasiComparison($currentMonth, $lastMonth);
            case 'kk':
                return $this->getKKComparison($currentMonth, $lastMonth);
            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }
    }

    /**
     * Get penduduk comparison data
     */
    private function getPendudukComparison($currentMonth, $lastMonth)
    {
        $currentStart = $currentMonth->copy()->startOfMonth();
        $currentEnd = $currentMonth->copy()->endOfMonth();
        $lastStart = $lastMonth->copy()->startOfMonth();
        $lastEnd = $lastMonth->copy()->endOfMonth();

        // Gender comparison
        $currentGender = Penduduk::whereBetween('created_at', [$currentStart, $currentEnd])
            ->select('jenis_kelamin', DB::raw('count(*) as total'))
            ->groupBy('jenis_kelamin')
            ->get()
            ->pluck('total', 'jenis_kelamin');

        $lastGender = Penduduk::whereBetween('created_at', [$lastStart, $lastEnd])
            ->select('jenis_kelamin', DB::raw('count(*) as total'))
            ->groupBy('jenis_kelamin')
            ->get()
            ->pluck('total', 'jenis_kelamin');

        // Age group comparison
        $currentAge = Penduduk::whereBetween('created_at', [$currentStart, $currentEnd])
            ->select(
                DB::raw('CASE
                    WHEN usia < 5 THEN "0-4"
                    WHEN usia BETWEEN 5 AND 9 THEN "5-9"
                    WHEN usia BETWEEN 10 AND 14 THEN "10-14"
                    WHEN usia BETWEEN 15 AND 19 THEN "15-19"
                    WHEN usia BETWEEN 20 AND 24 THEN "20-24"
                    WHEN usia BETWEEN 25 AND 29 THEN "25-29"
                    WHEN usia BETWEEN 30 AND 34 THEN "30-34"
                    WHEN usia BETWEEN 35 AND 39 THEN "35-39"
                    WHEN usia BETWEEN 40 AND 44 THEN "40-44"
                    WHEN usia BETWEEN 45 AND 49 THEN "45-49"
                    WHEN usia BETWEEN 50 AND 54 THEN "50-54"
                    WHEN usia BETWEEN 55 AND 59 THEN "55-59"
                    WHEN usia BETWEEN 60 AND 64 THEN "60-64"
                    WHEN usia >= 65 THEN "65+"
                    ELSE "Tidak Diketahui"
                END as age_group'),
                DB::raw('count(*) as total')
            )
            ->groupBy('age_group')
            ->get();

        $lastAge = Penduduk::whereBetween('created_at', [$lastStart, $lastEnd])
            ->select(
                DB::raw('CASE
                    WHEN usia < 5 THEN "0-4"
                    WHEN usia BETWEEN 5 AND 9 THEN "5-9"
                    WHEN usia BETWEEN 10 AND 14 THEN "10-14"
                    WHEN usia BETWEEN 15 AND 19 THEN "15-19"
                    WHEN usia BETWEEN 20 AND 24 THEN "20-24"
                    WHEN usia BETWEEN 25 AND 29 THEN "25-29"
                    WHEN usia BETWEEN 30 AND 34 THEN "30-34"
                    WHEN usia BETWEEN 35 AND 39 THEN "35-39"
                    WHEN usia BETWEEN 40 AND 44 THEN "40-44"
                    WHEN usia BETWEEN 45 AND 49 THEN "45-49"
                    WHEN usia BETWEEN 50 AND 54 THEN "50-54"
                    WHEN usia BETWEEN 55 AND 59 THEN "55-59"
                    WHEN usia BETWEEN 60 AND 64 THEN "60-64"
                    WHEN usia >= 65 THEN "65+"
                    ELSE "Tidak Diketahui"
                END as age_group'),
                DB::raw('count(*) as total')
            )
            ->groupBy('age_group')
            ->get();

        return response()->json([
            'gender' => [
                'current' => $currentGender,
                'last' => $lastGender
            ],
            'age' => [
                'current' => $currentAge,
                'last' => $lastAge
            ]
        ]);
    }

    /**
     * Get mutasi comparison data
     */
    private function getMutasiComparison($currentMonth, $lastMonth)
    {
        $currentStart = $currentMonth->copy()->startOfMonth();
        $currentEnd = $currentMonth->copy()->endOfMonth();
        $lastStart = $lastMonth->copy()->startOfMonth();
        $lastEnd = $lastMonth->copy()->endOfMonth();

        $currentMutasi = Mutasi::whereBetween('created_at', [$currentStart, $currentEnd])
            ->select('kategori_mutasi', DB::raw('count(*) as total'))
            ->groupBy('kategori_mutasi')
            ->get()
            ->pluck('total', 'kategori_mutasi');

        $lastMutasi = Mutasi::whereBetween('created_at', [$lastStart, $lastEnd])
            ->select('kategori_mutasi', DB::raw('count(*) as total'))
            ->groupBy('kategori_mutasi')
            ->get()
            ->pluck('total', 'kategori_mutasi');

        return response()->json([
            'current' => $currentMutasi,
            'last' => $lastMutasi
        ]);
    }

    /**
     * Get KK comparison data
     */
    private function getKKComparison($currentMonth, $lastMonth)
    {
        $currentStart = $currentMonth->copy()->startOfMonth();
        $currentEnd = $currentMonth->copy()->endOfMonth();
        $lastStart = $lastMonth->copy()->startOfMonth();
        $lastEnd = $lastMonth->copy()->endOfMonth();

        $currentKK = \App\Models\KartuKeluarga::whereBetween('created_at', [$currentStart, $currentEnd])->count();
        $lastKK = \App\Models\KartuKeluarga::whereBetween('created_at', [$lastStart, $lastEnd])->count();

        return response()->json([
            'current' => $currentKK,
            'last' => $lastKK
        ]);
    }
}

