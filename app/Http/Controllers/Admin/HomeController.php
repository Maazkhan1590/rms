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
}
