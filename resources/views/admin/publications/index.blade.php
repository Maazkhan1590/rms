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
                    <i class="fas fa-book"></i> Publications Management
                </h3>
            </div>
            <div style="margin-top: 10px; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <!-- Status Filters -->
                <a href="{{ route('admin.publications.index', array_merge(request()->except('status'), ['status' => 'pending'])) }}" 
                   class="btn btn-sm {{ request('status') == 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                    <i class="fas fa-clock"></i> Pending
                </a>
                <a href="{{ route('admin.publications.index', array_merge(request()->except('status'), ['status' => 'approved'])) }}" 
                   class="btn btn-sm {{ request('status') == 'approved' ? 'btn-success' : 'btn-outline-success' }}">
                    <i class="fas fa-check"></i> Approved
                </a>
                <a href="{{ route('admin.publications.index', array_merge(request()->except('status'), ['status' => 'rejected'])) }}" 
                   class="btn btn-sm {{ request('status') == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                    <i class="fas fa-times"></i> Rejected
                </a>
                <a href="{{ route('admin.publications.index', request()->except('status')) }}" 
                   class="btn btn-sm {{ !request('status') ? 'btn-secondary' : 'btn-outline-secondary' }}">
                    <i class="fas fa-list"></i> All
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="publications-table" class="table table-bordered table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Author</th>
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

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Publication</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="reject_reason">Reason (Optional)</label>
                        <textarea class="form-control" id="reject_reason" name="reason" rows="3" 
                                  placeholder="Enter reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Publication</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .btn-xs, .btn-sm {
        padding: 4px 8px !important;
        font-size: 12px !important;
        line-height: 1.5 !important;
        border-radius: 3px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        text-decoration: none !important;
        border: none !important;
        cursor: pointer !important;
        font-weight: 500 !important;
        transition: all 0.2s ease !important;
    }

    .btn-xs:hover, .btn-sm:hover {
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
    }

    .btn-info {
        background-color: #3b82f6 !important;
        color: white !important;
    }

    .btn-success {
        background-color: #22c55e !important;
        color: white !important;
    }

    .btn-danger {
        background-color: #ef4444 !important;
        color: white !important;
    }

    .btn-info:hover {
        background-color: #2563eb !important;
    }

    .btn-success:hover {
        background-color: #16a34a !important;
    }

    .btn-danger:hover {
        background-color: #dc2626 !important;
    }

    table tbody td:last-child {
        min-width: 200px;
    }

    table tbody td:last-child > div {
        display: flex !important;
        gap: 5px !important;
        flex-wrap: wrap !important;
        align-items: center !important;
    }

    #publications-table_wrapper .dt-toolbar {
        margin-bottom: 15px;
    }
</style>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#publications-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.publications.index') }}",
                data: function(d) {
                    d.status = "{{ request('status') }}";
                }
            },
            columns: [
                { 
                    data: null,
                    name: 'sr_no',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        // Calculate serial number: start index + row index + 1
                        var start = meta.settings._iDisplayStart || 0;
                        return start + meta.row + 1;
                    }
                },
                { data: 'title', name: 'title', orderable: false },
                { data: 'type', name: 'type', orderable: false },
                { data: 'author', name: 'author' },
                { data: 'year', name: 'year' },
                { data: 'status', name: 'status', orderable: false },
                { data: 'workflow', name: 'workflow', orderable: false },
                { data: 'points', name: 'points', orderable: false },
                { data: 'submitted', name: 'submitted' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            order: [[4, 'desc']], // Order by Year descending (changed from ID)
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            language: {
                processing: '<i class="fas fa-spinner fa-spin"></i> Loading publications...',
                lengthMenu: "Show _MENU_ entries",
                search: "Search:",
                info: "Showing _START_ to _END_ of _TOTAL_ publications",
                infoEmpty: "Showing 0 to 0 of 0 publications",
                infoFiltered: "(filtered from _MAX_ total publications)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            responsive: true
        });

    });

    function showRejectModal(publicationId) {
        const form = document.getElementById('rejectForm');
        form.action = '/admin/publications/' + publicationId + '/reject';
        $('#rejectModal').modal('show');
    }
</script>
@endsection
