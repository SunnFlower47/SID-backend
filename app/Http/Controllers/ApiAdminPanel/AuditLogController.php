<?php

namespace App\Http\Controllers\ApiAdminPanel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AuditLogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('admin_sistem');

        $query = Activity::with(['causer', 'subject']);

        if ($request->filled('start_date')) $query->whereDate('created_at', '>=', $request->start_date);
        if ($request->filled('end_date')) $query->whereDate('created_at', '<=', $request->end_date);
        if ($request->filled('user_id')) $query->where('causer_id', $request->user_id);
        if ($request->filled('event')) $query->where('event', $request->event);

        $activities = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $activities,
            'meta' => [
                'filters' => [
                    'users' => User::select('id', 'name')->get(),
                    'events' => Activity::select('event')->distinct()->pluck('event'),
                ],
                'stats' => [
                    'total' => Activity::count(),
                    'today' => Activity::whereDate('created_at', today())->count(),
                ]
            ]
        ]);
    }

    public function show(Activity $activity): JsonResponse
    {
        Gate::authorize('admin_sistem');
        return response()->json(['status' => 'success', 'data' => $activity->load(['causer', 'subject'])]);
    }

    public function clear(Request $request): JsonResponse
    {
        Gate::authorize('admin_sistem'); // Ensure permission
        $request->validate(['days' => 'required|integer|min:30']);

        $cutoff = now()->subDays($request->days);
        $count = Activity::where('created_at', '<', $cutoff)->delete();

        return response()->json(['status' => 'success', 'message' => "Berhasil menghapus {$count} log audit lama"]);
    }
}
