@extends('layouts.public')

@section('title', 'Bonus Recognitions | Academic Research Portal')

@section('content')
<!-- Bonus Recognitions Header -->
<header class="page-header">
    <div class="container">
        <h1>Bonus Recognitions</h1>
        <p>Browse approved bonus recognitions and achievements</p>
    </div>
</header>

<!-- Bonus Recognitions Filter -->
<section class="publications-filter">
    <div class="container">
        <form action="{{ route('bonus-recognitions.index') }}" method="GET" class="filter-container">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search recognitions..." style="flex: 1; min-width: 300px; padding: 1rem 1.5rem; border: 1px solid var(--border-color); border-radius: var(--border-radius); font-size: 1rem;">
            <div class="filter-options">
                <select name="recognition_type" style="padding: 1rem 1.5rem; border: 1px solid var(--border-color); border-radius: var(--border-radius); background: white; font-size: 0.95rem;">
                    <option value="">All Recognition Types</option>
                    @foreach($bonusRecognitions->pluck('recognition_type')->unique()->filter() as $type)
                        <option value="{{ $type }}" {{ request('recognition_type') == $type ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $type)) }}
                        </option>
                    @endforeach
                </select>
                <select name="year" style="padding: 1rem 1.5rem; border: 1px solid var(--border-color); border-radius: var(--border-radius); background: white; font-size: 0.95rem;">
                    <option value="">All Years</option>
                    @foreach($bonusRecognitions->pluck('year')->unique()->filter()->sortDesc() as $year)
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

<!-- Bonus Recognitions Grid -->
<section class="publications-grid-section">
    <div class="container">
        @if($bonusRecognitions->count() > 0)
        <div class="publications-grid-full" id="bonus-container">
            @include('bonus-recognitions.partials.bonus-card', ['bonusRecognitions' => $bonusRecognitions])
        </div>
        @if($hasMore ?? false)
        <div style="text-align: center; margin-top: 3rem;">
            <button id="load-more-btn" class="btn btn-primary" style="padding: 1rem 3rem; font-size: 1.1rem;">
                <i class="fas fa-arrow-down"></i> Load More Recognitions
            </button>
            <div id="loading-indicator" style="display: none; margin-top: 1rem;">
                <i class="fas fa-spinner fa-spin" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                <p style="margin-top: 0.5rem; color: var(--text-light);">Loading more recognitions...</p>
            </div>
        </div>
        @endif
        @else
        <div style="text-align: center; padding: 4rem; background: white; border-radius: 15px;">
            <i class="fas fa-award" style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
            <p style="color: var(--text-secondary); font-size: 1.125rem;">No bonus recognitions available yet.</p>
        </div>
        @endif
    </div>
</section>

<!-- Bonus Recognition Detail Modal -->
<div class="modal" id="bonus-modal">
    <div class="modal-content">
        <button class="modal-close" id="modal-close">&times;</button>
        <div class="modal-body" id="modal-body">
            <div style="text-align: center; padding: 2rem;">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--accent-color);"></i>
                <p>Loading recognition details...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('bonus-modal');
    const modalClose = document.getElementById('modal-close');
    const modalBody = document.getElementById('modal-body');
    const viewButtons = document.querySelectorAll('.view-bonus-btn');

    // Open modal and load bonus recognition details
    viewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const bonusId = this.getAttribute('data-id');
            loadBonusDetails(bonusId);
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

    function loadBonusDetails(id) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        modalBody.innerHTML = `
            <div style="text-align: center; padding: 2rem;">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--accent-color);"></i>
                <p>Loading recognition details...</p>
            </div>
        `;

        const baseUrl = window.BASE_URL || '';
        fetch(`${baseUrl}/bonus-recognitions/${id}`, {
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
                } else if (data.bonus) {
                    const bonus = data.bonus;
                    
                    modalBody.innerHTML = `
                        <div class="publication-detail-modal">
                            <div class="publication-detail-header">
                                <span class="publication-detail-category">${(bonus.recognition_type || 'Recognition').toUpperCase().replace('_', ' ')}</span>
                                <h2>${bonus.title}</h2>
                                <div class="publication-detail-authors">
                                    <i class="fas fa-user-edit"></i> ${bonus.user?.name || 'Anonymous'}
                                </div>
                            </div>
                            <div class="publication-detail-meta">
                                <div class="meta-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Year: ${bonus.year || 'N/A'}</span>
                                </div>
                                ${bonus.organization ? `<div class="meta-item"><i class="fas fa-building"></i> <span>Organization: ${bonus.organization}</span></div>` : ''}
                                ${bonus.journal_conference_name ? `<div class="meta-item"><i class="fas fa-book"></i> <span>Journal/Conference: ${bonus.journal_conference_name}</span></div>` : ''}
                                ${bonus.points ? `<div class="meta-item"><i class="fas fa-star"></i> <span>Points: ${bonus.points}</span></div>` : ''}
                            </div>
                            ${bonus.description ? `<div class="publication-detail-abstract"><h3><i class="fas fa-file-alt"></i> Description</h3><p>${bonus.description}</p></div>` : ''}
                            <div class="publication-detail-actions">
                                <a href="${window.BASE_URL || ''}/bonus-recognitions/${bonus.id}" class="btn btn-outline"><i class="fas fa-external-link-alt"></i> Full Page View</a>
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading bonus recognition:', error);
                modalBody.innerHTML = `
                    <div style="text-align: center; padding: 2rem;">
                        <i class="fas fa-exclamation-circle" style="font-size: 2rem; color: #ef4444; margin-bottom: 1rem;"></i>
                        <p>Error loading recognition details. Please try again.</p>
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
    const bonusContainer = document.getElementById('bonus-container');
    let currentOffset = {{ $bonusRecognitions->count() }};
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
                recognition_type: urlParams.get('recognition_type') || '',
                year: urlParams.get('year') || '',
                sort: urlParams.get('sort') || 'newest',
            };

            fetch('{{ route("bonus-recognitions.load-more") }}?' + new URLSearchParams(params), {
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
                        bonusContainer.appendChild(card);
                    });

                    const newViewButtons = tempDiv.querySelectorAll('.view-bonus-btn');
                    newViewButtons.forEach(button => {
                        button.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const bonusId = this.getAttribute('data-id');
                            loadBonusDetails(bonusId);
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
                        allLoadedMsg.innerHTML = '<i class="fas fa-check-circle"></i> All recognitions loaded';
                        loadingIndicator.parentElement.appendChild(allLoadedMsg);
                    }
                }
            })
            .catch(error => {
                console.error('Error loading more recognitions:', error);
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
