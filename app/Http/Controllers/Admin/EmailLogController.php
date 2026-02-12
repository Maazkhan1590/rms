<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmailLogController extends Controller
{
    /**
     * Display a listing of email logs
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('audit_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = EmailLog::with(['user']);

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by notification type
        if ($request->has('notification_type') && $request->notification_type) {
            $query->where('notification_type', $request->notification_type);
        }

        // Filter by recipient email
        if ($request->has('recipient_email') && $request->recipient_email) {
            $query->where('recipient_email', 'like', "%{$request->recipient_email}%");
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
                $q->where('recipient_email', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('user', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->latest('created_at')->paginate(50);

        // Get filter options
        $users = User::whereHas('emailLogs')->pluck('name', 'id')->take(100);
        $notificationTypes = EmailLog::distinct()
            ->pluck('notification_type')
            ->filter()
            ->map(function($type) {
                $parts = explode('\\', $type);
                return [
                    'value' => $type,
                    'label' => end($parts)
                ];
            })
            ->sortBy('label')
            ->take(50);

        $statuses = ['sent', 'failed', 'queued'];

        return view('admin.email-logs.index', compact('logs', 'users', 'notificationTypes', 'statuses'));
    }

    /**
     * Display the specified email log
     */
    public function show(EmailLog $emailLog)
    {
        abort_if(Gate::denies('audit_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $emailLog->load(['user']);

        return view('admin.email-logs.show', compact('emailLog'));
    }

    /**
     * Get email statistics
     */
    public function stats()
    {
        abort_if(Gate::denies('audit_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stats = [
            'total' => EmailLog::count(),
            'sent' => EmailLog::sent()->count(),
            'failed' => EmailLog::failed()->count(),
            'queued' => EmailLog::queued()->count(),
            'today' => EmailLog::whereDate('created_at', today())->count(),
            'this_week' => EmailLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => EmailLog::whereMonth('created_at', now()->month)->count(),
        ];

        return response()->json($stats);
    }
}
