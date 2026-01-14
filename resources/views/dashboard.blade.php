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
                <div class="card-stat-value">{{ number_format($stats['publications'] ?? 0) }}</div>
                <div class="badge badge-success">{{ $stats['publicationsChange'] ?? '' }}</div>
            </div>
        </div>
        <div class="card col-lg-3 col-md-6">
            <div class="card-body">
                <div class="text-muted">Grants</div>
                <div class="card-stat-value">{{ number_format($stats['grants'] ?? 0) }}</div>
                <div class="badge badge-info">{{ $stats['grantsPending'] ?? 0 }} pending</div>
            </div>
        </div>
        <div class="card col-lg-3 col-md-6">
            <div class="card-body">
                <div class="text-muted">Total Users</div>
                <div class="card-stat-value">{{ number_format($stats['users'] ?? 0) }}</div>
                <div class="badge badge-primary">{{ $stats['usersChange'] ?? '' }}</div>
            </div>
        </div>
        <div class="card col-lg-3 col-md-6">
            <div class="card-body">
                <div class="text-muted">Approvals</div>
                <div class="card-stat-value">{{ number_format($stats['approvals'] ?? 0) }}</div>
                <div class="badge badge-warning">{{ $stats['approvalsOverdue'] ?? 0 }} overdue</div>
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
                            @forelse($recentActivities ?? [] as $activity)
                            <tr>
                                <td>{{ $activity['item'] }}</td>
                                <td><span class="badge badge-{{ $activity['statusClass'] }}">{{ $activity['status'] }}</span></td>
                                <td>{{ $activity['date'] }}</td>
                                <td><button class="btn btn-outline btn-sm">{{ $activity['action'] }}</button></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No recent activity</td>
                            </tr>
                            @endforelse
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

  @push('scripts')
  <script>
      document.addEventListener('DOMContentLoaded', function() {
          // Wait for Chart.js to be available
          function initCharts() {
              if (typeof Chart === 'undefined') {
                  setTimeout(initCharts, 100);
                  return;
              }

              // Publications by Type Chart
              const pubCanvas = document.getElementById('chartPublications');
              if (pubCanvas) {
                  const pubCtx = pubCanvas.getContext('2d');
                  new Chart(pubCtx, {
                      type: 'doughnut',
                      data: {
                          labels: @json($publicationsByType['labels'] ?? ['Journal', 'Conference', 'Book']),
                          datasets: [{
                              data: @json($publicationsByType['data'] ?? [52, 36, 12]),
                              backgroundColor: @json($publicationsByType['colors'] ?? ['#4d8bff', '#0056b3', '#9ca3af'])
                          }]
                      },
                      options: {
                          responsive: true,
                          maintainAspectRatio: true,
                          plugins: {
                              legend: {
                                  position: 'bottom'
                              }
                          }
                      }
                  });
              }

              // Monthly Submissions Chart
              const subCanvas = document.getElementById('chartSubmissions');
              if (subCanvas) {
                  const subCtx = subCanvas.getContext('2d');
                  new Chart(subCtx, {
                      type: 'line',
                      data: {
                          labels: @json($monthlySubmissions['labels'] ?? ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']),
                          datasets: [{
                              label: 'Submissions',
                              data: @json($monthlySubmissions['data'] ?? [5,7,8,9,12,14,11,13,9,10,12,15]),
                              borderColor: '#0056b3',
                              backgroundColor: 'rgba(0,86,179,0.1)',
                              tension: 0.3,
                              fill: true
                          }]
                      },
                      options: {
                          responsive: true,
                          maintainAspectRatio: true,
                          scales: {
                              y: {
                                  beginAtZero: true
                              }
                          }
                      }
                  });
              }
          }

          initCharts();
      });
  </script>
  @endpush
@endsection
