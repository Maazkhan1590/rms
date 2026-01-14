@extends('layouts.admin_modern')

@section('title', 'Dashboard')

@section('content')
  <div class="container">
    <!-- Breadcrumbs and Welcome -->
    <div class="mb-lg">
        <nav aria-label="breadcrumb" class="mb-sm">
            <ol style="display:flex; gap:.5rem; list-style:none; padding:0; margin:0; color:var(--text-secondary);">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li>/</li>
                <li aria-current="page">Dashboard</li>
            </ol>
        </nav>
        <h1 class="mb-sm">Welcome back{{ auth()->check() ? ', '.auth()->user()->name : '' }} 👋</h1>
        <p class="mb-0 text-muted">Here’s what’s happening across your research today.</p>
    </div>

    <!-- Stats Grid -->
    <div class="dashboard-grid mb-xl">
        <div class="card col-lg-3 col-md-6">
            <div class="card-body">
                <div class="text-muted">Publications</div>
                <div class="card-stat-value">128</div>
                <div class="badge badge-success">+12% vs last month</div>
            </div>
        </div>
        <div class="card col-lg-3 col-md-6">
            <div class="card-body">
                <div class="text-muted">Grants</div>
                <div class="card-stat-value">34</div>
                <div class="badge badge-info">5 pending</div>
            </div>
        </div>
        <div class="card col-lg-3 col-md-6">
            <div class="card-body">
                <div class="text-muted">Citations</div>
                <div class="card-stat-value">2,476</div>
                <div class="badge badge-primary">+184 this month</div>
            </div>
        </div>
        <div class="card col-lg-3 col-md-6">
            <div class="card-body">
                <div class="text-muted">Approvals</div>
                <div class="card-stat-value">9</div>
                <div class="badge badge-warning">3 overdue</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card mb-xl">
        <div class="card-body" style="display:flex; gap: var(--spacing-md); flex-wrap:wrap;">
            <a href="#" class="btn btn-primary"><i class="bi bi-upload"></i> Submit Publication</a>
            <a href="#" class="btn btn-success"><i class="bi bi-plus-circle"></i> Add Grant</a>
            <a href="#" class="btn btn-outline"><i class="bi bi-person"></i> Invite Researcher</a>
            <a href="#" class="btn btn-secondary"><i class="bi bi-file-earmark-pdf"></i> Generate Report</a>
        </div>
    </div>

    <div class="row mb-xl">
        <!-- Recent Activity Table -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
                    <h3 class="card-title">Recent Activity</h3>
                    <a href="#" class="btn btn-outline btn-sm">View All</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Publication: Deep Learning in Healthcare</td>
                                <td><span class="badge badge-warning">Pending</span></td>
                                <td>2026-01-02</td>
                                <td><button class="btn btn-outline btn-sm">Review</button></td>
                            </tr>
                            <tr>
                                <td>Grant: AI for Diagnostics</td>
                                <td><span class="badge badge-success">Approved</span></td>
                                <td>2026-01-01</td>
                                <td><button class="btn btn-outline btn-sm">Open</button></td>
                            </tr>
                            <tr>
                                <td>User: Jane Doe requested access</td>
                                <td><span class="badge badge-info">New</span></td>
                                <td>2025-12-30</td>
                                <td><button class="btn btn-outline btn-sm">Approve</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="col-lg-4">
            <div class="card mb-lg">
                <div class="card-header"><h3 class="card-title">Publications by Type</h3></div>
                <div class="card-body">
                    <canvas id="chartPublications"></canvas>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3 class="card-title">Monthly Submissions</h3></div>
                <div class="card-body">
                    <canvas id="chartSubmissions"></canvas>
                </div>
            </div>
        </div>
    </div>

  </div>
@endsection
