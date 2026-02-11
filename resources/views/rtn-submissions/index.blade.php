@extends('layouts.public')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'RTN Submissions | Academic Research Portal')

@section('content')
<!-- RTN Submissions Header -->
<header class="page-header">
    <div class="container">
        <h1>RTN Submissions</h1>
        <p>Browse approved Research and Teaching Network submissions</p>
    </div>
</header>

<!-- RTN Submissions Filter -->
<section class="publications-filter">
    <div class="container">
        <form action="{{ route('rtn-submissions.index') }}" method="GET" class="filter-container">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search RTN submissions..." style="flex: 1; min-width: 300px; padding: 1rem 1.5rem; border: 1px solid var(--border-color); border-radius: var(--border-radius); font-size: 1rem;">
            <div class="filter-options">
                <select name="rtn_type" style="padding: 1rem 1.5rem; border: 1px solid var(--border-color); border-radius: var(--border-radius); background: white; font-size: 0.95rem;">
                    <option value="">All RTN Types</option>
                    @foreach($rtnSubmissions->pluck('rtn_type')->unique()->filter() as $type)
                        <option value="{{ $type }}" {{ request('rtn_type') == $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
                <select name="year" style="padding: 1rem 1.5rem; border: 1px solid var(--border-color); border-radius: var(--border-radius); background: white; font-size: 0.95rem;">
                    <option value="">All Years</option>
                    @foreach($rtnSubmissions->pluck('year')->unique()->filter()->sortDesc() as $year)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
                <select name="sort" style="padding: 1rem 1.5rem; border: 1px solid var(--border-color); border-radius: var(--border-radius); background: white; font-size: 0.95rem;">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title A-Z</option>
                </select>
                <button type="submit" class="btn btn-primary">Apply Filters</button>
            </div>
        </form>
    </div>
</section>

<!-- RTN Submissions Grid -->
<section class="publications-grid-section">
    <div class="container">
        @if($rtnSubmissions->count() > 0)
        <div class="publications-grid-full" id="rtn-container">
            @include('rtn-submissions.partials.rtn-card', ['rtnSubmissions' => $rtnSubmissions])
        </div>
        @if($hasMore ?? false)
        <div style="text-align: center; margin-top: 3rem;">
            <button id="load-more-btn" class="btn btn-primary" style="padding: 1rem 3rem; font-size: 1.1rem;">
                <i class="fas fa-arrow-down"></i> Load More RTN Submissions
            </button>
            <div id="loading-indicator" style="display: none; margin-top: 1rem;">
                <i class="fas fa-spinner fa-spin" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                <p style="margin-top: 0.5rem; color: var(--text-light);">Loading more RTN submissions...</p>
            </div>
        </div>
        @endif
        @else
        <div style="text-align: center; padding: 4rem; background: white; border-radius: 15px;">
            <i class="fas fa-certificate" style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
            <p style="color: var(--text-secondary); font-size: 1.125rem;">No RTN submissions available yet.</p>
        </div>
        @endif
    </div>
</section>

<!-- RTN Detail Modal -->
<div class="modal" id="rtn-modal">
    <div class="modal-content">
        <button class="modal-close" id="modal-close">&times;</button>
        <div class="modal-body" id="modal-body">
            <div style="text-align: center; padding: 2rem;">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--accent-color);"></i>
                <p>Loading RTN submission details...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('rtn-modal');
    const modalClose = document.getElementById('modal-close');
    const modalBody = document.getElementById('modal-body');
    const viewButtons = document.querySelectorAll('.view-rtn-btn');

    // Open modal and load RTN details
    viewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const rtnId = this.getAttribute('data-id');
            loadRtnDetails(rtnId);
        });
    });

    // Close modal handlers
    if (modalClose) {
        modalClose.addEventListener('click', closeModal);
    }

    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
    });

    function loadRtnDetails(id) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        modalBody.innerHTML = `
            <div style="text-align: center; padding: 2rem;">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--accent-color);"></i>
                <p>Loading RTN submission details...</p>
            </div>
        `;

        const baseUrl = window.BASE_URL || '';
        fetch(`${baseUrl}/rtn-submissions/${id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(response => {
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                }
                return response.text().then(html => ({ html }));
            })
            .then(data => {
                if (data.html) {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(data.html, 'text/html');
                    const content = doc.querySelector('.publication-detail-content') || doc.body;
                    modalBody.innerHTML = `<div class="publication-detail-modal">${content.innerHTML}</div>`;
                } else if (data.rtn) {
                    const rtn = data.rtn;
                    
                    modalBody.innerHTML = `
                        <div class="publication-detail-modal">
                            <div class="publication-detail-header">
                                <span class="publication-detail-category">${rtn.rtn_type || 'RTN'}</span>
                                <h2>${rtn.title}</h2>
                                <div class="publication-detail-authors">
                                    <i class="fas fa-user-edit"></i> ${rtn.user?.name || 'Anonymous'}
                                </div>
                            </div>
                            <div class="publication-detail-meta">
                                <div class="meta-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Year: ${rtn.year || 'N/A'}</span>
                                </div>
                                ${rtn.amount_omr ? `<div class="meta-item"><i class="fas fa-money-bill-wave"></i> <span>Amount: ${rtn.amount_omr} OMR</span></div>` : ''}
                                ${rtn.units ? `<div class="meta-item"><i class="fas fa-calculator"></i> <span>Units: ${rtn.units}</span></div>` : ''}
                            </div>
                            ${rtn.description ? `<div class="publication-detail-abstract"><h3><i class="fas fa-file-alt"></i> Description</h3><p>${rtn.description}</p></div>` : ''}
                            <div class="publication-detail-actions">
                                <a href="${window.BASE_URL || ''}/rtn-submissions/${rtn.id}" class="btn btn-outline"><i class="fas fa-external-link-alt"></i> Full Page View</a>
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading RTN:', error);
                modalBody.innerHTML = `
                    <div style="text-align: center; padding: 2rem;">
                        <i class="fas fa-exclamation-circle" style="font-size: 2rem; color: #ef4444; margin-bottom: 1rem;"></i>
                        <p>Error loading RTN submission details. Please try again.</p>
                        <button class="btn btn-primary" onclick="closeModal()">Close</button>
                    </div>
                `;
            });
    }

    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    window.closeModal = closeModal;

    // Load More functionality
    const loadMoreBtn = document.getElementById('load-more-btn');
    const loadingIndicator = document.getElementById('loading-indicator');
    const rtnContainer = document.getElementById('rtn-container');
    let currentOffset = {{ $rtnSubmissions->count() }};
    let isLoading = false;

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            if (isLoading) return;
            
            isLoading = true;
            loadMoreBtn.style.display = 'none';
            loadingIndicator.style.display = 'block';

            const urlParams = new URLSearchParams(window.location.search);
            const params = {
                offset: currentOffset,
                search: urlParams.get('search') || '',
                rtn_type: urlParams.get('rtn_type') || '',
                year: urlParams.get('year') || '',
                sort: urlParams.get('sort') || 'newest',
            };

            fetch('{{ route("rtn-submissions.load-more") }}?' + new URLSearchParams(params), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.html) {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data.html;
                    
                    const cards = tempDiv.querySelectorAll('.publication-card');
                    cards.forEach(card => {
                        rtnContainer.appendChild(card);
                    });

                    const newViewButtons = tempDiv.querySelectorAll('.view-rtn-btn');
                    newViewButtons.forEach(button => {
                        button.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const rtnId = this.getAttribute('data-id');
                            loadRtnDetails(rtnId);
                        });
                    });

                    currentOffset += cards.length;

                    if (data.hasMore) {
                        loadMoreBtn.style.display = 'block';
                    } else {
                        const allLoadedMsg = document.createElement('div');
                        allLoadedMsg.style.textAlign = 'center';
                        allLoadedMsg.style.marginTop = '2rem';
                        allLoadedMsg.style.padding = '1rem';
                        allLoadedMsg.style.color = 'var(--text-light)';
                        allLoadedMsg.innerHTML = '<i class="fas fa-check-circle"></i> All RTN submissions loaded';
                        loadingIndicator.parentElement.appendChild(allLoadedMsg);
                    }
                }
            })
            .catch(error => {
                console.error('Error loading more RTN submissions:', error);
                loadMoreBtn.style.display = 'block';
                loadMoreBtn.innerHTML = '<i class="fas fa-exclamation-circle"></i> Error loading. Click to retry';
            })
            .finally(() => {
                isLoading = false;
                loadingIndicator.style.display = 'none';
            });
        });
    }
});
</script>
@endpush
@endsection
