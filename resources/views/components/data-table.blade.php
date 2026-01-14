@props([
    'id' => 'dataTable',
    'columns' => [],
    'data' => [],
    'searchable' => true,
    'sortable' => true,
    'selectable' => false,
    'exportable' => true,
    'actions' => true,
    'bulkActions' => [],
    'perPageOptions' => [10, 25, 50, 100],
    'defaultPerPage' => 10,
    'mobileCards' => true,
    'expandable' => false,
])

<div class="data-table-wrapper" id="{{ $id }}-wrapper">
    <!-- Table Header Controls -->
    <div class="data-table-header">
        <div class="row align-items-center mb-3">
            <!-- Left: Entries selector and bulk actions -->
            <div class="col-md-6 col-12 mb-2 mb-md-0">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <!-- Per Page Selector -->
                    <div class="d-flex align-items-center gap-2">
                        <label class="mb-0 text-muted small">Show</label>
                        <select class="form-select form-select-sm per-page-selector" style="width: auto;">
                            @foreach($perPageOptions as $option)
                                <option value="{{ $option }}" {{ $option === $defaultPerPage ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                        <label class="mb-0 text-muted small">entries</label>
                    </div>

                    <!-- Bulk Actions -->
                    @if($selectable && count($bulkActions) > 0)
                        <div class="bulk-actions-container" style="display: none;">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-lightning-fill me-1"></i>
                                    Bulk Actions
                                    <span class="badge bg-primary ms-1 selected-count">0</span>
                                </button>
                                <ul class="dropdown-menu">
                                    @foreach($bulkActions as $action)
                                        <li>
                                            <a class="dropdown-item bulk-action-item" href="#" data-action="{{ $action['value'] }}">
                                                <i class="bi {{ $action['icon'] ?? 'bi-check' }} me-2"></i>
                                                {{ $action['label'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right: Search and export -->
            <div class="col-md-6 col-12">
                <div class="d-flex align-items-center gap-2 justify-content-md-end">
                    <!-- Global Search -->
                    @if($searchable)
                        <div class="input-group input-group-sm" style="max-width: 250px;">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control global-search" placeholder="Search...">
                        </div>
                    @endif

                    <!-- Export Buttons -->
                    @if($exportable)
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary export-csv" title="Export to CSV">
                                <i class="bi bi-file-earmark-spreadsheet"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary export-print" title="Print">
                                <i class="bi bi-printer"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary export-pdf" title="Export to PDF">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop Table View -->
    <div class="table-responsive desktop-view">
        <table class="table table-hover data-table" id="{{ $id }}">
            <thead>
                <tr>
                    @if($selectable)
                        <th style="width: 40px;">
                            <input type="checkbox" class="form-check-input select-all">
                        </th>
                    @endif

                    @foreach($columns as $column)
                        <th 
                            class="{{ $sortable && ($column['sortable'] ?? true) ? 'sortable' : '' }}"
                            data-column="{{ $column['field'] }}"
                            data-type="{{ $column['type'] ?? 'string' }}"
                            style="{{ isset($column['width']) ? 'width: ' . $column['width'] : '' }}"
                        >
                            <div class="d-flex align-items-center justify-content-between">
                                <span>{{ $column['label'] }}</span>
                                @if($sortable && ($column['sortable'] ?? true))
                                    <span class="sort-icon">
                                        <i class="bi bi-arrow-down-up text-muted"></i>
                                    </span>
                                @endif
                            </div>
                        </th>
                    @endforeach

                    @if($actions)
                        <th style="width: 100px;" class="text-center">Actions</th>
                    @endif
                </tr>

                <!-- Column Filters -->
                <tr class="filter-row">
                    @if($selectable)
                        <th></th>
                    @endif

                    @foreach($columns as $column)
                        <th>
                            @if($column['filterable'] ?? false)
                                <input 
                                    type="text" 
                                    class="form-control form-control-sm column-filter" 
                                    placeholder="Filter..."
                                    data-column="{{ $column['field'] }}"
                                >
                            @endif
                        </th>
                    @endforeach

                    @if($actions)
                        <th></th>
                    @endif
                </tr>
            </thead>
            <tbody class="table-body">
                <!-- Loading State -->
                <tr class="loading-state">
                    <td colspan="100%">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2 mb-0">Loading data...</p>
                        </div>
                    </td>
                </tr>

                <!-- No Data State -->
                <tr class="no-data-state" style="display: none;">
                    <td colspan="100%">
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">No data available</h5>
                            <p class="text-muted">There are no records to display.</p>
                        </div>
                    </td>
                </tr>

                <!-- Error State -->
                <tr class="error-state" style="display: none;">
                    <td colspan="100%">
                        <div class="text-center py-5">
                            <i class="bi bi-exclamation-triangle display-1 text-danger"></i>
                            <h5 class="mt-3 text-danger">Error loading data</h5>
                            <p class="text-muted error-message"></p>
                            <button class="btn btn-primary btn-sm retry-load">
                                <i class="bi bi-arrow-clockwise me-1"></i>
                                Retry
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Data rows will be inserted here by JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    @if($mobileCards)
        <div class="mobile-view" style="display: none;">
            <div class="mobile-cards-container">
                <!-- Cards will be inserted here by JavaScript -->
            </div>
        </div>
    @endif

    <!-- Table Footer -->
    <div class="data-table-footer">
        <div class="row align-items-center mt-3">
            <div class="col-md-6 col-12 mb-2 mb-md-0">
                <p class="text-muted small mb-0 table-info">
                    Showing <span class="start-entry">0</span> to <span class="end-entry">0</span> of <span class="total-entries">0</span> entries
                    <span class="filtered-info" style="display: none;">
                        (filtered from <span class="total-entries-unfiltered">0</span> total entries)
                    </span>
                </p>
            </div>
            <div class="col-md-6 col-12">
                <nav>
                    <ul class="pagination pagination-sm justify-content-md-end justify-content-center mb-0">
                        <!-- Pagination will be inserted here by JavaScript -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Data Table Styles */
    .data-table-wrapper {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .data-table {
        margin-bottom: 0;
    }

    .data-table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 14px;
        border-bottom: 2px solid #dee2e6;
        padding: 12px;
        vertical-align: middle;
    }

    .data-table thead th.sortable {
        cursor: pointer;
        user-select: none;
    }

    .data-table thead th.sortable:hover {
        background-color: #e9ecef;
    }

    .data-table thead th.sorted-asc .sort-icon i::before {
        content: "\f145";
        color: #0056b3;
    }

    .data-table thead th.sorted-desc .sort-icon i::before {
        content: "\f149";
        color: #0056b3;
    }

    .data-table tbody tr {
        transition: background-color 0.2s;
    }

    .data-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .data-table tbody tr.selected {
        background-color: #e3f2fd;
    }

    .data-table tbody td {
        padding: 12px;
        vertical-align: middle;
        font-size: 14px;
    }

    /* Row Actions */
    .row-actions {
        display: flex;
        gap: 5px;
        justify-content: center;
    }

    .row-actions .btn {
        padding: 4px 8px;
        font-size: 12px;
    }

    /* Expandable Row */
    .expandable-row {
        cursor: pointer;
    }

    .expandable-row .expand-icon {
        transition: transform 0.3s;
    }

    .expandable-row.expanded .expand-icon {
        transform: rotate(90deg);
    }

    .expanded-content {
        background-color: #f8f9fa;
        padding: 15px;
    }

    /* Filter Row */
    .filter-row th {
        padding: 8px 12px;
        background-color: white;
    }

    .column-filter {
        font-size: 13px;
    }

    /* Mobile Cards */
    .mobile-view {
        display: none;
    }

    .mobile-card {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .mobile-card.selected {
        border-color: #0056b3;
        background-color: #e3f2fd;
    }

    .mobile-card-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .mobile-card-row:last-child {
        border-bottom: none;
    }

    .mobile-card-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 13px;
    }

    .mobile-card-value {
        font-size: 14px;
        color: #212529;
        text-align: right;
    }

    /* Loading Skeleton */
    .skeleton-row {
        animation: skeleton-loading 1s linear infinite alternate;
    }

    @keyframes skeleton-loading {
        0% {
            background-color: #f0f0f0;
        }
        100% {
            background-color: #e0e0e0;
        }
    }

    .skeleton-cell {
        height: 20px;
        background-color: #f0f0f0;
        border-radius: 4px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .desktop-view {
            display: none !important;
        }

        .mobile-view {
            display: block !important;
        }

        .data-table-header .row > div {
            margin-bottom: 10px;
        }
    }

    /* Export Buttons */
    .btn-group .btn:hover {
        transform: translateY(-1px);
    }

    /* Pagination */
    .pagination .page-link {
        color: #0056b3;
    }

    .pagination .page-item.active .page-link {
        background-color: #0056b3;
        border-color: #0056b3;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/data-table.js') }}"></script>
<script>
    // Initialize data table
    document.addEventListener('DOMContentLoaded', function() {
        const dataTable = new DataTable({
            id: '{{ $id }}',
            columns: @json($columns),
            data: @json($data),
            searchable: {{ $searchable ? 'true' : 'false' }},
            sortable: {{ $sortable ? 'true' : 'false' }},
            selectable: {{ $selectable ? 'true' : 'false' }},
            expandable: {{ $expandable ? 'true' : 'false' }},
            perPage: {{ $defaultPerPage }},
            mobileCards: {{ $mobileCards ? 'true' : 'false' }},
            actions: {{ $actions ? 'true' : 'false' }},
            @if($actions)
            actionButtons: function(row) {
                return `
                    <div class="row-actions">
                        <button class="btn btn-sm btn-outline-primary view-btn" data-id="${row.id}" title="View">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary edit-btn" data-id="${row.id}" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${row.id}" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                `;
            }
            @endif
        });
    });
</script>
@endpush
