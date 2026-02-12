@extends('layouts.admin')

@section('content')
@push('styles')
<style>
    .card-body .table-responsive {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .card-body .table-responsive table {
        width: 100%;
        margin: 0;
        table-layout: auto;
    }
</style>
@endpush
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
                    <i class="fas fa-trophy"></i> Bonus Recognitions Management
                </h3>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table id="bonus-table" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>User</th>
                        <th>Organization</th>
                        <th>Evidence Link</th>
                        <th>Year</th>
                        <th>Status</th>
                        <th>Workflow</th>
                        <th>Points</th>
                        <th>Submitted</th>
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
    $('#bonus-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.bonus-recognitions.index") }}',
            type: 'GET'
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'title', name: 'title', orderable: false },
            { data: 'recognition_type', name: 'recognition_type', orderable: false },
            { data: 'user_name', name: 'user_id', orderable: true },
            { data: 'organization', name: 'organization', orderable: false },
            { data: 'evidence_link', name: 'evidence_link', orderable: false },
            { data: 'year', name: 'year' },
            { data: 'status', name: 'status', orderable: false },
            { data: 'workflow_status', name: 'workflow.status', orderable: false },
            { data: 'points_allocated', name: 'points_allocated', orderable: false },
            { data: 'submitted_at', name: 'submitted_at' },
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
