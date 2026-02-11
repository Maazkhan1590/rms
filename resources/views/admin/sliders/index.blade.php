@extends('layouts.admin')

@section('page-title', 'Sliders')

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
            <h3 class="card-title">Slider Images</h3>
            @can('slider_create')
            <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary btn-sm">
                <span class="material-icons-outlined" style="font-size: 16px; vertical-align: middle;">add</span> Add Slider
            </a>
            @endcan
        </div>
    </div>
    <div class="card-body">
        @if($sliders->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="slidersTable">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Tag</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sliders as $slider)
                    <tr>
                        <td>{{ $slider->order }}</td>
                        <td>
                            @if($slider->image_url)
                                @if(filter_var($slider->image_url, FILTER_VALIDATE_URL))
                                    <img src="{{ $slider->image_url }}" alt="Slider" style="max-width: 100px; max-height: 60px; object-fit: cover; border-radius: 4px;">
                                @else
                                    <img src="{{ asset('storage/' . $slider->image_url) }}" alt="Slider" style="max-width: 100px; max-height: 60px; object-fit: cover; border-radius: 4px;">
                                @endif
                            @else
                                <span class="text-muted">No image</span>
                            @endif
                        </td>
                        <td>{{ $slider->title ?? 'N/A' }}</td>
                        <td>
                            @if($slider->tag)
                                <span class="badge badge-info">{{ $slider->tag }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($slider->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Slider actions">
                                @can('slider_read')
                                <a href="{{ route('admin.sliders.show', $slider) }}" class="btn btn-outline-primary" title="View">
                                    <span class="material-icons-outlined" style="font-size: 16px;">visibility</span>
                                </a>
                                @endcan
                                @can('slider_update')
                                <a href="{{ route('admin.sliders.edit', $slider) }}" class="btn btn-outline-info" title="Edit">
                                    <span class="material-icons-outlined" style="font-size: 16px;">edit</span>
                                </a>
                                @endcan
                                @can('slider_delete')
                                <form action="{{ route('admin.sliders.destroy', $slider) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this slider?');">
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
            <p>No sliders found. <a href="{{ route('admin.sliders.create') }}">Create your first slider</a></p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    if (window.jQuery) {
        jQuery(function($) {
            if ($.fn.DataTable) {
                $('#slidersTable').DataTable({
                    order: [[0, 'asc']],
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
                    $('#slidersTable').DataTable({
                        order: [[0, 'asc']],
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
