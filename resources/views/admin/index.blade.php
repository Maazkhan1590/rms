@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
    <div class="dashboard-grid">
        <!-- Welcome Section -->
        <div class="dashboard-header">
            <h2>Welcome back, {{ auth()->user()->name }}! 👋</h2>
            <p>Here's what's happening with your research management system today.</p>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            @if(auth()->user()->isAdmin)
                <!-- Admin Stats -->
                <div class="card card-stat">
                    <div class="card-body">
                        <div class="card-stat-label">Total Users</div>
                        <div class="card-stat-value">{{ $totalUsers ?? 0 }}</div>
                        <div class="card-stat-change">
                            <span class="text-warning">{{ $pendingUsers ?? 0 }} pending</span>
                        </div>
                    </div>
                </div>

                <div class="card card-stat">
                    <div class="card-body">
                        <div class="card-stat-label">Publications ({{ $year }})</div>
                        <div class="card-stat-value">{{ $totalPublications ?? 0 }}</div>
                        <div class="card-stat-change positive">{{ $approvedPublications ?? 0 }} approved</div>
                    </div>
                </div>

                <div class="card card-stat">
                    <div class="card-body">
                        <div class="card-stat-label">Grants ({{ $year }})</div>
                        <div class="card-stat-value">{{ $totalGrants ?? 0 }}</div>
                        <div class="card-stat-change">~{{ number_format($totalGrantAmount ?? 0, 2) }}M OMR</div>
                    </div>
                </div>

                <div class="card card-stat">
                    <div class="card-body">
                        <div class="card-stat-label">Pending Approvals</div>
                        <div class="card-stat-value">{{ $pendingApprovals ?? 0 }}</div>
                        <div class="card-stat-change warning">Requires attention</div>
                    </div>
                </div>

                <div class="card card-stat">
                    <div class="card-body">
                        <div class="card-stat-label">Total Research Points</div>
                        <div class="card-stat-value">{{ number_format($totalResearchPoints ?? 0, 0) }}</div>
                        <div class="card-stat-change">All users combined</div>
                    </div>
                </div>

                <div class="card card-stat">
                    <div class="card-body">
                        <div class="card-stat-label">RTN Submissions</div>
                        <div class="card-stat-value">{{ $totalRtnSubmissions ?? 0 }}</div>
                        <div class="card-stat-change">Year {{ $year }}</div>
                    </div>
                </div>

                <div class="card card-stat">
                    <div class="card-body">
                        <div class="card-stat-label">Bonus Recognitions</div>
                        <div class="card-stat-value">{{ $totalBonusRecognitions ?? 0 }}</div>
                        <div class="card-stat-change positive">Year {{ $year }}</div>
                    </div>
                </div>

                <div class="card card-stat">
                    <div class="card-body">
                        <div class="card-stat-label">Active Workflows</div>
                        <div class="card-stat-value">{{ $activeWorkflows ?? 0 }}</div>
                        <div class="card-stat-change warning">{{ $pendingWorkflowsCount ?? 0 }} pending</div>
                    </div>
                </div>
            @elseif(auth()->user()->isResearchCoordinator() || auth()->user()->isDean())
                <!-- Coordinator/Dean Stats -->
                <div class="card card-stat">
                    <div class="card-body">
                        <div class="card-stat-label">My Pending Approvals</div>
                        <div class="card-stat-value">{{ $myPendingWorkflows ?? 0 }}</div>
                        <div class="card-stat-change warning">Requires your attention</div>
                    </div>
                </div>

                <div class="card card-stat">
                    <div class="card-body">
                        <div class="card-stat-label">Total Pending</div>
                        <div class="card-stat-value">{{ $pendingApprovals ?? 0 }}</div>
                        <div class="card-stat-change">In your scope</div>
                    </div>
                </div>

                <div class="card card-stat">
                    <div class="card-body">
                        <div class="card-stat-label">Publications ({{ $year }})</div>
                        <div class="card-stat-value">{{ $totalPublications ?? 0 }}</div>
                        <div class="card-stat-change">In your scope</div>
                    </div>
                </div>

                <div class="card card-stat">
                    <div class="card-body">
                        <div class="card-stat-label">Grants ({{ $year }})</div>
                        <div class="card-stat-value">{{ $totalGrants ?? 0 }}</div>
                        <div class="card-stat-change">In your scope</div>
                    </div>
                </div>
            @else
                <!-- Faculty Stats -->
                <div class="card card-stat">
                    <div class="card-body">
                        <div class="card-stat-label">My Publications ({{ $year }})</div>
                        <div class="card-stat-value">{{ $myPublications ?? 0 }}</div>
                        <div class="card-stat-change positive">{{ $approvedPublications ?? 0 }} approved</div>
                    </div>
                </div>

                <div class="card card-stat">
                    <div class="card-body">
                        <div class="card-stat-label">Pending Publications</div>
                        <div class="card-stat-value">{{ $pendingPublications ?? 0 }}</div>
                        <div class="card-stat-change warning">Under review</div>
                    </div>
                </div>

                <div class="card card-stat">
                    <div class="card-body">
                        <div class="card-stat-label">My Grants ({{ $year }})</div>
                        <div class="card-stat-value">{{ $myGrants ?? 0 }}</div>
                        <div class="card-stat-change">Year {{ $year }}</div>
                    </div>
                </div>

                <div class="card card-stat">
                    <div class="card-body">
                        <div class="card-stat-label">Total Research Points</div>
                        <div class="card-stat-value">{{ number_format($totalPoints ?? 0, 1) }}</div>
                        <div class="card-stat-change positive">Current total</div>
                    </div>
                </div>

                <div class="card card-stat">
                    <div class="card-body">
                        <div class="card-stat-label">RTN Submissions</div>
                        <div class="card-stat-value">{{ $myRtnSubmissions ?? 0 }}</div>
                        <div class="card-stat-change">Year {{ $year }}</div>
                    </div>
                </div>

                <div class="card card-stat">
                    <div class="card-body">
                        <div class="card-stat-label">Bonus Recognitions</div>
                        <div class="card-stat-value">{{ $myBonusRecognitions ?? 0 }}</div>
                        <div class="card-stat-change">Year {{ $year }}</div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Charts and Recent Activity -->
        <div class="dashboard-content">
            <!-- Chart Section -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Monthly Submissions</h3>
                </div>
                <div class="card-body">
                    <canvas id="monthlySubmissionsChart" style="height: 250px;"></canvas>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Activity</h3>
                </div>
                <div class="card-body">
                    <div class="activity-list">
                        @forelse($recentActivities ?? [] as $activity)
                        <div class="activity-item">
                            <div class="activity-icon">{{ $activity['icon'] ?? '📋' }}</div>
                            <div class="activity-content">
                                <p class="activity-title">{{ $activity['title'] ?? 'Activity' }}</p>
                                <p class="activity-desc">{{ $activity['desc'] ?? '' }}</p>
                            </div>
                            <div class="activity-time">{{ $activity['time'] ?? 'Recently' }}</div>
                        </div>
                        @empty
                        <div class="activity-item">
                            <div class="activity-content">
                                <p class="activity-desc" style="text-align: center; color: var(--text-light);">No recent activity</p>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Charts Section -->
        <div class="dashboard-charts-grid">
            <!-- Publications by Type -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Publications by Type</h3>
                </div>
                <div class="card-body">
                    <canvas id="publicationsByTypeChart" style="height: 250px;"></canvas>
                </div>
            </div>

            <!-- Grants by Status -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Grants by Status</h3>
                </div>
                <div class="card-body">
                    <canvas id="grantsByStatusChart" style="height: 250px;"></canvas>
                </div>
            </div>

            <!-- Submissions Overview -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Submissions Overview</h3>
                </div>
                <div class="card-body">
                    <canvas id="submissionsByTypeChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Faculty Publications Listing -->
        @if(!auth()->user()->isAdmin && !auth()->user()->isResearchCoordinator() && !auth()->user()->isDean())
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title">My Publications</h3>
                    <a href="{{ route('publications.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Submit New Paper
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(isset($allPublications) && $allPublications->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Year</th>
                                <th>Status</th>
                                <th>Points</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allPublications as $publication)
                            <tr>
                                <td>
                                    <a href="{{ route('publications.show', $publication->id) }}" style="color: inherit; text-decoration: none;">
                                        {{ \Illuminate\Support\Str::limit($publication->title, 50) }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $publication->publication_type ?? 'N/A')) }}</span>
                                </td>
                                <td>{{ $publication->publication_year ?? $publication->year ?? 'N/A' }}</td>
                                <td>
                                    @if($publication->status == 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($publication->status == 'submitted')
                                        <span class="badge badge-warning">Submitted</span>
                                    @elseif($publication->status == 'rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($publication->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($publication->points_allocated)
                                        <strong style="color: var(--primary);">{{ number_format($publication->points_allocated, 2) }}</strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('publications.show', $publication->id) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($publication->status == 'draft')
                                            <form action="{{ route('publications.submit', $publication->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Submit for Approval" onclick="return confirm('Submit this publication for approval?');">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @can('publication_approve')
                                        @if(in_array($publication->status, ['submitted', 'pending', 'pending_coordinator', 'pending_dean']))
                                            <form action="{{ route('admin.publications.approve', $publication->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Approve" onclick="return confirm('Approve this publication?');">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.publications.reject', $publication->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger" title="Reject" onclick="return confirm('Reject this publication?');">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $allPublications->links() }}
                </div>
                @else
                <p class="text-muted text-center py-4">No publications found. <a href="{{ route('publications.create') }}">Submit your first paper</a></p>
                @endif
            </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    @if(auth()->user()->isAdmin)
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Add New User
                    </a>
                    @endif
                    <a href="{{ route('admin.workflows.pending') }}" class="btn btn-secondary">
                        <i class="fas fa-tasks"></i> Pending Approvals
                    </a>
                    @if(auth()->user()->isAdmin)
                    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-clipboard-list"></i> Audit Logs
                    </a>
                    @endif
                    <a href="{{ route('admin.publications.index') }}" class="btn btn-secondary">
                        <i class="fas fa-book"></i> All Publications
                    </a>
                    <a href="{{ route('admin.grants.index') }}" class="btn btn-secondary">
                        <i class="fas fa-money-bill-wave"></i> All Grants
                    </a>
                    @if(!auth()->user()->isAdmin && !auth()->user()->isResearchCoordinator() && !auth()->user()->isDean())
                    <a href="{{ route('publications.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Submit Paper
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .dashboard-grid {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            width: 100%;
        }

        .dashboard-header {
            margin-bottom: 1rem;
        }

        .dashboard-header h2 {
            margin-bottom: 0.5rem;
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary, #1a1a1a);
        }

        .dashboard-header p {
            color: var(--text-secondary, #6b7280);
            margin: 0;
            font-size: 1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .card-stat {
            min-height: 140px;
        }

        .card-stat-label {
            font-size: 0.75rem;
            color: var(--text-secondary, #6b7280);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.75rem;
            font-weight: 600;
        }

        .card-stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--color-primary, #0056b3);
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }

        .card-stat-change {
            font-size: 0.875rem;
            color: var(--text-secondary, #6b7280);
        }

        .card-stat-change.positive {
            color: var(--color-success, #28a745);
        }

        .card-stat-change.warning {
            color: var(--color-warning, #ffc107);
        }

        .dashboard-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .dashboard-charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .dashboard-content .card {
            margin-bottom: 0;
        }

        .chart-placeholder {
            text-align: center;
        }

        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .activity-item {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            border-radius: 0.5rem;
            background: var(--bg-secondary, #f8f9fa);
            transition: all 0.2s ease;
        }

        .activity-item:hover {
            background: var(--color-gray-100, #e9ecef);
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .activity-icon {
            font-size: 1.5rem;
            min-width: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .activity-content {
            flex: 1;
        }

        .activity-content p {
            margin: 0;
        }

        .activity-title {
            font-weight: 600;
            color: var(--text-primary, #1a1a1a);
            margin-bottom: 0.25rem;
            font-size: 0.95rem;
        }

        .activity-desc {
            font-size: 0.875rem;
            color: var(--text-secondary, #6b7280);
        }

        .activity-time {
            font-size: 0.875rem;
            color: var(--text-light, #9ca3af);
            white-space: nowrap;
            align-self: flex-start;
            padding-top: 0.25rem;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .quick-actions .btn {
            min-width: 150px;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .dashboard-content {
                grid-template-columns: 1fr;
            }

            .dashboard-charts-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
            }

            .card-stat-value {
                font-size: 2rem;
            }

            .dashboard-content {
                grid-template-columns: 1fr;
            }

            .dashboard-charts-grid {
                grid-template-columns: 1fr;
            }

            .quick-actions {
                grid-template-columns: 1fr;
            }

            .quick-actions .btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-header h2 {
                font-size: 1.5rem;
            }
        }
    </style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Submissions Chart
    const ctx = document.getElementById('monthlySubmissionsChart');
    if (ctx) {
        const monthlyData = @json($monthlySubmissions ?? []);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthlyData.labels || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'Publications',
                        data: monthlyData.publications || [],
                        borderColor: '#4d8bff',
                        backgroundColor: 'rgba(77, 139, 255, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Grants',
                        data: monthlyData.grants || [],
                        borderColor: '#0056b3',
                        backgroundColor: 'rgba(0, 86, 179, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 12
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Publications by Type Chart
    const pubTypeCtx = document.getElementById('publicationsByTypeChart');
    if (pubTypeCtx) {
        const pubTypeData = @json($publicationsByType ?? []);

        new Chart(pubTypeCtx, {
            type: 'doughnut',
            data: {
                labels: pubTypeData.labels || [],
                datasets: [{
                    data: pubTypeData.data || [],
                    backgroundColor: pubTypeData.colors || [],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12
                    }
                }
            }
        });
    }

    // Grants by Status Chart
    const grantsStatusCtx = document.getElementById('grantsByStatusChart');
    if (grantsStatusCtx) {
        const grantsStatusData = @json($grantsByStatus ?? []);

        new Chart(grantsStatusCtx, {
            type: 'doughnut',
            data: {
                labels: grantsStatusData.labels || [],
                datasets: [{
                    data: grantsStatusData.data || [],
                    backgroundColor: grantsStatusData.colors || [],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12
                    }
                }
            }
        });
    }

    // Submissions by Type Chart
    const submissionsCtx = document.getElementById('submissionsByTypeChart');
    if (submissionsCtx) {
        const submissionsData = @json($submissionsByType ?? []);

        new Chart(submissionsCtx, {
            type: 'bar',
            data: {
                labels: submissionsData.labels || [],
                datasets: [{
                    label: 'Total Submissions',
                    data: submissionsData.data || [],
                    backgroundColor: submissionsData.colors || [],
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
