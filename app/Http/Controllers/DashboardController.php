<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\Grant;
use App\Models\User;
use App\Models\ApprovalWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the dashboard with real database statistics
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;
        $lastMonth = $currentMonth - 1;
        $lastMonthYear = $lastMonth <= 0 ? $currentYear - 1 : $currentYear;

        // Calculate publications statistics
        $totalPublications = Publication::count();
        $approvedPublications = Publication::where('status', 'approved')->count();
        $pendingPublications = Publication::whereIn('status', ['submitted', 'pending'])->count();
        
        // Calculate publications change vs last month
        $publicationsThisMonth = Publication::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();
        $publicationsLastMonth = Publication::whereYear('created_at', $lastMonthYear)
            ->whereMonth('created_at', $lastMonth <= 0 ? 12 : $lastMonth)
            ->count();
        $publicationsChange = $publicationsLastMonth > 0 
            ? round((($publicationsThisMonth - $publicationsLastMonth) / $publicationsLastMonth) * 100, 1)
            : 0;
        $publicationsChangeText = $publicationsChange >= 0 
            ? '+' . $publicationsChange . '% vs last month' 
            : $publicationsChange . '% vs last month';

        // Calculate grants statistics
        $totalGrants = Grant::count();
        $approvedGrants = Grant::where('status', 'approved')->count();
        $pendingGrants = Grant::whereIn('status', ['submitted', 'pending'])->count();

        // Calculate users statistics (replacing citations)
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $usersThisMonth = User::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();
        $usersLastMonth = User::whereYear('created_at', $lastMonthYear)
            ->whereMonth('created_at', $lastMonth <= 0 ? 12 : $lastMonth)
            ->count();
        $usersChange = $usersLastMonth > 0 
            ? round((($usersThisMonth - $usersLastMonth) / $usersLastMonth) * 100, 1)
            : 0;
        $usersChangeText = $usersChange >= 0 
            ? '+' . $usersChange . '% vs last month' 
            : $usersChange . '% vs last month';

        // Calculate approvals statistics
        $pendingApprovals = ApprovalWorkflow::pending()->count();
        $overdueApprovals = ApprovalWorkflow::pending()
            ->where('created_at', '<', now()->subDays(7))
            ->count();

        // Statistics data
        $stats = [
            'publications' => $totalPublications,
            'publicationsChange' => $publicationsChangeText,
            'grants' => $totalGrants,
            'grantsPending' => $pendingGrants,
            'users' => $totalUsers,
            'usersChange' => $usersChangeText,
            'approvals' => $pendingApprovals,
            'approvalsOverdue' => $overdueApprovals,
        ];

        // Get recent activities from database
        $recentActivities = $this->getRecentActivities();

        // Chart data for Publications by Type
        $publicationsByType = $this->getPublicationsByType();

        // Chart data for Monthly Submissions
        $monthlySubmissions = $this->getMonthlySubmissions($currentYear);

        return view('dashboard', compact('stats', 'recentActivities', 'publicationsByType', 'monthlySubmissions'));
    }

    /**
     * Get recent activities from database
     */
    private function getRecentActivities(): array
    {
        $activities = [];

        // Get recent pending publications
        $recentPublications = Publication::whereIn('status', ['submitted', 'pending'])
            ->with('submitter')
            ->latest('submitted_at')
            ->limit(3)
            ->get();

        foreach ($recentPublications as $pub) {
            $activities[] = [
                'item' => 'Publication: ' . Str::limit($pub->title, 50),
                'status' => ucfirst($pub->status),
                'statusClass' => $pub->status === 'approved' ? 'success' : 'warning',
                'date' => $pub->submitted_at ? $pub->submitted_at->format('Y-m-d') : $pub->created_at->format('Y-m-d'),
                'action' => 'Review',
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
                'item' => 'Grant: ' . Str::limit($grant->title, 50),
                'status' => ucfirst($grant->status),
                'statusClass' => $grant->status === 'approved' ? 'success' : 'warning',
                'date' => $grant->submitted_at ? $grant->submitted_at->format('Y-m-d') : $grant->created_at->format('Y-m-d'),
                'action' => 'Review',
            ];
        }

        // Get recent pending user approvals
        $pendingUsers = User::where('status', 'pending')
            ->latest()
            ->limit(2)
            ->get();

        foreach ($pendingUsers as $user) {
            $activities[] = [
                'item' => 'User: ' . $user->name . ' requested access',
                'status' => 'New',
                'statusClass' => 'info',
                'date' => $user->created_at->format('Y-m-d'),
                'action' => 'Approve',
            ];
        }

        // Sort by date descending and limit to 5
        usort($activities, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array_slice($activities, 0, 5);
    }

    /**
     * Get publications grouped by type for chart
     */
    private function getPublicationsByType(): array
    {
        $publicationsByType = Publication::select('publication_type', DB::raw('count(*) as count'))
            ->whereNotNull('publication_type')
            ->groupBy('publication_type')
            ->get();

        $labels = [];
        $data = [];
        $colors = ['#4d8bff', '#0056b3', '#9ca3af', '#28a745', '#ffc107'];

        foreach ($publicationsByType as $index => $type) {
            $labels[] = ucfirst($type->publication_type ?? 'Other');
            $data[] = $type->count;
        }

        // If no data, provide defaults
        if (empty($labels)) {
            $labels = ['Journal', 'Conference', 'Book'];
            $data = [0, 0, 0];
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => array_slice($colors, 0, count($labels)),
        ];
    }

    /**
     * Get monthly submissions data for chart
     */
    private function getMonthlySubmissions(int $year): array
    {
        $monthlyData = Publication::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $data[] = $monthlyData->has($i) ? $monthlyData->get($i)->count : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
