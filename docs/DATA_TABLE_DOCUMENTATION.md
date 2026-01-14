# Data Table Component Documentation

## Overview
The Data Table component is a comprehensive, reusable Blade component for displaying and managing tabular data with advanced features like sorting, filtering, pagination, selection, bulk actions, and export capabilities.

## Features
- ✅ Column sorting (ascending/descending)
- ✅ Global search across all columns
- ✅ Per-column filtering
- ✅ Pagination with customizable page sizes
- ✅ Row selection (individual and bulk)
- ✅ Bulk actions dropdown
- ✅ Row action buttons (view, edit, delete)
- ✅ Expandable rows for additional details
- ✅ Mobile-responsive card layout
- ✅ Export to CSV and Print
- ✅ PDF export (requires jsPDF library)
- ✅ Loading, empty, and error states
- ✅ Customizable styling

## Installation

### 1. Files Required
```
resources/views/components/data-table.blade.php
public/js/data-table.js
```

### 2. Dependencies
- Bootstrap 5.3.0+
- Bootstrap Icons
- Modern browser with ES6+ support

### 3. Include in Layout
```blade
<!-- In your layout file -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<script src="{{ asset('js/data-table.js') }}" defer></script>
```

## Basic Usage

### Controller
```php
namespace App\Http\Controllers;

use App\Models\Publication;

class PublicationController extends Controller
{
    public function index()
    {
        // Define columns
        $columns = [
            [
                'field' => 'id',
                'label' => 'ID',
                'sortable' => true,
                'type' => 'number',
            ],
            [
                'field' => 'title',
                'label' => 'Title',
                'sortable' => true,
                'filterable' => true,
            ],
            [
                'field' => 'status',
                'label' => 'Status',
                'sortable' => true,
                'render' => function($value) {
                    return '<span class="badge bg-success">' . $value . '</span>';
                },
            ],
        ];

        // Fetch data
        $publications = Publication::all()->map(fn($p) => [
            'id' => $p->id,
            'title' => $p->title,
            'status' => $p->status,
        ])->toArray();

        return view('publications.index', [
            'columns' => $columns,
            'publications' => $publications,
        ]);
    }
}
```

### Blade View
```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <x-data-table
        id="publicationsTable"
        :columns="$columns"
        :data="$publications"
        :searchable="true"
        :sortable="true"
        :exportable="true"
    />
</div>
@endsection
```

## Component Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | string | Required | Unique identifier for the table |
| `columns` | array | Required | Column definitions (see Column Configuration) |
| `data` | array | Required | Data array to display |
| `searchable` | boolean | `false` | Enable global search |
| `sortable` | boolean | `false` | Enable column sorting |
| `selectable` | boolean | `false` | Enable row selection |
| `exportable` | boolean | `false` | Show export buttons |
| `actions` | boolean | `false` | Show action buttons column |
| `expandable` | boolean | `false` | Enable row expansion |
| `mobileCards` | boolean | `false` | Use card layout on mobile |
| `bulkActions` | array | `[]` | Bulk action definitions |
| `perPageOptions` | array | `[10, 25, 50, 100]` | Page size options |
| `defaultPerPage` | int | `10` | Initial page size |
| `tableClass` | string | `''` | Additional CSS classes for table |

## Column Configuration

Each column in the `columns` array can have the following properties:

```php
[
    'field' => 'column_name',        // Required: Data field key
    'label' => 'Display Name',       // Required: Column header text
    'sortable' => true,              // Optional: Enable sorting
    'filterable' => true,            // Optional: Show filter input
    'type' => 'string',              // Optional: string|number|date
    'width' => '100px',              // Optional: Fixed width
    'className' => 'text-center',    // Optional: CSS classes
    'render' => function($value, $row) {  // Optional: Custom rendering
        return '<strong>' . $value . '</strong>';
    },
]
```

### Column Types
- `string` (default): Alphabetical sorting, text filtering
- `number`: Numeric sorting, number filtering
- `date`: Date sorting, date filtering

### Custom Rendering
Use the `render` callback to customize cell content:

```php
'render' => function($value, $row) {
    // $value: cell value
    // $row: entire row data
    return view('partials.custom-cell', [
        'value' => $value,
        'row' => $row,
    ])->render();
}
```

## Bulk Actions

Define bulk actions that appear in the dropdown when rows are selected:

```blade
:bulkActions="[
    [
        'value' => 'approve',
        'label' => 'Approve Selected',
        'icon' => 'bi-check-circle'
    ],
    [
        'value' => 'delete',
        'label' => 'Delete Selected',
        'icon' => 'bi-trash'
    ],
]"
```

Handle bulk actions with JavaScript:

