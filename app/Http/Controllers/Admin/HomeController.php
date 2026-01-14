<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Publication;
use App\Models\Grant;
use App\Models\RtnSubmission;
use App\Models\BonusRecognition;
use App\Models\ApprovalWorkflow;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class HomeController
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Block Students from accessing admin dashboard
        if ($user->hasRole('Student')) {
            return redirect()->route('welcome')
                ->with('error', 'Students do not have access to the admin dashboard. You can view and submit publications from the home page.');
        }

        $currentYear = $request->get('year', now()->year);

        // Role-based stats
        if ($user->isAdmin || $user->hasRole('admin')) {
            // Admin stats
            $stats = $this->getAdminStats($currentYear);
        } elseif ($user->isResearchCoordinator() || $user->isDean()) {
            // Coordinator/Dean stats
            $stats = $this->getCoordinatorStats($user, $currentYear);
        } else {
            // Faculty stats
            $stats = $this->getFacultyStats($user, $currentYear);
        }

        // Get monthly submissions and recent activities for all roles
        $stats['monthlySubmissions'] = $this->getMonthlySubmissions($currentYear);
        $stats['recentActivities'] = $this->getRecentActivities();

        return view('admin.index', $stats);
    }

    /**
     * Get admin dashboard statistics
     */
    private function getAdminStats(int $year): array
    {
        return [
            'totalUsers' => User::where('status', 'active')->count(),
            'pendingUsers' => User::where('status', 'pending')->count(),
            'totalPublications' => Publication::where('year', $year)->count(),
            'approvedPublications' => Publication::where('year', $year)->where('status', 'approved')->count(),
            'totalGrants' => Grant::where('award_year', $year)->count(),
            'approvedGrants' => Grant::where('award_year', $year)->where('status', 'approved')->count(),
            'totalGrantAmount' => Grant::where('award_year', $year)->where('status', 'approved')->sum('amount_omr') / 1000000,
            'pendingApprovals' => ApprovalWorkflow::pending()->count(),
            'totalRtnSubmissions' => RtnSubmission::where('year', $year)->count(),
            'totalBonusRecognitions' => BonusRecognition::where('year', $year)->count(),
            'totalResearchPoints' => User::sum('total_research_points'),
            'year' => $year,
            'recentPublications' => Publication::with('submitter')->latest()->limit(5)->get(),
            'recentGrants' => Grant::with('submitter')->latest()->limit(5)->get(),
            'pendingWorkflows' => ApprovalWorkflow::pending()->with(['submitter', 'assignee'])->limit(10)->get(),
            'pendingWorkflowsCount' => ApprovalWorkflow::pending()->count(),
        ];
    }

    /**
     * Get coordinator/dean dashboard statistics
     */
    private function getCoordinatorStats(User $user, int $year): array
    {
        $college = $user->college;
        $department = $user->department;

        $query = ApprovalWorkflow::query();
        
        if ($college) {
            $query->where('college', $college->name);
        }
        if ($department) {
            $query->where('department', $department->name);
        }

        return [
            'pendingApprovals' => $query->pending()->count(),
            'myPendingWorkflows' => ApprovalWorkflow::where('assigned_to', $user->id)->pending()->count(),
            'totalPublications' => Publication::when($college, function($q) use ($college) {
                $q->where('college', $college->name);
            })->where('year', $year)->count(),
            'totalGrants' => Grant::where('award_year', $year)->count(),
            'year' => $year,
            'pendingWorkflows' => ApprovalWorkflow::where('assigned_to', $user->id)->pending()->with(['submitter', 'submission'])->limit(10)->get(),
            'pendingWorkflowsCount' => ApprovalWorkflow::where('assigned_to', $user->id)->pending()->count(),
        ];
    }

    /**
     * Get faculty dashboard statistics
     */
    private function getFacultyStats(User $user, int $year): array
    {
        return [
            'myPublications' => Publication::where('primary_author_id', $user->id)->where('year', $year)->count(),
            'approvedPublications' => Publication::where('primary_author_id', $user->id)->where('year', $year)->where('status', 'approved')->count(),
            'pendingPublications' => Publication::where('primary_author_id', $user->id)->where('status', 'submitted')->count(),
            'myGrants' => Grant::where('submitted_by', $user->id)->where('award_year', $year)->count(),
            'myRtnSubmissions' => RtnSubmission::where('user_id', $user->id)->where('year', $year)->count(),
            'myBonusRecognitions' => BonusRecognition::where('user_id', $user->id)->where('year', $year)->count(),
            'totalPoints' => $user->total_research_points,
            'year' => $year,
            'recentPublications' => Publication::where('primary_author_id', $user->id)->latest()->limit(5)->get(),
            'recentGrants' => Grant::where('submitted_by', $user->id)->latest()->limit(5)->get(),
        ];
    }

    /**
     * Get monthly submissions data for chart (includes publications and grants)
     */
    private function getMonthlySubmissions(int $year): array
    {
        // Get publications by month
        $publicationsData = Publication::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Get grants by month
        $grantsData = Grant::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $publicationsDataArray = [];
        $grantsDataArray = [];

        for ($i = 1; $i <= 12; $i++) {
            $pubCount = $publicationsData->has($i) ? $publicationsData->get($i)->count : 0;
            $grantCount = $grantsData->has($i) ? $grantsData->get($i)->count : 0;
            
            $publicationsDataArray[] = $pubCount;
            $grantsDataArray[] = $grantCount;
        }

        return [
            'labels' => $labels,
            'publications' => $publicationsDataArray,
            'grants' => $grantsDataArray,
        ];
    }

    /**
     * Get recent activities from database
     */
    private function getRecentActivities(): array
    {
        $activities = [];

        // Get recent approval workflows (most important)
        $recentWorkflows = ApprovalWorkflow::with(['submitter', 'assignee'])
            ->whereIn('status', ['submitted', 'pending_coordinator', 'pending_dean'])
            ->latest('created_at')
            ->limit(5)
            ->get();

        foreach ($recentWorkflows as $workflow) {
            $submission = $workflow->submission;
            $title = 'Unknown';
            if ($submission) {
                $title = $submission->title ?? ($submission->name ?? 'Untitled');
            }
            
            $activities[] = [
                'icon' => '📋',
                'title' => ucfirst($workflow->submission_type) . ' Submission',
                'desc' => Str::limit($title, 50),
                'timestamp' => $workflow->created_at->timestamp,
                'url' => null,
            ];
        }

        // Get recent pending publications
        $recentPublications = Publication::whereIn('status', ['submitted', 'pending'])
            ->with('submitter')
            ->latest('submitted_at')
            ->limit(3)
            ->get();

        foreach ($recentPublications as $pub) {
            $activities[] = [
                'icon' => '📚',
                'title' => 'Publication Submitted',
                'desc' => Str::limit($pub->title, 50),
                'timestamp' => ($pub->submitted_at ?? $pub->created_at)->timestamp,
                'url' => null,
            ];
        }

        // Get recent pending grants
        $recentGrants = Grant::whereIn('status', ['submitted', 'pending'])
            ->with('submitter')
            ->latest('submitted_at')
            ->limit(2)
            ->get();

        foreach ($recentGrants as $grant) {
            $activities[] = [
                'icon' => '💰',
                'title' => 'Grant Submitted',
                'desc' => Str::limit($grant->title, 50),
                'timestamp' => ($grant->submitted_at ?? $grant->created_at)->timestamp,
                'url' => null,
            ];
        }

        // Get recent pending user registrations
        $pendingUsers = User::where('status', 'pending')
            ->latest()
            ->limit(2)
            ->get();

        foreach ($pendingUsers as $user) {
            $activities[] = [
                'icon' => '👤',
                'title' => 'New User Registration',
                'desc' => $user->name . ' requested access',
                'timestamp' => $user->created_at->timestamp,
                'url' => null,
            ];
        }

        // Store timestamps for sorting, then convert to diffForHumans
        foreach ($activities as &$activity) {
            if (isset($activity['timestamp'])) {
                $activity['time'] = Carbon::parse($activity['timestamp'])->diffForHumans();
            }
        }
        unset($activity);

        // Sort by timestamp descending and limit to 10
        usort($activities, function($a, $b) {
            $timeA = $a['timestamp'] ?? 0;
            $timeB = $b['timestamp'] ?? 0;
            return $timeB <=> $timeA;
        });

        // Remove timestamp before returning
        foreach ($activities as &$activity) {
            unset($activity['timestamp']);
        }

        return array_slice($activities, 0, 10);
    }
}
