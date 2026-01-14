@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-clipboard-list"></i> Audit Log Details</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">User</th>
                        <td>
                            @if($auditLog->user)
                                <strong>{{ $auditLog->user->name }}</strong>
                                <br><small class="text-muted">{{ $auditLog->user->email }}</small>
                            @else
                                <span class="text-muted">System</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Action</th>
                        <td>
                            <span class="badge badge-info">{{ $auditLog->action }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Model Type</th>
                        <td>{{ $auditLog->model_type }}</td>
                    </tr>
                    <tr>
                        <th>Model ID</th>
                        <td>{{ $auditLog->model_id }}</td>
                    </tr>
                    <tr>
                        <th>IP Address</th>
                        <td>{{ $auditLog->ip_address ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>User Agent</th>
                        <td>
                            <small>{{ $auditLog->user_agent ?? '-' }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Date & Time</th>
                        <td>
                            {{ $auditLog->created_at->format('M d, Y H:i:s') }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Changes -->
        @if($auditLog->changes && count($auditLog->changes) > 0)
        <div class="row mt-4">
            <div class="col-md-12">
                <h5><i class="fas fa-exchange-alt"></i> Changes Made</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Old Value</th>
                                <th>New Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auditLog->changes as $field => $change)
                            <tr>
                                <td><strong>{{ $field }}</strong></td>
                                <td>
                                    @if(isset($change['old']))
                                        <span style="background: #fee2e2; padding: 2px 6px; border-radius: 3px;">
                                            {{ is_array($change['old']) ? json_encode($change['old']) : $change['old'] }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($change['new']))
                                        <span style="background: #d1fae5; padding: 2px 6px; border-radius: 3px;">
                                            {{ is_array($change['new']) ? json_encode($change['new']) : $change['new'] }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Old Values -->
        @if($auditLog->old_values && count($auditLog->old_values) > 0)
        <div class="row mt-4">
            <div class="col-md-6">
                <h5><i class="fas fa-history"></i> Previous State</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auditLog->old_values as $field => $value)
                            <tr>
                                <td><strong>{{ $field }}</strong></td>
                                <td>
                                    <small>{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : $value }}</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- New Values -->
        @if($auditLog->new_values && count($auditLog->new_values) > 0)
        <div class="row mt-4">
            <div class="col-md-6">
                <h5><i class="fas fa-check-circle"></i> New State</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auditLog->new_values as $field => $value)
                            <tr>
                                <td><strong>{{ $field }}</strong></td>
                                <td>
                                    <small>{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : $value }}</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
        
        <div style="margin-top: 20px;">
            <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
</div>
@endsection
