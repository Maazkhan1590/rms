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
                    <i class="fas fa-calculator"></i> Scoring Policies Management
                </h3>
            </div>
            <div style="margin-top: 10px;">
                @can('policy_create')
                <a href="{{ route('admin.policies.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add New Policy
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Information Alert -->
        <div class="alert alert-info" style="margin-bottom: 20px;">
            <h5><i class="fas fa-info-circle"></i> Scoring Policy Guidelines</h5>
            <p><strong>Policy Integrity:</strong> Approved historical data remains locked. New rules apply prospectively only.</p>
            <p><strong>Types:</strong> Publication, Grant, RTN, Bonus Recognition</p>
            <p><strong>Caps:</strong> Set maximum points per category (e.g., Journals: 120, Conferences: 15, Publications Total: 150)</p>
        </div>

        <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table id="policies-table" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Subcategory</th>
                        <th>Points</th>
                        <th>Cap</th>
                        <th>Policy Version</th>
                        <th>Effective Period</th>
                        <th>Status</th>
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

@push('styles')
<style>
    .card-body .table-responsive { 
        display: block; 
        width: 100%; 
        overflow-x: auto; 
        -webkit-overflow-scrolling: touch; 
        min-height: 200px;
    }
    #policies-table { 
        width: 100% !important; 
        margin: 0; 
        table-layout: auto; 
        min-width: 1200px;
    }
    .dataTables_wrapper { 
        width: 100%; 
        overflow-x: auto;
    }
    .dataTables_wrapper .dataTables_scrollBody { 
        overflow-x: auto !important; 
    }
    .dataTables_wrapper .dataTables_scrollHead { 
        overflow-x: auto !important; 
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('#policies-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.policies.index") }}',
            type: 'GET'
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name', orderable: true },
            { data: 'type', name: 'type', orderable: true },
            { data: 'category', name: 'category', orderable: true },
            { data: 'subcategory', name: 'subcategory', orderable: true },
            { data: 'points', name: 'points', orderable: true },
            { data: 'cap', name: 'cap', orderable: true },
            { data: 'policy_version', name: 'policy_version_id', orderable: true },
            { data: 'effective_period', name: 'effective_from', orderable: true },
            { data: 'status', name: 'is_active', orderable: true },
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
