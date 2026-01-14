@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-plus"></i> Create New Publication</h3>
    </div>
    <div class="card-body">
        <p class="text-muted">This feature is coming soon. Please use the public publication submission form.</p>
        <a href="{{ route('publications.create') }}" class="btn btn-primary">
            <i class="fas fa-external-link-alt"></i> Go to Publication Submission Form
        </a>
        <a href="{{ route('admin.publications.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>
@endsection
