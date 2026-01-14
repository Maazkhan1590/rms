@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 style="margin: 0; display: inline-block;">
            <i class="fas fa-clipboard-list"></i> Audit Logs
        </h3>
    </div>

    <div class="card-body">
        <!-- Information Alert -->
        <div class="alert alert-info" style="margin-bottom: 20px;">
            <h5><i class="fas fa-info-circle"></i> Audit Log Information</h5>
            <p><strong>Purpose:</strong> Track all system changes and model modifications with complete before/after values.</p>
            <p><strong>What's Logged:</strong> All create, update, delete, and status change actions on models (Publications, Grants, Policies, Users, etc.)</p>
        </div>

        <!-- Search and Filters -->
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" style="margin-bottom: 20px;">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search logs..." 
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
                    <select name="action" class="form-control">
                        <option value="">All Actions</option>
                        @foreach($actions ?? [] as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ $action }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="model_type" class="form-control">
                        <option value="">All Models</option>
                        @foreach($modelTypes ?? [] as $modelType)
                            <option value="{{ $modelType }}" {{ request('model_type') == $modelType ? 'selected' : '' }}>
                                {{ class_basename($modelType) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-secondary">
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
                        <th>Action</th>
                        <th>Model</th>
                        <th>Model ID</th>
                        <th>Changes</th>
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
                                    <span class="text-muted">System</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td>
                                <small>{{ class_basename($log->model_type) }}</small>
                            </td>
                            <td>{{ $log->model_id }}</td>
                            <td>
                                @if($log->changes && count($log->changes) > 0)
                                    <span class="badge badge-warning">{{ count($log->changes) }} fields changed</span>
                                @elseif($log->action == 'created')
                                    <span class="badge badge-success">Created</span>
                                @elseif($log->action == 'deleted')
                                    <span class="badge badge-danger">Deleted</span>
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
                                <a class="btn btn-sm btn-info" href="{{ route('admin.audit-logs.show', $log->id) }}" title="View Details" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">
                                <p style="padding: 2rem; color: #6c757d;">No audit logs found.</p>
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
