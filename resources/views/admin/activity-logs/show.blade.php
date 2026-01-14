@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-user-clock"></i> Activity Log Details</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">User</th>
                        <td>
                            @if($activityLog->user)
                                <strong>{{ $activityLog->user->name }}</strong>
                                <br><small class="text-muted">{{ $activityLog->user->email }}</small>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Activity Type</th>
                        <td>
                            <span class="badge badge-info">{{ ucfirst($activityLog->activity_type) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td>{{ $activityLog->description }}</td>
                    </tr>
                    @if($activityLog->related_model_type)
                    <tr>
                        <th>Related Model</th>
                        <td>
                            <strong>{{ class_basename($activityLog->related_model_type) }}</strong>
                            @if($activityLog->related_model_id)
                                (ID: {{ $activityLog->related_model_id }})
                            @endif
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <th>IP Address</th>
                        <td>{{ $activityLog->ip_address ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Date & Time</th>
                        <td>
                            {{ $activityLog->created_at->format('M d, Y H:i:s') }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div style="margin-top: 20px;">
            <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
</div>
@endsection
