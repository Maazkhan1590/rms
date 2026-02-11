@extends('layouts.admin')

@section('page-title', 'View Site Content')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Site Content Details</h3>
    </div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Key</dt>
            <dd class="col-sm-9"><code>{{ $siteContent->key }}</code></dd>

            <dt class="col-sm-3">Label</dt>
            <dd class="col-sm-9">{{ $siteContent->label ?? 'N/A' }}</dd>

            <dt class="col-sm-3">Section</dt>
            <dd class="col-sm-9">
                <span class="badge badge-info">{{ ucfirst($siteContent->section) }}</span>
            </dd>

            <dt class="col-sm-3">Type</dt>
            <dd class="col-sm-9">
                <span class="badge badge-secondary">{{ ucfirst($siteContent->type) }}</span>
            </dd>

            <dt class="col-sm-3">Value</dt>
            <dd class="col-sm-9">
                @if($siteContent->type == 'html')
                    <div>{!! $siteContent->value !!}</div>
                @else
                    <div>{{ $siteContent->value ?? 'Empty' }}</div>
                @endif
            </dd>

            <dt class="col-sm-3">Order</dt>
            <dd class="col-sm-9">{{ $siteContent->order }}</dd>

            <dt class="col-sm-3">Status</dt>
            <dd class="col-sm-9">
                @if($siteContent->is_active)
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-secondary">Inactive</span>
                @endif
            </dd>

            <dt class="col-sm-3">Created</dt>
            <dd class="col-sm-9">{{ $siteContent->created_at->format('M d, Y H:i') }}</dd>

            <dt class="col-sm-3">Updated</dt>
            <dd class="col-sm-9">{{ $siteContent->updated_at->format('M d, Y H:i') }}</dd>
        </dl>

        <div class="mt-4">
            @can('site_content_update')
            <a href="{{ route('admin.site-contents.edit', $siteContent) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            @endcan
            <a href="{{ route('admin.site-contents.index') }}" class="btn btn-secondary">
                Back to List
            </a>
        </div>
    </div>
</div>
@endsection
