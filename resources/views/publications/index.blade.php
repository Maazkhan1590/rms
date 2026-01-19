@extends('layouts.public')

@section('title', 'Publications | Academic Research Portal')

@section('content')
<!-- Publications Header -->
<header class="page-header">
    <div class="container">
        <h1>Publications</h1>
        <p>Browse our collection of peer-reviewed research papers across all disciplines</p>
    </div>
</header>

<!-- Publications Filter -->
<section class="publications-filter">
    <div class="container">
        <form action="{{ route('publications.index') }}" method="GET" class="filter-container">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search publications..." style="flex: 1; min-width: 300px; padding: 1rem 1.5rem; border: 1px solid var(--border-color); border-radius: var(--border-radius); font-size: 1rem;">
            <div class="filter-options">
                <select name="type" style="padding: 1rem 1.5rem; border: 1px solid var(--border-color); border-radius: var(--border-radius); background: white; font-size: 0.95rem;">
                    <option value="">All Categories</option>
                    @foreach($publications->pluck('publication_type')->unique()->filter() as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
                <select name="year" style="padding: 1rem 1.5rem; border: 1px solid var(--border-color); border-radius: var(--border-radius); background: white; font-size: 0.95rem;">
                    <option value="">All Years</option>
                    @foreach($publications->pluck('publication_year')->unique()->filter()->sortDesc() as $year)
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

<!-- Publications Grid -->
<section class="publications-grid-section">
    <div class="container">
        @if($publications->count() > 0)
        <div class="publications-grid-full" id="publications-container">
            @include('publications.partials.publication-card', ['publications' => $publications])
        </div>
        @if($hasMore ?? false)
        <div style="text-align: center; margin-top: 3rem;">
            <button id="load-more-btn" class="btn btn-primary" style="padding: 1rem 3rem; font-size: 1.1rem;">
                <i class="fas fa-arrow-down"></i> Load More Publications
            </button>
            <div id="loading-indicator" style="display: none; margin-top: 1rem;">
                <i class="fas fa-spinner fa-spin" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                <p style="margin-top: 0.5rem; color: var(--text-light);">Loading more publications...</p>
            </div>
        </div>
        @endif
        @else
        <div style="text-align: center; padding: 4rem; background: white; border-radius: 15px;">
            <i class="fas fa-book-open" style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
            <p style="color: var(--text-secondary); font-size: 1.125rem;">No publications available yet.</p>
        </div>
        @endif
    </div>
</section>

<!-- Publication Detail Modal -->
<div class="modal" id="publication-modal">
    <div class="modal-content">
        <button class="modal-close" id="modal-close">&times;</button>
        <div class="modal-body" id="modal-body">
            <div style="text-align: center; padding: 2rem;">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--accent-color);"></i>
                <p>Loading publication details...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('publication-modal');
    const modalClose = document.getElementById('modal-close');
    const modalBody = document.getElementById('modal-body');
    const viewButtons = document.querySelectorAll('.view-publication-btn');

    // Open modal and load publication details
    viewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const publicationId = this.getAttribute('data-id');
            loadPublicationDetails(publicationId);
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

    function loadPublicationDetails(id) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        modalBody.innerHTML = `
            <div style="text-align: center; padding: 2rem;">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--accent-color);"></i>
                <p>Loading publication details...</p>
            </div>
        `;

        // Use BASE_URL if available (for subdirectory deployment), otherwise use relative path
        const baseUrl = window.BASE_URL || '';
        fetch(`${baseUrl}/publications/${id}`, {
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
                } else if (data.publication) {
                    const pub = data.publication;
                    const authors = pub.authors && Array.isArray(pub.authors) 
                        ? pub.authors.map(a => a.name || a).join(', ')
                        : (pub.submitter?.name || 'Anonymous');
                    
                    modalBody.innerHTML = `
                        <div class="publication-detail-modal">
                            <div class="publication-detail-header">
                                <span class="publication-detail-category">${(pub.publication_type || 'Publication').toUpperCase().replace('_', ' ')}</span>
                                <h2>${pub.title}</h2>
                                <div class="publication-detail-authors">
                                    <i class="fas fa-user-edit"></i> ${authors}
                                </div>
                            </div>
                            <div class="publication-detail-meta">
                                <div class="meta-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Published: ${pub.publication_year || 'N/A'}</span>
                                </div>
                                ${pub.journal_name ? `<div class="meta-item"><i class="fas fa-book"></i> <span>Journal: ${pub.journal_name}</span></div>` : ''}
                                ${pub.conference_name ? `<div class="meta-item"><i class="fas fa-users"></i> <span>Conference: ${pub.conference_name}</span></div>` : ''}
                                ${pub.doi ? `<div class="meta-item"><i class="fas fa-hashtag"></i> <span>DOI: <a href="https://doi.org/${pub.doi}" target="_blank">${pub.doi}</a></span></div>` : ''}
                                ${pub.publisher ? `<div class="meta-item"><i class="fas fa-building"></i> <span>Publisher: ${pub.publisher}</span></div>` : ''}
                                ${pub.isbn ? `<div class="meta-item"><i class="fas fa-barcode"></i> <span>ISBN: ${pub.isbn}</span></div>` : ''}
                            </div>
                            ${pub.abstract ? `<div class="publication-detail-abstract"><h3><i class="fas fa-file-alt"></i> Abstract</h3><p>${pub.abstract}</p></div>` : ''}
                            ${pub.authors && Array.isArray(pub.authors) && pub.authors.length > 0 ? `
                                <div class="publication-detail-keywords">
                                    <h3><i class="fas fa-users"></i> Authors</h3>
                                    <div class="keywords-list">
                                        ${pub.authors.map(a => `<span class="keyword">${a.name || a}</span>`).join('')}
                                    </div>
                                </div>
                            ` : ''}
                            <div class="publication-detail-actions">
                                ${pub.published_link ? `<a href="${pub.published_link}" target="_blank" class="btn btn-primary"><i class="fas fa-external-link-alt"></i> View Publication</a>` : ''}
                                ${pub.proceedings_link ? `<a href="${pub.proceedings_link}" target="_blank" class="btn btn-outline"><i class="fas fa-file-pdf"></i> View Proceedings</a>` : ''}
                                <a href="${window.BASE_URL || ''}/publications/${pub.id}" class="btn btn-outline"><i class="fas fa-external-link-alt"></i> Full Page View</a>
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading publication:', error);
                modalBody.innerHTML = `
                    <div style="text-align: center; padding: 2rem;">
                        <i class="fas fa-exclamation-circle" style="font-size: 2rem; color: #ef4444; margin-bottom: 1rem;"></i>
                        <p>Error loading publication details. Please try again.</p>
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
    const publicationsContainer = document.getElementById('publications-container');
    let currentOffset = {{ $publications->count() }};
    let isLoading = false;

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            if (isLoading) return;
            
            isLoading = true;
            loadMoreBtn.style.display = 'none';
            loadingIndicator.style.display = 'block';

            // Get current filter parameters
            const urlParams = new URLSearchParams(window.location.search);
            const params = {
                offset: currentOffset,
                search: urlParams.get('search') || '',
                type: urlParams.get('type') || '',
                year: urlParams.get('year') || '',
                sort: urlParams.get('sort') || 'newest',
            };

            fetch('{{ route("publications.load-more") }}?' + new URLSearchParams(params), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.html) {
                    // Create a temporary container to parse the HTML
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data.html;
                    
                    // Append each publication card
                    const cards = tempDiv.querySelectorAll('.publication-card');
                    cards.forEach(card => {
                        publicationsContainer.appendChild(card);
                    });

                    // Re-attach event listeners to new cards
                    const newViewButtons = tempDiv.querySelectorAll('.view-publication-btn');
                    newViewButtons.forEach(button => {
                        button.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const publicationId = this.getAttribute('data-id');
                            loadPublicationDetails(publicationId);
                        });
                    });

                    currentOffset += cards.length;

                    if (data.hasMore) {
                        loadMoreBtn.style.display = 'block';
                    } else {
                        // Show message when all publications are loaded
                        const allLoadedMsg = document.createElement('div');
                        allLoadedMsg.style.textAlign = 'center';
                        allLoadedMsg.style.marginTop = '2rem';
                        allLoadedMsg.style.padding = '1rem';
                        allLoadedMsg.style.color = 'var(--text-light)';
                        allLoadedMsg.innerHTML = '<i class="fas fa-check-circle"></i> All publications loaded';
                        loadingIndicator.parentElement.appendChild(allLoadedMsg);
                    }
                }
            })
            .catch(error => {
                console.error('Error loading more publications:', error);
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
