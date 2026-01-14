@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-calculator"></i> Scoring Policy Details</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h4>{{ $policy->name }}</h4>
                
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Type</th>
                        <td>
                            <span class="badge badge-info">
                                {{ ucfirst($policy->type) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Category</th>
                        <td>{{ $policy->category ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Subcategory</th>
                        <td>{{ $policy->subcategory ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Points</th>
                        <td><strong style="color: var(--primary); font-size: 1.2em;">{{ number_format($policy->points, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <th>Cap (Maximum)</th>
                        <td>
                            @if($policy->cap)
                                <strong>{{ number_format($policy->cap, 2) }}</strong>
                            @else
                                <span class="text-muted">No cap</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($policy->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Effective Period</th>
                        <td>
                            {{ $policy->effective_from->format('M d, Y') }}
                            @if($policy->effective_to)
                                → {{ $policy->effective_to->format('M d, Y') }}
                            @else
                                → Ongoing
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Policy Version</th>
                        <td>
                            @if($policy->policyVersion)
                                <span class="badge badge-info">
                                    {{ $policy->policyVersion->version_number }} ({{ $policy->policyVersion->year }})
                                </span>
                                @if($policy->policyVersion->description)
                                    <br><small class="text-muted">{{ $policy->policyVersion->description }}</small>
                                @endif
                            @else
                                <span class="text-muted">Not assigned</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Version Identifier</th>
                        <td>{{ $policy->version ?? '-' }}</td>
                    </tr>
                    @if($policy->creator)
                    <tr>
                        <th>Created By</th>
                        <td>{{ $policy->creator->name }} on {{ $policy->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Scoring Rules -->
        @if($policy->rules && $policy->rules->count() > 0)
        <div class="row mt-4">
            <div class="col-md-12">
                <h5><i class="fas fa-list"></i> Scoring Rules</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Rule Name</th>
                                <th>Type</th>
                                <th>Points</th>
                                <th>Priority</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($policy->rules->sortBy('priority') as $rule)
                            <tr>
                                <td>{{ $rule->rule_name }}</td>
                                <td>{{ $rule->rule_type }}</td>
                                <td><strong>{{ number_format($rule->points, 2) }}</strong></td>
                                <td>{{ $rule->priority }}</td>
                                <td>
                                    @if($rule->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
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
        
        <div style="margin-top: 20px;">
            <a href="{{ route('admin.policies.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            @can('policy_update')
                <a href="{{ route('admin.policies.edit', $policy->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endcan
        </div>
    </div>
</div>
@endsection
