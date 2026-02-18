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
    }
    #editorial-appointments-table {
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
                    <i class="fas fa-edit"></i> Editorial Appointments Management
                </h3>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <label>Status:</label>
                <select id="status-filter" class="form-control form-control-sm">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Year:</label>
                <select id="year-filter" class="form-control form-control-sm">
                    <option value="">All Years</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>User:</label>
                <select id="user-filter" class="form-control form-control-sm">
                    <option value="">All Users</option>
                    @foreach($users as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table id="editorial-appointments-table" class="table table-bordered table-striped table-hover" style="width:100%;">
                <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Journal/Conference</th>
                        <th>Role</th>
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
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#editorial-appointments-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.editorial-appointments.index") }}',
            data: function(d) {
                d.status = $('#status-filter').val();
                d.year = $('#year-filter').val();
                d.user_id = $('#user-filter').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'journal_conference', name: 'journal_conference' },
            { data: 'role', name: 'role' },
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
            processing: '<i class="fas fa-spinner fa-spin"></i> Loading editorial appointments...'
        }
    });

    $('#status-filter, #year-filter, #user-filter').on('change', function() {
        table.draw();
    });
});
</script>
@endpush
@endsection
