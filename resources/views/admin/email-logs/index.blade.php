@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 style="margin: 0; display: inline-block;">
            <i class="fas fa-envelope"></i> Email Logs
        </h3>
    </div>

    <div class="card-body">
        <!-- Information Alert -->
        <div class="alert alert-info" style="margin-bottom: 20px;">
            <h5><i class="fas fa-info-circle"></i> Email Log Information</h5>
            <p><strong>Purpose:</strong> Track all emails sent from the system including notifications and alerts</p>
            <p><strong>What's Logged:</strong> Recipient details, subject, status, notification type, and timestamps</p>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6 class="card-title">Sent Emails</h6>
                        <h3>{{ $logs->where('status', 'sent')->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h6 class="card-title">Failed Emails</h6>
                        <h3>{{ $logs->where('status', 'failed')->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6 class="card-title">Queued Emails</h6>
                        <h3>{{ $logs->where('status', 'queued')->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6 class="card-title">Total Emails</h6>
                        <h3>{{ $logs->total() }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <form method="GET" action="{{ route('admin.email-logs.index') }}" style="margin-bottom: 20px;">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search emails..." 
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
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        @foreach($statuses ?? [] as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="notification_type" class="form-control">
                        <option value="">All Types</option>
                        @foreach($notificationTypes ?? [] as $type)
                            <option value="{{ $type['value'] }}" {{ request('notification_type') == $type['value'] ? 'selected' : '' }}>
                                {{ $type['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <a href="{{ route('admin.email-logs.index') }}" class="btn btn-secondary">
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
                <div class="col-md-3">
                    <input type="email" name="recipient_email" class="form-control" placeholder="Recipient Email" 
                           value="{{ request('recipient_email') }}">
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Recipient</th>
                        <th>Subject</th>
                        <th>User</th>
                        <th>Notification Type</th>
                        <th>Status</th>
                        <th>Date & Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>
                                <strong>{{ $log->recipient_name ?? 'N/A' }}</strong>
                                <br><small class="text-muted">{{ $log->recipient_email }}</small>
                            </td>
                            <td>
                                <small>{{ Str::limit($log->subject, 50) }}</small>
                            </td>
                            <td>
                                @if($log->user)
                                    <small>
                                        {{ $log->user->name }}
                                        <br><span class="text-muted">{{ $log->user->email }}</span>
                                    </small>
                                @else
                                    <span class="text-muted">System</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $log->notification_name }}</small>
                            </td>
                            <td>
                                <span class="badge badge-{{ $log->status_color }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                            <td>
                                <small>
                                    {{ $log->created_at->format('M d, Y') }}<br>
                                    {{ $log->created_at->format('H:i:s') }}
                                </small>
                            </td>
                            <td>
                                <a class="btn btn-sm btn-info" href="{{ route('admin.email-logs.show', $log->id) }}" title="View Details" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                <p style="padding: 2rem; color: #6c757d;">No email logs found.</p>
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
