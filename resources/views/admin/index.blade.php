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
                    <div class="chart-placeholder" style="height: 250px; display: flex; align-items: center; justify-content: center; background: var(--bg-secondary); border-radius: var(--radius-lg); color: var(--text-light);">
                        Chart visualization will be implemented here
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Activity</h3>
                </div>
                <div class="card-body">
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon">👤</div>
                            <div class="activity-content">
                                <p class="activity-title">New user registered</p>
                                <p class="activity-desc">John Smith created an account</p>
                            </div>
                            <div class="activity-time">2 hours ago</div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon">📚</div>
                            <div class="activity-content">
                                <p class="activity-title">Publication submitted</p>
                                <p class="activity-desc">"Machine Learning in Healthcare" by Dr. Jane</p>
                            </div>
                            <div class="activity-time">5 hours ago</div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon">✅</div>
                            <div class="activity-content">
                                <p class="activity-title">Grant approved</p>
                                <p class="activity-desc">Research Grant #2024-001 approved</p>
                            </div>
                            <div class="activity-time">1 day ago</div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon">⚙️</div>
                            <div class="activity-content">
                                <p class="activity-title">System maintenance</p>
                                <p class="activity-desc">Database optimization completed</p>
                            </div>
                            <div class="activity-time">2 days ago</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        ➕ Add New User
                    </a>
                    <a href="#" class="btn btn-secondary">
                        📊 Generate Report
                    </a>
                    <a href="#" class="btn btn-secondary">
                        ⚙️ System Settings
                    </a>
                    <a href="#" class="btn btn-secondary">
                        📋 View Audit Logs
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .dashboard-grid {
            display: grid;
            gap: var(--spacing-lg);
        }

        .dashboard-header {
            margin-bottom: var(--spacing-lg);
        }

        .dashboard-header h2 {
            margin-bottom: var(--spacing-sm);
            font-size: var(--font-size-2xl);
        }

        .dashboard-header p {
            color: var(--text-secondary);
            margin: 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-lg);
        }

        .card-stat-label {
            font-size: var(--font-size-sm);
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: var(--spacing-sm);
        }

        .card-stat-value {
            font-size: var(--font-size-4xl);
            font-weight: var(--font-weight-bold);
            color: var(--color-primary);
            margin-bottom: var(--spacing-md);
        }

        .card-stat-change {
            font-size: var(--font-size-sm);
            color: var(--text-secondary);
        }

        .card-stat-change.positive {
            color: var(--color-success);
        }

        .card-stat-change.warning {
            color: var(--color-warning);
        }

        .dashboard-content {
            display: grid;
            gap: var(--spacing-lg);
        }

        .chart-placeholder {
            text-align: center;
        }

        .activity-list {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-md);
        }

        .activity-item {
            display: flex;
            gap: var(--spacing-md);
            padding: var(--spacing-md);
            border-radius: var(--radius-lg);
            background: var(--bg-secondary);
            transition: var(--transition-fast);
        }

        .activity-item:hover {
            background: var(--color-gray-100);
        }

        .activity-icon {
            font-size: var(--font-size-2xl);
            min-width: 40px;
            display: flex;
            align-items: center;
        }

        .activity-content {
            flex: 1;
        }

        .activity-content p {
            margin: 0;
        }

        .activity-title {
            font-weight: var(--font-weight-medium);
            color: var(--text-primary);
            margin-bottom: var(--spacing-xs);
        }

        .activity-desc {
            font-size: var(--font-size-sm);
            color: var(--text-secondary);
        }

        .activity-time {
            font-size: var(--font-size-sm);
            color: var(--text-light);
            white-space: nowrap;
        }

        .quick-actions {
            display: flex;
            gap: var(--spacing-md);
            flex-wrap: wrap;
        }

        .quick-actions .btn {
            flex: 1;
            min-width: 150px;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }

            .card-stat-value {
                font-size: var(--font-size-2xl);
            }

            .quick-actions {
                flex-direction: column;
            }

            .quick-actions .btn {
                width: 100%;
            }
        }
    </style>
@endsection
