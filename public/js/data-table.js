/**
 * Interactive Data Table Component
 * Handles sorting, filtering, pagination, selection, and export
 */

class DataTable {
    constructor(options) {
        this.options = {
            id: options.id || 'dataTable',
            columns: options.columns || [],
            data: options.data || [],
            searchable: options.searchable !== false,
            sortable: options.sortable !== false,
            selectable: options.selectable || false,
            expandable: options.expandable || false,
            perPage: options.perPage || 10,
            mobileCards: options.mobileCards !== false,
            actions: options.actions || false,
            actionButtons: options.actionButtons || null,
            ajaxUrl: options.ajaxUrl || null,
            onRowClick: options.onRowClick || null,
            onBulkAction: options.onBulkAction || null,
        };

        this.state = {
            currentPage: 1,
            perPage: this.options.perPage,
            sortColumn: null,
            sortDirection: 'asc',
            searchQuery: '',
            columnFilters: {},
            selectedRows: new Set(),
            filteredData: [],
            expandedRows: new Set(),
        };

        this.wrapper = document.getElementById(this.options.id + '-wrapper');
        this.table = document.getElementById(this.options.id);
        
        if (!this.wrapper || !this.table) {
            console.error('Data table elements not found');
            return;
        }

        this.init();
    }

    init() {
        this.state.filteredData = [...this.options.data];
        this.attachEventListeners();
        this.render();
        this.updateResponsiveView();
        
        // Handle window resize
        window.addEventListener('resize', () => this.updateResponsiveView());
    }

