@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 style="margin: 0; display: inline-block;">
            <i class="fas fa-user-clock"></i> Activity Logs
        </h3>
    </div>

    <div class="card-body">
        <!-- Information Alert -->
        <div class="alert alert-info" style="margin-bottom: 20px;">
            <h5><i class="fas fa-info-circle"></i> Activity Log Information</h5>
            <p><strong>Purpose:</strong> Track user activities and system interactions (logins, submissions, views, etc.)</p>
            <p><strong>What's Logged:</strong> User actions, login/logout events, page views, submissions, and other system interactions</p>
        </div>

        <!-- Search and Filters -->
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" style="margin-bottom: 20px;">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search activities..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="user_id" class="form-control">
                        <option value="">All Users</option>
                        @foreach($users ?? [] as $id => $name)
                            <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="activity_type" class="form-control">
                        <option value="">All Types</option>
                        @foreach($activityTypes ?? [] as $type)
                            <option value="{{ $type }}" {{ request('activity_type') == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-3">
                    <input type="date" name="date_from" class="form-control" placeholder="From Date" 
                           value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="date_to" class="form-control" placeholder="To Date" 
                           value="{{ request('date_to') }}">
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Activity Type</th>
                        <th>Description</th>
                        <th>Related Model</th>
                        <th>IP Address</th>
                        <th>Date & Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>
                                @if($log->user)
                                    <strong>{{ $log->user->name }}</strong>
                                    <br><small class="text-muted">{{ $log->user->email }}</small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ ucfirst($log->activity_type) }}
                                </span>
                            </td>
                            <td>
                                <small>{{ Str::limit($log->description, 80) }}</small>
                            </td>
                            <td>
                                @if($log->related_model_type)
                                    <small>
                                        {{ class_basename($log->related_model_type) }}
                                        @if($log->related_model_id)
                                            #{{ $log->related_model_id }}
                                        @endif
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $log->ip_address ?? '-' }}</small>
                            </td>
                            <td>
                                <small>
                                    {{ $log->created_at->format('M d, Y') }}<br>
                                    {{ $log->created_at->format('H:i:s') }}
                                </small>
                            </td>
                            <td>
                                <a class="btn btn-sm btn-info" href="{{ route('admin.activity-logs.show', $log->id) }}" title="View Details" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                <p style="padding: 2rem; color: #6c757d;">No activity logs found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($logs->hasPages())
            <div style="margin-top: 20px;">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
