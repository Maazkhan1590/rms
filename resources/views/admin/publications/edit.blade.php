@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-edit"></i> Edit Publication</h3>
    </div>
    <div class="card-body">
        <p class="text-muted">Publication editing feature is coming soon.</p>
        <a href="{{ route('admin.publications.show', $publication->id) }}" class="btn btn-info">
            <i class="fas fa-eye"></i> View Publication
        </a>
        <a href="{{ route('admin.publications.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>
@endsection
