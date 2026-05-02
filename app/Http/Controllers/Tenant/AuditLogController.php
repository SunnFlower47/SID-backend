<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use Carbon\Carbon;

class AuditLogController extends Controller
{
        public function __construct()
    {
        $this->middleware(['auth', 'can:admin_sistem']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject']);

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('causer_id', $request->user_id);
        }

        // Filter by event
        if ($request->has('event') && $request->event) {
            $query->where('event', $request->event);
        }

        // Filter by subject type
        if ($request->has('subject_type') && $request->subject_type) {
            $query->where('subject_type', $request->subject_type);
        }

        // Search in description
        if ($request->has('search') && $request->search) {
            $query->where('description', 'like', "%{$request->search}%");
        }

        $activities = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $users = User::select('id', 'name')->get();
        $events = Activity::select('event')->distinct()->pluck('event');
        $subjectTypes = Activity::select('subject_type')->distinct()->pluck('subject_type');

        // Get statistics
        $stats = [
            'total' => Activity::count(),
            'today' => Activity::whereDate('created_at', today())->count(),
            'this_week' => Activity::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => Activity::whereMonth('created_at', now()->month)->count(),
        ];

        return view('audit-log.index', compact(
            'activities',
            'users',
            'events',
            'subjectTypes',
            'stats'
        ));
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity)
    {
        $activity->load(['causer', 'subject']);

        return view('audit-log.show', compact('activity'));
    }

    /**
     * Export audit log
     */
    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:pdf,excel',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $query = Activity::with(['causer', 'subject']);

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $activities = $query->orderBy('created_at', 'desc')->get();

        if ($request->format === 'pdf') {
            return $this->exportPdf($activities, $request->start_date, $request->end_date);
        } else {
            return $this->exportExcel($activities, $request->start_date, $request->end_date);
        }
    }

    /**
     * Clear old audit logs
     */
    public function clear(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:30|max:365'
        ]);

        $cutoffDate = now()->subDays($request->days);
        $deletedCount = Activity::where('created_at', '<', $cutoffDate)->delete();

        return redirect()->back()->with('success', "Berhasil menghapus {$deletedCount} log audit yang lebih dari {$request->days} hari.");
    }

    /**
     * Export to PDF
     */
    public function exportPdf($activities, $startDate, $endDate)
    {
        // Implementation for PDF export
        return response()->json(['message' => 'PDF export not implemented yet']);
    }

    /**
     * Export to Excel
     */
    public function exportExcel($activities, $startDate, $endDate)
    {
        // Implementation for Excel export
        return response()->json(['message' => 'Excel export not implemented yet']);
    }
}
