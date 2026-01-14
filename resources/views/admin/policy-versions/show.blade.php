@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-code-branch"></i> Policy Version Details</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h4>Version {{ $policyVersion->version_number }}</h4>
                
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Version Number</th>
                        <td><strong>{{ $policyVersion->version_number }}</strong></td>
                    </tr>
                    <tr>
                        <th>Year</th>
                        <td>{{ $policyVersion->year }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($policyVersion->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    @if($policyVersion->description)
                    <tr>
                        <th>Description</th>
                        <td>{{ $policyVersion->description }}</td>
                    </tr>
                    @endif
                    @if($policyVersion->creator)
                    <tr>
                        <th>Created By</th>
                        <td>{{ $policyVersion->creator->name }} on {{ $policyVersion->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Scoring Policies in this Version -->
        @if($policyVersion->scoringPolicies && $policyVersion->scoringPolicies->count() > 0)
        <div class="row mt-4">
            <div class="col-md-12">
                <h5><i class="fas fa-calculator"></i> Scoring Policies ({{ $policyVersion->scoringPolicies->count() }})</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Points</th>
                                <th>Cap</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($policyVersion->scoringPolicies as $policy)
                            <tr>
                                <td>{{ $policy->name }}</td>
                                <td><span class="badge badge-info">{{ ucfirst($policy->type) }}</span></td>
                                <td>{{ $policy->category ?? '-' }}</td>
                                <td><strong>{{ number_format($policy->points, 2) }}</strong></td>
                                <td>{{ $policy->cap ? number_format($policy->cap, 2) : '-' }}</td>
                                <td>
                                    @if($policy->is_active)
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
        @else
        <div class="alert alert-info mt-4">
            <p>No scoring policies assigned to this version yet.</p>
            <a href="{{ route('admin.policies.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Policy
            </a>
        </div>
        @endif
        
        <div style="margin-top: 20px;">
            <a href="{{ route('admin.policy-versions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            @can('policy_update')
                <a href="{{ route('admin.policy-versions.edit', $policyVersion->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @if(!$policyVersion->is_active)
                <form action="{{ route('admin.policy-versions.activate', $policyVersion->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Activate this policy version? This will deactivate all other versions.');">
                        <i class="fas fa-check"></i> Activate Version
                    </button>
                </form>
                @endif
            @endcan
        </div>
    </div>
</div>
@endsection
