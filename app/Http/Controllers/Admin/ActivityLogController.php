<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('audit_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = ActivityLog::with(['user']);

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by activity type
        if ($request->has('activity_type') && $request->activity_type) {
            $query->where('activity_type', $request->activity_type);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('activity_type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->latest('created_at')->paginate(50);

        // Get filter options
        $users = User::whereHas('activityLogs')->pluck('name', 'id')->take(100);
        $activityTypes = ActivityLog::distinct()->pluck('activity_type')->filter()->sort()->values()->take(50);

        return view('admin.activity-logs.index', compact('logs', 'users', 'activityTypes'));
    }

    /**
     * Display the specified activity log
     */
    public function show(ActivityLog $activityLog)
    {
        abort_if(Gate::denies('audit_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activityLog->load(['user']);

        return view('admin.activity-logs.show', compact('activityLog'));
    }
}