```javascript
document.addEventListener('DOMContentLoaded', function() {
    const dataTable = window.publicationsTableInstance;
    
    // Override bulk action handler
    dataTable.handleBulkAction = function(action, selectedIds) {
        if (action === 'approve') {
            fetch('/admin/publications/bulk-approve', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids: selectedIds })
            })
            .then(response => response.json())
            .then(data => {
                alert('Success!');
                location.reload();
            });
        }
    };
});
```

## Action Buttons

When `actions="true"`, each row displays action buttons. Customize them:

```javascript
// View button click
table.addEventListener('click', function(e) {
    if (e.target.closest('.view-btn')) {
        const id = e.target.closest('.view-btn').dataset.id;
        window.location.href = `/publications/${id}`;
    }
});

// Edit button click
table.addEventListener('click', function(e) {
    if (e.target.closest('.edit-btn')) {
        const id = e.target.closest('.edit-btn').dataset.id;
        window.location.href = `/publications/${id}/edit`;
    }
});
```

## Expandable Rows

Enable expandable rows to show additional details:

```blade
:expandable="true"
```

Customize expanded content by modifying the Blade component or handling via JavaScript:

```javascript
// In data-table.js, modify renderExpandedRow method
renderExpandedRow(row) {
    return `
        <div class="p-3">
            <h6>Additional Details</h6>
            <p>${row.description}</p>
        </div>
    `;
}
```

## Export Features

### CSV Export
Built-in CSV export functionality. Click "Export CSV" button.

### Print
Opens print-friendly popup. Click "Print" button.

### PDF Export
Requires jsPDF library:

```blade
<!-- Add to your layout -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
```

Then uncomment the PDF export code in `data-table.js`:

```javascript
exportToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    
    // Add configuration...
    doc.save(`${this.options.id}_${Date.now()}.pdf`);
}
```

## Mobile Responsiveness

Enable mobile card layout:

```blade
:mobileCards="true"
```

On screens < 768px, the table switches to a card-based layout automatically.

## JavaScript API

Access the DataTable instance:

```javascript
const table = window.publicationsTableInstance;

// Methods
table.refresh();                    // Refresh table
table.loadData(newData);           // Load new data
table.getSelectedRows();           // Get selected row IDs
table.clearSelection();            // Deselect all
table.exportToCSV();               // Trigger CSV export
table.printTable();                // Trigger print
table.exportToPDF();               // Trigger PDF export
```

## Styling Customization

### Custom Table Classes
```blade
:tableClass="'table-striped table-hover custom-table'"
```

### CSS Variables
Override these in your CSS:

```css
.data-table-wrapper {
    --primary-color: #0056b3;
    --border-color: #dee2e6;
    --hover-bg: #f8f9fa;
}
```

### Custom Styles
```css
/* Compact table */
#myTable .table td,
#myTable .table th {
    padding: 0.5rem;
    font-size: 0.875rem;
}

/* Colored status badges */
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
}
```

## Advanced Examples

### With AJAX Loading
```javascript
// In your view
const dataTable = new DataTable({
    id: 'publicationsTable',
    columns: columns,
    data: [],
    ajaxUrl: '/api/publications',
    searchable: true,
    sortable: true,
});

// Data will be loaded via AJAX automatically
```

### With Row Click Handler
```javascript
const dataTable = new DataTable({
    id: 'usersTable',
    columns: columns,
    data: usersData,
    onRowClick: function(row) {
        window.location.href = `/users/${row.id}`;
    },
});
```

### With Custom Action Buttons
```javascript
const dataTable = new DataTable({
    id: 'grantsTable',
    columns: columns,
    data: grantsData,
    actions: true,
    actionButtons: [
        {
            label: 'View',
            icon: 'bi-eye',
            className: 'btn-primary',
            onClick: (row) => window.location.href = `/grants/${row.id}`
        },
        {
            label: 'Approve',
            icon: 'bi-check',
            className: 'btn-success',
            onClick: (row) => approveGrant(row.id)
        },
    ],
});
```

## Troubleshooting

### Table Not Rendering
1. Check if `data-table.js` is loaded
2. Verify Bootstrap CSS is included
3. Check browser console for errors
4. Ensure `id` prop is unique

### Sorting Not Working
1. Set `sortable="true"` on component
2. Set `sortable: true` on column definitions
3. Check data types match column types

### Export Buttons Not Visible
1. Set `exportable="true"` on component
2. Check if buttons are hidden by CSS
3. For PDF, ensure jsPDF library is loaded

### Mobile Cards Not Showing
1. Set `mobileCards="true"` on component
2. Resize browser to < 768px
3. Check if responsive meta tag is present

## Performance Tips

1. **Paginate server-side** for large datasets (>1000 rows)
2. **Limit initial data** - Load first page only
3. **Use AJAX loading** instead of passing all data
4. **Debounce search** input for better UX
5. **Virtual scrolling** for very large tables

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## License

This component is part of the RMS (Research Management System) project.

## Support

For issues or questions, contact the development team.
