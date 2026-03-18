@extends('layouts.admin')

@section('content')
@push('styles')
<style>
    table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control::before, table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control::before {
        background-color: #0056b300 !important;
        box-shadow: none !important;
    }
    
    .card-body .table-responsive {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    #partnerships-table {
        width: 100%;
        margin: 0;
        table-layout: auto;
    }
    
    .dataTables_wrapper {
        width: 100%;
        overflow-x: visible;
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
                    <i class="fas fa-handshake"></i> Partnerships & MOUs Management
                </h3>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table id="partnerships-table" class="table table-bordered table-striped table-hover" style="width:100%;">
                <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Partner Organization</th>
                        <th>Type</th>
                        <th>Submitted By</th>
                        <th>Year</th>
                        <th>Status</th>
                        <th>Workflow</th>
                        <th>Points</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTables will populate this -->
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#partnerships-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.partnerships.index") }}'
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'partner_organization', name: 'partner_organization' },
            { data: 'type', name: 'type' },
            { data: 'submitted_by', name: 'submitted_by' },
            { data: 'year', name: 'year' },
            { data: 'status', name: 'status' },
            { data: 'workflow', name: 'workflow', orderable: false },
            { data: 'points', name: 'points_allocated' },
            { data: 'submitted', name: 'submitted_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            processing: '<i class="fas fa-spinner fa-spin"></i> Loading partnerships...'
        }
    });
});
</script>
@endpush
@endsection
