<?php

namespace App\Http\Controllers\ApiAdminPanel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Penduduk;
use App\Models\Mutasi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class ComparisonController extends Controller
{
    public function index(): JsonResponse
    {
        Gate::authorize('statistics.view');

        $current = $this->getMonthData(now());
        $last = $this->getMonthData(now()->subMonth());

        return response()->json([
            'status' => 'success',
            'data' => [
                'current' => $current,
                'last' => $last,
                'changes' => $this->calculateChanges($current, $last),
                'trends' => $this->getMonthlyTrends()
            ]
        ]);
    }

    private function getMonthData(Carbon $month): array
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        return [
            'total_penduduk' => Penduduk::whereBetween('created_at', [$start, $end])->count(),
            'total_kk' => Penduduk::distinct('nkk')->whereBetween('created_at', [$start, $end])->count(),
            'total_mutasi' => Mutasi::whereBetween('created_at', [$start, $end])->count(),
        ];
    }

    private function calculateChanges(array $current, array $last): array
    {
        $changes = [];
        foreach ($current as $key => $value) {
            $lastVal = $last[$key] ?? 0;
            $diff = $value - $lastVal;
            $changes[$key] = [
                'diff' => $diff,
                'percent' => $lastVal > 0 ? round(($diff / $lastVal) * 100, 2) : 0,
                'trend' => $diff > 0 ? 'up' : ($diff < 0 ? 'down' : 'stable')
            ];
        }
        return $changes;
    }

    private function getMonthlyTrends(): array
    {
        $trends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $trends[] = [
                'month' => $month->format('M Y'),
                'penduduk' => Penduduk::whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])->count(),
            ];
        }
        return $trends;
    }
}
