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
                    <i class="fas fa-money-bill-wave"></i> Grants Management
                </h3>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="grants-table" class="table table-bordered table-striped table-hover" style="min-width: 1200px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Role</th>
                        <th>External/Internal</th>
                        <th>Sponsor</th>
                        <th>Amount (OMR)</th>
                        <th>Units</th>
                        <th>User</th>
                        <th>Year</th>
                        <th>Status</th>
                        <th>Workflow</th>
                        <th>Points</th>
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

@push('scripts')
<script>
$(document).ready(function() {
    $('#grants-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.grants.index") }}',
            type: 'GET'
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'title', name: 'title', orderable: false },
            { data: 'grant_type', name: 'grant_type', orderable: false },
            { data: 'role', name: 'role', orderable: false },
            { data: 'external_internal', name: 'external_internal', orderable: false },
            { data: 'sponsor_name', name: 'sponsor_name', orderable: false },
            { data: 'amount_omr', name: 'amount_omr', orderable: false },
            { data: 'units', name: 'units', orderable: false },
            { data: 'submitter_name', name: 'submitted_by', orderable: true },
            { data: 'award_year', name: 'award_year' },
            { data: 'grant_status', name: 'grant_status', orderable: false },
            { data: 'workflow_status', name: 'workflow.status', orderable: false },
            { data: 'points_allocated', name: 'points_allocated', orderable: false },
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
        columnDefs: [
            { targets: 0, orderable: true, searchable: true, className: '' }
        ]
    });
});
</script>
@endpush

@endsection
