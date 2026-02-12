@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-envelope"></i> Email Log Details</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Log ID</th>
                        <td>{{ $emailLog->id }}</td>
                    </tr>
                    <tr>
                        <th>Recipient Name</th>
                        <td>
                            <strong>{{ $emailLog->recipient_name ?? 'N/A' }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <th>Recipient Email</th>
                        <td>
                            <a href="mailto:{{ $emailLog->recipient_email }}">{{ $emailLog->recipient_email }}</a>
                        </td>
                    </tr>
                    <tr>
                        <th>Subject</th>
                        <td>{{ $emailLog->subject }}</td>
                    </tr>
                    <tr>
                        <th>Associated User</th>
                        <td>
                            @if($emailLog->user)
                                <strong>{{ $emailLog->user->name }}</strong>
                                <br><small class="text-muted">{{ $emailLog->user->email }}</small>
                            @else
                                <span class="text-muted">System Email</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Notification Type</th>
                        <td>
                            <span class="badge badge-secondary">{{ $emailLog->notification_name }}</span>
                            @if($emailLog->notification_type)
                                <br><small class="text-muted">{{ $emailLog->notification_type }}</small>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge badge-{{ $emailLog->status_color }}">
                                {{ ucfirst($emailLog->status) }}
                            </span>
                        </td>
                    </tr>
                    @if($emailLog->error_message)
                    <tr>
                        <th>Error Message</th>
                        <td>
                            <div class="alert alert-danger" style="margin: 0;">
                                {{ $emailLog->error_message }}
                            </div>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <th>Sent At</th>
                        <td>
                            @if($emailLog->sent_at)
                                {{ $emailLog->sent_at->format('M d, Y H:i:s') }}
                            @else
                                <span class="text-muted">Not sent yet</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>
                            {{ $emailLog->created_at->format('M d, Y H:i:s') }}
                            <small class="text-muted">({{ $emailLog->created_at->diffForHumans() }})</small>
                        </td>
                    </tr>
                    <tr>
                        <th>IP Address</th>
                        <td>{{ $emailLog->ip_address ?? '-' }}</td>
                    </tr>
                    @if($emailLog->metadata && is_array($emailLog->metadata))
                    <tr>
                        <th>Metadata</th>
                        <td>
                            <pre style="background: #f5f5f5; padding: 10px; border-radius: 4px; max-height: 200px; overflow-y: auto;">{{ json_encode($emailLog->metadata, JSON_PRETTY_PRINT) }}</pre>
                        </td>
                    </tr>
                    @endif
                </table>

                @if($emailLog->body)
                <div class="mt-4">
                    <h5><i class="fas fa-file-alt"></i> Email Body Content</h5>
                    <div class="card">
                        <div class="card-body" style="max-height: 400px; overflow-y: auto; background: #f8f9fa;">
                            <small style="white-space: pre-wrap; font-family: monospace;">{{ Str::limit($emailLog->body, 2000) }}</small>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Quick Info</h5>
                    </div>
                    <div class="card-body">
                        <dl>
                            <dt>Email Status:</dt>
                            <dd>
                                <span class="badge badge-{{ $emailLog->status_color }}">
                                    {{ ucfirst($emailLog->status) }}
                                </span>
                            </dd>

                            <dt class="mt-3">Delivery Status:</dt>
                            <dd>
                                @if($emailLog->status === 'sent')
                                    <i class="fas fa-check-circle text-success"></i> Successfully Sent
                                @elseif($emailLog->status === 'failed')
                                    <i class="fas fa-times-circle text-danger"></i> Failed to Send
                                @elseif($emailLog->status === 'queued')
                                    <i class="fas fa-clock text-warning"></i> Queued for Sending
                                @else
                                    <i class="fas fa-question-circle text-secondary"></i> Unknown
                                @endif
                            </dd>

                            <dt class="mt-3">Time Since Sent:</dt>
                            <dd>
                                @if($emailLog->sent_at)
                                    {{ $emailLog->sent_at->diffForHumans() }}
                                @else
                                    <span class="text-muted">Not sent</span>
                                @endif
                            </dd>

                            @if($emailLog->user)
                            <dt class="mt-3">Related User ID:</dt>
                            <dd>#{{ $emailLog->user_id }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 20px;">
            <a href="{{ route('admin.email-logs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            
            @if($emailLog->status === 'failed')
                <button class="btn btn-warning" title="Retry sending this email (Feature not implemented)">
                    <i class="fas fa-redo"></i> Retry Send
                </button>
            @endif
        </div>
    </div>
</div>
@endsection
