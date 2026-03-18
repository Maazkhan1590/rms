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
    #consultancies-table {
        width: 100%;
        margin: 0;
        table-layout: auto;
    }

    .dataTables_wrapper {
        width: 100%;
        overflow-x: visible;
    }

    .dataTables_wrapper .dataTables_scrollBody {
        overflow-x: visible !important;
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
                    <i class="fas fa-briefcase"></i> Consultancies & KT Management
                </h3>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <label>Status:</label>
                <select id="status-filter" class="form-control form-control-sm">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>Type:</label>
                <select id="type-filter" class="form-control form-control-sm">
                    <option value="">All Types</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>Year:</label>
                <select id="year-filter" class="form-control form-control-sm">
                    <option value="">All Years</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
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
            <table id="consultancies-table" class="table table-bordered table-striped table-hover" style="width:100%;">
                <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Project/Consultancy Name</th>
                        <th>Income Type</th>
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
    var table = $('#consultancies-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.consultancies.index") }}',
            type: 'GET',
            data: function(d) {
                d.status = $('#status-filter').val();
                d.type = $('#type-filter').val();
                d.year = $('#year-filter').val();
                d.user_id = $('#user-filter').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'project_consultancy_name', name: 'project_consultancy_name', orderable: false },
            { data: 'income_type', name: 'income_type', orderable: false },
            { data: 'submitted_by', name: 'submitted_by', orderable: true },
            { data: 'year', name: 'year' },
            { data: 'status', name: 'status', orderable: false },
            { data: 'workflow', name: 'workflow', orderable: false },
            { data: 'points', name: 'points_allocated', orderable: false },
            { data: 'submitted', name: 'submitted_at', orderable: false },
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

    $('#status-filter, #type-filter, #year-filter, #user-filter').on('change', function() {
        table.draw();
    });

    $(document).on('submit', '.approve-consultancy-form', function (e) {
        e.preventDefault();
        const form = $(this);
        Swal.fire({
            title: 'Approve Consultancy?',
            text: 'Are you sure you want to approve this consultancy?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, approve',
            confirmButtonColor: '#22c55e'
        }).then((result) => {
            if (result.isConfirmed) {
                form[0].submit();
            }
        });
    });

    $(document).on('click', '.btn-reject-consultancy', function (e) {
        e.preventDefault();
        const consultancyId = $(this).data('consultancy-id');
        Swal.fire({
            title: 'Reject Consultancy?',
            html: '<textarea id="swal-reject-comments" class="swal2-textarea" placeholder="Enter reason for rejection (optional)..." rows="4" style="width: 100%; margin-top: 10px;"></textarea>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, reject',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            preConfirm: () => {
                return document.getElementById('swal-reject-comments').value;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const comments = result.value || '';
                const form = $('<form>', {
                    method: 'POST',
                    action: '/admin/consultancies/' + consultancyId + '/reject'
                });
                form.append($('<input>', { type: 'hidden', name: '_token', value: $('meta[name="csrf-token"]').attr('content') }));
                form.append($('<input>', { type: 'hidden', name: 'comments', value: comments }));
                $('body').append(form);
                form.submit();
            }
        });
    });
});
</script>
@endpush
@endsection
