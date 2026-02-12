@extends('layouts.admin')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <div>
                <h3 style="margin: 0; display: inline-block;">
                    <i class="fas fa-code-branch"></i> Policy Versions Management
                </h3>
            </div>
            <div style="margin-top: 10px;">
                @can('policy_create')
                <a href="{{ route('admin.policy-versions.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add New Version
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Information Alert -->
        <div class="alert alert-info" style="margin-bottom: 20px;">
            <h5><i class="fas fa-info-circle"></i> Policy Versioning</h5>
            <p><strong>Purpose:</strong> Manage year-wise policy versions to ensure historical data integrity.</p>
            <p><strong>Locking:</strong> Approved historical data remains locked. New rules apply prospectively only.</p>
            <p><strong>Active Version:</strong> Only one version can be active at a time. Activating a new version deactivates others.</p>
        </div>

        <div class="card-body">
            <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table id="policy-versions-table" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Version Number</th>
                            <th>Year</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Policies</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will populate this via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card-body .table-responsive { display: block; width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    #policy-versions-table { width: 100%; margin: 0; table-layout: auto; }
    .dataTables_wrapper { width: 100%; overflow-x: visible; }
    .dataTables_wrapper .dataTables_scrollBody { overflow-x: visible !important; }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('#policy-versions-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.policy-versions.index") }}',
            type: 'GET'
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'version_number', name: 'version_number', orderable: true },
            { data: 'year', name: 'year', orderable: true },
            { data: 'description', name: 'description', orderable: false },
            { data: 'status', name: 'is_active', orderable: true },
            { data: 'policies_count', name: 'scoring_policies', orderable: true },
            { data: 'created_at', name: 'created_at', orderable: true },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50, 100], [10, 15, 25, 50, 100]],
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
        },
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        select: false,
        responsive: false,
        autoWidth: false,
        columnDefs: [
            { targets: 0, orderable: true, searchable: true, className: '' }
        ]
    });
});
</script>
@endpush
@endsection
