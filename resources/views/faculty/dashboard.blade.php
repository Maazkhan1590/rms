@extends('layouts.faculty')

@section('faculty-content')
<div class="faculty-dashboard">
    <!-- Profile Completion -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Profile Completion</h3>
        </div>
        <div class="card-body">
            <div class="progress-section">
                <div class="progress-bar-container">
                    <div class="progress-bar" style="width: 75%"></div>
                </div>
                <p class="progress-text">75% Profile Complete</p>
                <a href="#" class="btn btn-sm btn-outline">Complete Profile</a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="faculty-stats">
        <div class="stat-card">
            <div class="stat-icon">📚</div>
            <div class="stat-content">
                <p class="stat-label">Publications</p>
                <p class="stat-value">12</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-content">
                <p class="stat-label">Active Grants</p>
                <p class="stat-value">3</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⭐</div>
            <div class="stat-content">
                <p class="stat-label">Current Score</p>
                <p class="stat-value">8,450</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🎯</div>
            <div class="stat-content">
                <p class="stat-label">Rank</p>
                <p class="stat-value">#15</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Quick Actions</h3>
        </div>
        <div class="card-body">
            <div class="quick-actions-grid">
                <a href="#" class="action-card">
                    <span class="action-icon">➕</span>
                    <span class="action-label">Submit Publication</span>
                </a>
                <a href="#" class="action-card">
                    <span class="action-icon">💵</span>
                    <span class="action-label">Add Grant</span>
                </a>
                <a href="#" class="action-card">
                    <span class="action-icon">🏆</span>
                    <span class="action-label">Report Award</span>
                </a>
                <a href="#" class="action-card">
                    <span class="action-icon">📥</span>
                    <span class="action-label">Import Data</span>
                </a>
                <a href="#" class="action-card">
                    <span class="action-icon">📊</span>
                    <span class="action-label">View CV</span>
                </a>
                <a href="#" class="action-card">
                    <span class="action-icon">⬇️</span>
                    <span class="action-label">Download Report</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Submissions -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Submissions</h3>
            <a href="#" class="btn btn-sm btn-primary">View All</a>
        </div>
        <div class="card-body">
            <div class="submissions-list">
                <div class="submission-item">
                    <div class="submission-header">
                        <h4>Machine Learning in Healthcare</h4>
                        <span class="badge badge-warning">Pending Review</span>
                    </div>
                    <p class="submission-meta">Journal: Medical AI Review • Submitted: Jan 5, 2026</p>
                </div>
                <div class="submission-item">
                    <div class="submission-header">
                        <h4>Advanced Data Processing Techniques</h4>
                        <span class="badge badge-success">Approved</span>
                    </div>
                    <p class="submission-meta">Journal: Data Science Quarterly • Approved: Dec 28, 2025</p>
                </div>
                <div class="submission-item">
                    <div class="submission-header">
                        <h4>Climate Change Impact Analysis</h4>
                        <span class="badge badge-danger">Revisions Needed</span>
                    </div>
                    <p class="submission-meta">Journal: Environmental Studies • Reviewed: Dec 15, 2025</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Score Breakdown -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Score Breakdown</h3>
        </div>
        <div class="card-body">
            <div class="score-breakdown">
                <div class="score-item">
                    <div class="score-label">Publications</div>
                    <div class="score-bar">
                        <div class="score-fill" style="width: 80%"></div>
                    </div>
                    <div class="score-value">4,200</div>
                </div>
                <div class="score-item">
                    <div class="score-label">Grants</div>
                    <div class="score-bar">
                        <div class="score-fill" style="width: 60%"></div>
                    </div>
                    <div class="score-value">2,400</div>
                </div>
                <div class="score-item">
                    <div class="score-label">Awards & Recognition</div>
                    <div class="score-bar">
                        <div class="score-fill" style="width: 70%"></div>
                    </div>
                    <div class="score-value">1,850</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Approvals -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Pending Approvals</h3>
        </div>
        <div class="card-body">
            @if(true)
                <div class="alert alert-info">
                    ℹ️ You have no pending approvals. All submissions are being reviewed.
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .faculty-dashboard {
        display: grid;
        gap: var(--spacing-lg);
    }

    /* Progress Bar */
    .progress-section {
        padding: var(--spacing-md) 0;
    }

    .progress-bar-container {
        width: 100%;
        height: 12px;
        background: var(--color-gray-200);
        border-radius: var(--radius-full);
        overflow: hidden;
        margin-bottom: var(--spacing-md);
    }

    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, var(--color-primary), var(--color-primary-light));
        transition: width var(--transition-slow);
    }

    .progress-text {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        margin-bottom: var(--spacing-md);
    }

    /* Stats Cards */
    .faculty-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: var(--spacing-lg);
    }

    .stat-card {
        background: linear-gradient(135deg, var(--color-primary-50), var(--color-primary-100));
        border: 1px solid var(--color-primary-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        display: flex;
        gap: var(--spacing-md);
        align-items: flex-start;
    }

    .stat-icon {
        font-size: var(--font-size-3xl);
        min-width: 50px;
    }

    .stat-label {
        margin: 0 0 var(--spacing-xs) 0;
        font-size: var(--font-size-sm);
        color: var(--color-primary-700);
        font-weight: var(--font-weight-medium);
    }

    .stat-value {
        margin: 0;
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        color: var(--color-primary);
    }

    /* Quick Actions */
    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: var(--spacing-lg);
    }

    .action-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: var(--spacing-md);
        padding: var(--spacing-lg);
        border: 2px solid var(--color-gray-200);
        border-radius: var(--radius-lg);
        text-decoration: none;
        color: var(--text-secondary);
        transition: all var(--transition-fast);
    }

    .action-card:hover {
        border-color: var(--color-primary);
        background-color: var(--color-primary-50);
        color: var(--color-primary);
    }

    .action-icon {
        font-size: var(--font-size-3xl);
    }

    .action-label {
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-medium);
        text-align: center;
    }

    /* Submissions List */
    .submissions-list {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-md);
    }

    .submission-item {
        padding: var(--spacing-md);
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        border-left: 4px solid var(--color-primary);
    }

    .submission-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-sm);
    }

    .submission-header h4 {
        margin: 0;
        font-size: var(--font-size-base);
        color: var(--text-primary);
    }

    .submission-meta {
        margin: 0;
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
    }

    /* Score Breakdown */
    .score-breakdown {
        display: grid;
        gap: var(--spacing-lg);
    }

    .score-item {
        display: grid;
        grid-template-columns: 150px 1fr 80px;
        gap: var(--spacing-md);
        align-items: center;
    }

    .score-label {
        font-weight: var(--font-weight-medium);
        color: var(--text-primary);
    }

    .score-bar {
        width: 100%;
        height: 20px;
        background: var(--color-gray-200);
        border-radius: var(--radius-full);
        overflow: hidden;
    }

    .score-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--color-success), var(--color-primary));
        transition: width var(--transition-slow);
    }

    .score-value {
        font-weight: var(--font-weight-semibold);
        color: var(--color-primary);
        text-align: right;
    }

    @media (max-width: 768px) {
        .faculty-stats {
            grid-template-columns: repeat(2, 1fr);
        }

        .quick-actions-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        .score-item {
            grid-template-columns: 100px 1fr 70px;
        }

        .score-label {
            font-size: var(--font-size-sm);
        }

        .stat-icon {
            font-size: var(--font-size-2xl);
        }

        .action-icon {
            font-size: var(--font-size-2xl);
        }

        .action-label {
            font-size: var(--font-size-xs);
        }
    }

    @media (max-width: 480px) {
        .faculty-stats {
            grid-template-columns: 1fr;
        }

        .quick-actions-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .stat-card {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .score-item {
            grid-template-columns: 1fr;
        }

        .score-value {
            text-align: left;
        }

        .submission-header {
            flex-direction: column;
        }
    }
</style>
@endsection
