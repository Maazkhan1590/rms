@extends('layouts.admin')

@section('page-title', 'Site Content')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title">Site Content Management</h3>
            @can('site_content_create')
            <a href="{{ route('admin.site-contents.create') }}" class="btn btn-primary btn-sm">
                <span class="material-icons-outlined" style="font-size: 16px; vertical-align: middle;">add</span> Add Content
            </a>
            @endcan
        </div>
    </div>
    <div class="card-body">
        @if($sections->count() > 0)
        <div class="mb-3">
            <label>Filter by Section:</label>
            <div class="btn-group" role="group">
                <a href="{{ route('admin.site-contents.index') }}" class="btn btn-sm {{ !request('section') ? 'btn-primary' : 'btn-outline-primary' }}">
                    All
                </a>
                @foreach($sections as $section)
                <a href="{{ route('admin.site-contents.index', ['section' => $section]) }}" class="btn btn-sm {{ request('section') == $section ? 'btn-primary' : 'btn-outline-primary' }}">
                    {{ ucfirst($section) }}
                </a>
                @endforeach
            </div>
        </div>
        @endif

        @if($siteContents->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="siteContentsTable">
                <thead>
                    <tr>
                        <th>Key</th>
                        <th>Label</th>
                        <th>Section</th>
                        <th>Type</th>
                        <th>Value Preview</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siteContents as $content)
                    <tr>
                        <td><code>{{ $content->key }}</code></td>
                        <td>{{ $content->label ?? 'N/A' }}</td>
                        <td>
                            <span class="badge badge-info">{{ ucfirst($content->section) }}</span>
                        </td>
                        <td>
                            <span class="badge badge-secondary">{{ ucfirst($content->type) }}</span>
                        </td>
                        <td>
                            @if($content->value)
                                {{ Str::limit($content->value, 50) }}
                            @else
                                <span class="text-muted">Empty</span>
                            @endif
                        </td>
                        <td>{{ $content->order }}</td>
                        <td>
                            @if($content->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Content actions">
                                @can('site_content_read')
                                <a href="{{ route('admin.site-contents.show', $content) }}" class="btn btn-outline-primary" title="View">
                                    <span class="material-icons-outlined" style="font-size: 16px;">visibility</span>
                                </a>
                                @endcan
                                @can('site_content_update')
                                <a href="{{ route('admin.site-contents.edit', $content) }}" class="btn btn-outline-info" title="Edit">
                                    <span class="material-icons-outlined" style="font-size: 16px;">edit</span>
                                </a>
                                @endcan
                                @can('site_content_delete')
                                <form action="{{ route('admin.site-contents.destroy', $content) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this content?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                        <span class="material-icons-outlined" style="font-size: 16px;">delete</span>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="alert alert-info">
            <p>No site content found. <a href="{{ route('admin.site-contents.create') }}">Create your first content</a></p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    if (window.jQuery) {
        jQuery(function($) {
            if ($.fn.DataTable) {
                $('#siteContentsTable').DataTable({
                    order: [[5, 'asc']],
                    pageLength: 25,
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    responsive: true,
                    columnDefs: [
                        { targets: -1, orderable: false, searchable: false, responsivePriority: 1 }
                    ],
                    language: {
                        lengthMenu: "Show _MENU_ entries",
                        search: "Search:",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "Showing 0 to 0 of 0 entries",
                        infoFiltered: "(filtered from _MAX_ total entries)",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    }
                });
            } else {
                console.error('DataTables plugin not loaded');
            }
        });
    } else {
        console.error('jQuery not loaded');
        // Fallback: wait for jQuery
        window.addEventListener('load', function() {
            if (window.jQuery && window.jQuery.fn.DataTable) {
                jQuery(function($) {
                    $('#siteContentsTable').DataTable({
                        order: [[5, 'asc']],
                        pageLength: 25,
                        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                        responsive: true,
                        columnDefs: [
                            { targets: -1, orderable: false, searchable: false, responsivePriority: 1 }
                        ]
                    });
                });
            }
        });
    }
</script>
@endpush
@endsection