    attachEventListeners() {
        // Per page selector
        const perPageSelector = this.wrapper.querySelector('.per-page-selector');
        if (perPageSelector) {
            perPageSelector.addEventListener('change', (e) => {
                this.state.perPage = parseInt(e.target.value);
                this.state.currentPage = 1;
                this.render();
            });
        }

        // Global search
        const globalSearch = this.wrapper.querySelector('.global-search');
        if (globalSearch) {
            globalSearch.addEventListener('input', (e) => {
                this.state.searchQuery = e.target.value.toLowerCase();
                this.applyFilters();
            });
        }

        // Column filters
        const columnFilters = this.wrapper.querySelectorAll('.column-filter');
        columnFilters.forEach(filter => {
            filter.addEventListener('input', (e) => {
                const column = e.target.dataset.column;
                this.state.columnFilters[column] = e.target.value.toLowerCase();
                this.applyFilters();
            });
        });

        // Sort columns
        if (this.options.sortable) {
            const sortableHeaders = this.table.querySelectorAll('th.sortable');
            sortableHeaders.forEach(header => {
                header.addEventListener('click', () => {
                    const column = header.dataset.column;
                    this.sortBy(column, header.dataset.type);
                });
            });
        }

        // Select all checkbox
        if (this.options.selectable) {
            const selectAll = this.wrapper.querySelector('.select-all');
            if (selectAll) {
                selectAll.addEventListener('change', (e) => {
                    this.toggleSelectAll(e.target.checked);
                });
            }
        }

        // Export buttons
        const exportCsv = this.wrapper.querySelector('.export-csv');
        if (exportCsv) {
            exportCsv.addEventListener('click', () => this.exportToCSV());
        }

        const exportPrint = this.wrapper.querySelector('.export-print');
        if (exportPrint) {
            exportPrint.addEventListener('click', () => this.printTable());
        }

        const exportPdf = this.wrapper.querySelector('.export-pdf');
        if (exportPdf) {
            exportPdf.addEventListener('click', () => this.exportToPDF());
        }

        // Retry button
        const retryBtn = this.wrapper.querySelector('.retry-load');
        if (retryBtn) {
            retryBtn.addEventListener('click', () => this.loadData());
        }

        // Bulk actions
        const bulkActionItems = this.wrapper.querySelectorAll('.bulk-action-item');
        bulkActionItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const action = e.currentTarget.dataset.action;
                this.handleBulkAction(action);
            });
        });
    }

    applyFilters() {
        let filtered = [...this.options.data];

        // Apply global search
        if (this.state.searchQuery) {
            filtered = filtered.filter(row => {
                return this.options.columns.some(column => {
                    const value = this.getCellValue(row, column.field);
                    return String(value).toLowerCase().includes(this.state.searchQuery);
                });
            });
        }

        // Apply column filters
        Object.keys(this.state.columnFilters).forEach(columnField => {
            const filterValue = this.state.columnFilters[columnField];
            if (filterValue) {
                filtered = filtered.filter(row => {
                    const value = this.getCellValue(row, columnField);
                    return String(value).toLowerCase().includes(filterValue);
                });
            }
        });

        this.state.filteredData = filtered;
        this.state.currentPage = 1;
        this.render();
    }

    sortBy(column, type = 'string') {
        if (this.state.sortColumn === column) {
            this.state.sortDirection = this.state.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.state.sortColumn = column;
            this.state.sortDirection = 'asc';
        }

        this.state.filteredData.sort((a, b) => {
            let aVal = this.getCellValue(a, column);
            let bVal = this.getCellValue(b, column);

            // Handle different data types
            if (type === 'number') {
                aVal = parseFloat(aVal) || 0;
                bVal = parseFloat(bVal) || 0;
            } else if (type === 'date') {
                aVal = new Date(aVal);
                bVal = new Date(bVal);
            } else {
                aVal = String(aVal).toLowerCase();
                bVal = String(bVal).toLowerCase();
            }

            if (aVal < bVal) return this.state.sortDirection === 'asc' ? -1 : 1;
            if (aVal > bVal) return this.state.sortDirection === 'asc' ? 1 : -1;
            return 0;
        });

        this.updateSortIcons();
        this.render();
    }

    updateSortIcons() {
        // Remove all sort classes
        this.table.querySelectorAll('th.sortable').forEach(th => {
            th.classList.remove('sorted-asc', 'sorted-desc');
        });

        // Add class to current sorted column
        if (this.state.sortColumn) {
            const sortedHeader = this.table.querySelector(`th[data-column="${this.state.sortColumn}"]`);
            if (sortedHeader) {
                sortedHeader.classList.add(this.state.sortDirection === 'asc' ? 'sorted-asc' : 'sorted-desc');
            }
        }
    }

    toggleSelectAll(checked) {
        const currentPageData = this.getCurrentPageData();
        
        if (checked) {
            currentPageData.forEach(row => {
                this.state.selectedRows.add(row.id);
            });
        } else {
            currentPageData.forEach(row => {
                this.state.selectedRows.delete(row.id);
            });
        }

        this.updateSelectionUI();
    }

    toggleRowSelection(rowId) {
        if (this.state.selectedRows.has(rowId)) {
            this.state.selectedRows.delete(rowId);
        } else {
            this.state.selectedRows.add(rowId);
        }

        this.updateSelectionUI();
    }

    updateSelectionUI() {
        // Update checkbox states
        const checkboxes = this.wrapper.querySelectorAll('input[type="checkbox"][data-row-id]');
        checkboxes.forEach(cb => {
            cb.checked = this.state.selectedRows.has(parseInt(cb.dataset.rowId));
        });

        // Update select all checkbox
        const selectAll = this.wrapper.querySelector('.select-all');
        if (selectAll) {
            const currentPageData = this.getCurrentPageData();
            const allSelected = currentPageData.length > 0 && 
                currentPageData.every(row => this.state.selectedRows.has(row.id));
            selectAll.checked = allSelected;
        }

        // Update bulk actions visibility
        const bulkActionsContainer = this.wrapper.querySelector('.bulk-actions-container');
        const selectedCount = this.wrapper.querySelector('.selected-count');
        
        if (this.state.selectedRows.size > 0) {
            if (bulkActionsContainer) bulkActionsContainer.style.display = '';
            if (selectedCount) selectedCount.textContent = this.state.selectedRows.size;
        } else {
            if (bulkActionsContainer) bulkActionsContainer.style.display = 'none';
        }

        // Update row highlighting
        const rows = this.wrapper.querySelectorAll('tbody tr[data-row-id]');
        rows.forEach(row => {
            const rowId = parseInt(row.dataset.rowId);
            if (this.state.selectedRows.has(rowId)) {
                row.classList.add('selected');
            } else {
                row.classList.remove('selected');
            }
        });
    }

    handleBulkAction(action) {
        const selectedIds = Array.from(this.state.selectedRows);
        
        if (this.options.onBulkAction) {
            this.options.onBulkAction(action, selectedIds);
        } else {
            console.log('Bulk action:', action, 'IDs:', selectedIds);
            alert(`Bulk action "${action}" on ${selectedIds.length} items`);
        }
    }

    toggleRowExpansion(rowId) {
        if (this.state.expandedRows.has(rowId)) {
            this.state.expandedRows.delete(rowId);
        } else {
            this.state.expandedRows.add(rowId);
        }
        this.render();
    }

    getCurrentPageData() {
        const start = (this.state.currentPage - 1) * this.state.perPage;
        const end = start + this.state.perPage;
        return this.state.filteredData.slice(start, end);
    }

    render() {
        this.hideAllStates();

        const currentData = this.getCurrentPageData();

        if (currentData.length === 0) {
            if (this.state.searchQuery || Object.keys(this.state.columnFilters).length > 0) {
                this.showNoDataState();
            } else {
                this.showNoDataState();
            }
            return;
        }

        this.renderTableRows(currentData);
        this.renderMobileCards(currentData);
        this.renderPagination();
        this.updateTableInfo();
        this.updateSortIcons();

        if (this.options.selectable) {
            this.updateSelectionUI();
        }
    }

    renderTableRows(data) {
        const tbody = this.table.querySelector('tbody');
        tbody.innerHTML = '';

        data.forEach(row => {
            const tr = document.createElement('tr');
            tr.dataset.rowId = row.id;

            if (this.options.expandable) {
                tr.classList.add('expandable-row');
                tr.addEventListener('click', (e) => {
                    if (!e.target.closest('button') && !e.target.closest('input')) {
                        this.toggleRowExpansion(row.id);
                    }
                });

                if (this.state.expandedRows.has(row.id)) {
                    tr.classList.add('expanded');
                }
            }

            // Selection checkbox
            if (this.options.selectable) {
                const checkboxTd = document.createElement('td');
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'form-check-input';
                checkbox.dataset.rowId = row.id;
                checkbox.checked = this.state.selectedRows.has(row.id);
                checkbox.addEventListener('change', (e) => {
                    e.stopPropagation();
                    this.toggleRowSelection(row.id);
                });
                checkboxTd.appendChild(checkbox);
                tr.appendChild(checkboxTd);
            }

            // Data columns
            this.options.columns.forEach(column => {
                const td = document.createElement('td');
                const value = this.getCellValue(row, column.field);
                
                if (column.render) {
                    td.innerHTML = column.render(value, row);
                } else {
                    td.textContent = value;
                }

                if (column.className) {
                    td.className = column.className;
                }

                tr.appendChild(td);
            });

            // Actions column
            if (this.options.actions && this.options.actionButtons) {
                const actionsTd = document.createElement('td');
                actionsTd.className = 'text-center';
                actionsTd.innerHTML = this.options.actionButtons(row);
                tr.appendChild(actionsTd);
            }

            tbody.appendChild(tr);

            // Expanded content row
            if (this.options.expandable && this.state.expandedRows.has(row.id)) {
                const expandedTr = document.createElement('tr');
                expandedTr.className = 'expanded-content-row';
                const expandedTd = document.createElement('td');
                expandedTd.colSpan = this.getColSpan();
                expandedTd.innerHTML = `
                    <div class="expanded-content">
                        ${this.renderExpandedContent(row)}
                    </div>
                `;
                expandedTr.appendChild(expandedTd);
                tbody.appendChild(expandedTr);
            }
        });

        this.attachRowEventListeners();
    }

    renderMobileCards(data) {
        if (!this.options.mobileCards) return;

        const container = this.wrapper.querySelector('.mobile-cards-container');
        if (!container) return;

        container.innerHTML = '';

        data.forEach(row => {
            const card = document.createElement('div');
            card.className = 'mobile-card';
            card.dataset.rowId = row.id;

            if (this.state.selectedRows.has(row.id)) {
                card.classList.add('selected');
            }

            let cardHTML = '';

            // Selection checkbox
            if (this.options.selectable) {
                cardHTML += `
                    <div class="form-check mb-2">
                        <input class="form-check-input mobile-checkbox" type="checkbox" 
                               data-row-id="${row.id}" ${this.state.selectedRows.has(row.id) ? 'checked' : ''}>
                    </div>
                `;
            }

            // Data rows
            this.options.columns.forEach(column => {
                const value = this.getCellValue(row, column.field);
                const displayValue = column.render ? column.render(value, row) : value;
                
                cardHTML += `
                    <div class="mobile-card-row">
                        <div class="mobile-card-label">${column.label}</div>
                        <div class="mobile-card-value">${displayValue}</div>
                    </div>
                `;
            });

            // Actions
            if (this.options.actions && this.options.actionButtons) {
                cardHTML += `
                    <div class="mobile-card-row mt-2">
                        <div class="mobile-card-label">Actions</div>
                        <div class="mobile-card-value">${this.options.actionButtons(row)}</div>
                    </div>
                `;
            }

            card.innerHTML = cardHTML;
            container.appendChild(card);
        });

        this.attachMobileEventListeners();
    }

    renderPagination() {
        const totalPages = Math.ceil(this.state.filteredData.length / this.state.perPage);
        const pagination = this.wrapper.querySelector('.pagination');
        
        if (!pagination) return;

        let paginationHTML = '';

        // Previous button
        paginationHTML += `
            <li class="page-item ${this.state.currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${this.state.currentPage - 1}">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
        `;

        // Page numbers
        const maxVisible = 5;
        let startPage = Math.max(1, this.state.currentPage - Math.floor(maxVisible / 2));
        let endPage = Math.min(totalPages, startPage + maxVisible - 1);

        if (endPage - startPage < maxVisible - 1) {
            startPage = Math.max(1, endPage - maxVisible + 1);
        }

        if (startPage > 1) {
            paginationHTML += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="1">1</a>
                </li>
            `;
            if (startPage > 2) {
                paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
                <li class="page-item ${i === this.state.currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            paginationHTML += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
                </li>
            `;
        }

        // Next button
        paginationHTML += `
            <li class="page-item ${this.state.currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${this.state.currentPage + 1}">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        `;

        pagination.innerHTML = paginationHTML;

        // Attach pagination click events
        pagination.querySelectorAll('a.page-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = parseInt(e.currentTarget.dataset.page);
                if (page && page !== this.state.currentPage && page >= 1 && page <= totalPages) {
                    this.state.currentPage = page;
                    this.render();
                    this.scrollToTop();
                }
            });
        });
    }

    updateTableInfo() {
        const start = (this.state.currentPage - 1) * this.state.perPage + 1;
        const end = Math.min(start + this.state.perPage - 1, this.state.filteredData.length);
        const total = this.state.filteredData.length;
        const totalUnfiltered = this.options.data.length;

        const startEntry = this.wrapper.querySelector('.start-entry');
        const endEntry = this.wrapper.querySelector('.end-entry');
        const totalEntries = this.wrapper.querySelector('.total-entries');
        const totalEntriesUnfiltered = this.wrapper.querySelector('.total-entries-unfiltered');
        const filteredInfo = this.wrapper.querySelector('.filtered-info');

        if (startEntry) startEntry.textContent = total > 0 ? start : 0;
        if (endEntry) endEntry.textContent = end;
        if (totalEntries) totalEntries.textContent = total;

        if (total !== totalUnfiltered && filteredInfo) {
            filteredInfo.style.display = '';
            if (totalEntriesUnfiltered) totalEntriesUnfiltered.textContent = totalUnfiltered;
        } else if (filteredInfo) {
            filteredInfo.style.display = 'none';
        }
    }

    attachRowEventListeners() {
        // View buttons
        this.wrapper.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const id = btn.dataset.id;
                console.log('View row:', id);
                if (this.options.onRowClick) {
                    this.options.onRowClick('view', id);
                }
            });
        });

        // Edit buttons
        this.wrapper.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const id = btn.dataset.id;
                console.log('Edit row:', id);
                if (this.options.onRowClick) {
                    this.options.onRowClick('edit', id);
                }
            });
        });

        // Delete buttons
        this.wrapper.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const id = btn.dataset.id;
                if (confirm('Are you sure you want to delete this item?')) {
                    console.log('Delete row:', id);
                    if (this.options.onRowClick) {
                        this.options.onRowClick('delete', id);
                    }
                }
            });
        });
    }

    attachMobileEventListeners() {
        const mobileCheckboxes = this.wrapper.querySelectorAll('.mobile-checkbox');
        mobileCheckboxes.forEach(cb => {
            cb.addEventListener('change', (e) => {
                const rowId = parseInt(cb.dataset.rowId);
                this.toggleRowSelection(rowId);
            });
        });
    }

    getCellValue(row, field) {
        return field.split('.').reduce((obj, key) => obj?.[key], row) ?? '';
    }

    renderExpandedContent(row) {
        // Override this method to customize expanded content
        return `<pre>${JSON.stringify(row, null, 2)}</pre>`;
    }

    getColSpan() {
        let count = this.options.columns.length;
        if (this.options.selectable) count++;
        if (this.options.actions) count++;
        return count;
    }

    scrollToTop() {
        this.wrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    updateResponsiveView() {
        const isMobile = window.innerWidth < 768;
        const desktopView = this.wrapper.querySelector('.desktop-view');
        const mobileView = this.wrapper.querySelector('.mobile-view');

        if (isMobile && this.options.mobileCards) {
            if (desktopView) desktopView.style.display = 'none';
            if (mobileView) mobileView.style.display = 'block';
        } else {
            if (desktopView) desktopView.style.display = 'block';
            if (mobileView) mobileView.style.display = 'none';
        }
    }

    // State management methods
    hideAllStates() {
        this.wrapper.querySelector('.loading-state')?.style.setProperty('display', 'none');
        this.wrapper.querySelector('.no-data-state')?.style.setProperty('display', 'none');
        this.wrapper.querySelector('.error-state')?.style.setProperty('display', 'none');
    }

    showLoadingState() {
        this.hideAllStates();
        this.wrapper.querySelector('.loading-state')?.style.removeProperty('display');
    }

    showNoDataState() {
        this.hideAllStates();
        this.wrapper.querySelector('.no-data-state')?.style.removeProperty('display');
    }

    showErrorState(message) {
        this.hideAllStates();
        const errorState = this.wrapper.querySelector('.error-state');
        if (errorState) {
            errorState.style.removeProperty('display');
            const errorMessage = errorState.querySelector('.error-message');
            if (errorMessage) errorMessage.textContent = message;
        }
    }

    // Export methods
    exportToCSV() {
        const headers = this.options.columns.map(col => col.label);
        const rows = this.state.filteredData.map(row => {
            return this.options.columns.map(col => {
                const value = this.getCellValue(row, col.field);
                return `"${String(value).replace(/"/g, '""')}"`;
            });
        });

        const csv = [headers.join(','), ...rows.map(r => r.join(','))].join('\n');
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `export_${new Date().getTime()}.csv`;
        a.click();
        window.URL.revokeObjectURL(url);
    }

    printTable() {
        const printWindow = window.open('', '', 'height=600,width=800');
        const headers = this.options.columns.map(col => `<th>${col.label}</th>`).join('');
        const rows = this.state.filteredData.map(row => {
            const cells = this.options.columns.map(col => {
                const value = this.getCellValue(row, col.field);
                return `<td>${value}</td>`;
            }).join('');
            return `<tr>${cells}</tr>`;
        }).join('');

        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Print Table</title>
                <style>
                    body { font-family: Arial, sans-serif; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f8f9fa; font-weight: bold; }
                    tr:nth-child(even) { background-color: #f9f9f9; }
                    @media print {
                        @page { margin: 1cm; }
                    }
                </style>
            </head>
            <body>
                <h2>Data Export</h2>
                <table>
                    <thead><tr>${headers}</tr></thead>
                    <tbody>${rows}</tbody>
                </table>
                <script>window.print(); window.close();</script>
            </body>
            </html>
        `);
        printWindow.document.close();
    }

    exportToPDF() {
        // This would require a PDF library like jsPDF or html2pdf
        alert('PDF export requires jsPDF library. Please include it in your project.');
        
        // Example implementation with jsPDF:
        /*
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        doc.autoTable({
            head: [this.options.columns.map(col => col.label)],
            body: this.state.filteredData.map(row => 
                this.options.columns.map(col => this.getCellValue(row, col.field))
            ),
        });
        
        doc.save(`export_${new Date().getTime()}.pdf`);
        */
    }

    // Public API methods
    refresh() {
        this.render();
    }

    loadData(data) {
        if (data) {
            this.options.data = data;
            this.state.filteredData = [...data];
            this.state.currentPage = 1;
        }
        this.render();
    }

    getSelectedRows() {
        return Array.from(this.state.selectedRows);
    }

    clearSelection() {
        this.state.selectedRows.clear();
        this.updateSelectionUI();
    }
}

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DataTable;
}
