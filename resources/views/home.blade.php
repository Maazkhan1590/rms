@extends('layouts.public')

@section('title', 'Academic Research Portal | Home')

@section('content')
<!-- Hero Section -->
<header class="hero">
    <div class="slider-container">
        <div class="slider">
            <div class="slide active">
                <div class="slide-overlay"></div>
                <div class="slide-image" style="background-image: url('https://images.unsplash.com/photo-1532094349884-543bc11b234d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1950&q=80');"></div>
                <div class="slide-content">
                    <div class="container">
                        <div class="slide-tag">New Research</div>
                        <h1 class="slide-title">Advancing Knowledge Through <span class="highlight">Innovative</span> Research</h1>
                        <p class="slide-description">Submit, discover, and collaborate on cutting-edge academic research across all disciplines with our global community of scholars.</p>
                        <div class="hero-buttons">
                            @guest
                            <a href="{{ route('register') }}" class="btn btn-primary">
                                <i class="fas fa-rocket"></i> Start Your Research
                            </a>
                            @else
                            @if(auth()->user()->hasRole('Student'))
                            <a href="{{ route('publications.create') }}" class="btn btn-primary">
                                <i class="fas fa-rocket"></i> Submit Publication
                            </a>
                            @else
                            <a href="{{ route('admin.home') }}" class="btn btn-primary">
                                <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                            </a>
                            @endif
                            @endguest
                            <a href="{{ route('publications.index') }}" class="btn btn-outline">
                                <i class="fas fa-book-reader"></i> Explore Publications
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="slide">
                <div class="slide-overlay"></div>
                <div class="slide-image" style="background-image: url('https://images.unsplash.com/photo-1589998059171-988d887df646?ixlib=rb-4.0.3&auto=format&fit=crop&w=1950&q=80');"></div>
                <div class="slide-content">
                    <div class="container">
                        <div class="slide-tag">Open Access</div>
                        <h1 class="slide-title">Global Knowledge <span class="highlight">Without Barriers</span></h1>
                        <p class="slide-description">Discover thousands of peer-reviewed papers available to researchers worldwide. Access cutting-edge research across all academic fields.</p>
                        <div class="hero-buttons">
                            <a href="{{ route('publications.index') }}" class="btn btn-primary">
                                <i class="fas fa-search"></i> Browse Publications
                            </a>
                            <a href="#" class="btn btn-outline">
                                <i class="fas fa-download"></i> Download Resources
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="slide">
                <div class="slide-overlay"></div>
                <div class="slide-image" style="background-image: url('https://images.unsplash.com/photo-1554475900-7c0f4a35b8c1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1950&q=80');"></div>
                <div class="slide-content">
                    <div class="container">
                        <div class="slide-tag">Collaborate</div>
                        <h1 class="slide-title">Join a Network of <span class="highlight">Leading Experts</span></h1>
                        <p class="slide-description">Connect with researchers from top institutions around the world. Collaborate on groundbreaking projects and share knowledge across disciplines.</p>
                        <div class="hero-buttons">
                            @guest
                            <a href="{{ route('register') }}" class="btn btn-primary">
                                <i class="fas fa-users"></i> Join Our Community
                            </a>
                            @else
                            <a href="{{ route('publications.index') }}" class="btn btn-primary">
                                <i class="fas fa-book-open"></i> View Publications
                            </a>
                            @endguest
                            <a href="#" class="btn btn-outline">
                                <i class="fas fa-calendar-alt"></i> View Events
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="slider-controls">
            <button class="slider-prev" aria-label="Previous slide">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="slider-dots">
                <span class="dot active"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
            <button class="slider-next" aria-label="Next slide">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</header>

<!-- Search Section -->
<section class="search-section">
    <div class="container">
        <div class="section-intro">
            <h2 class="section-title">Discover Research</h2>
            <p class="section-subtitle">Search our extensive database of peer-reviewed publications</p>
        </div>
        <form action="{{ route('publications.index') }}" method="GET" class="search-container elegant">
            <div class="search-icon">
                <i class="fas fa-search"></i>
            </div>
            <input type="text" id="search-input" name="search" value="{{ request('search') }}" placeholder="Search by title, author, keyword, or DOI...">
            <button type="submit" id="search-btn" class="btn btn-primary">
                Search <i class="fas fa-arrow-right"></i>
            </button>
        </form>
        <form action="{{ route('publications.index') }}" method="GET" class="filter-options elegant">
            <div class="filter-group">
                <label><i class="fas fa-filter"></i> Filter by:</label>
                <select id="filter-category" name="type" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($publicationTypes ?? [] as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
                <select id="filter-year" name="year" onchange="this.form.submit()">
                    <option value="">All Years</option>
                    @foreach($publicationYears ?? [] as $year)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="quick-filters">
                <span class="quick-filter active">All</span>
                <span class="quick-filter">Trending</span>
                <span class="quick-filter">Recently Added</span>
                <span class="quick-filter">Most Cited</span>
            </div>
        </form>
    </div>
</section>

<!-- Featured Publications -->
<section class="publications-section">
    <div class="container">
        <div class="section-header elegant">
            <div>
                <h2 class="section-title">Featured Publications</h2>
                <p class="section-subtitle">Curated selection of groundbreaking research</p>
            </div>
            <a href="{{ route('publications.index') }}" class="view-all">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="publications-grid">
            @forelse($publications->take(6) as $publication)
            <div class="publication-card" data-publication-id="{{ $publication->id }}" onclick="if(!event.target.closest('.publication-actions')) { window.location.href='{{ route('publications.show', $publication->id) }}'; }" style="cursor: pointer;">
                <div class="publication-header">
                    <h3 class="publication-title">{{ $publication->title }}</h3>
                    <div class="publication-authors">
                        <i class="fas fa-user-edit"></i>
                        {{ $publication->submitter->name ?? 'Anonymous' }}
                        @if($publication->primaryAuthor)
                            , {{ $publication->primaryAuthor->name }}
                        @endif
                    </div>
                </div>
                <div class="publication-body">
                    <p class="publication-abstract">
                        {{ Str::limit(strip_tags($publication->abstract ?? ''), 200) }}
                    </p>
                </div>
                <div class="publication-footer">
                    <span class="publication-category">{{ strtoupper(str_replace('_', ' ', $publication->publication_type ?? 'Publication')) }}</span>
                    <div class="publication-meta">
                        <div class="publication-date">
                            <i class="far fa-calendar"></i>
                            {{ $publication->published_at ? $publication->published_at->format('F d, Y') : ($publication->publication_year ?? 'N/A') }}
                        </div>
                    </div>
                    <div class="publication-actions">
                        <button class="btn btn-outline view-publication-btn" data-id="{{ $publication->id }}" onclick="event.stopPropagation(); loadPublicationModal({{ $publication->id }});">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 4rem;">
                <i class="fas fa-book-open" style="font-size: 4rem; color: var(--text-lighter); margin-bottom: 1.5rem; opacity: 0.5;"></i>
                <h3 style="color: var(--text-color); margin-bottom: 0.5rem;">No publications available</h3>
                <p style="color: var(--text-lighter);">Check back soon for new research publications.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="section-intro">
            <h2 class="section-title">By The Numbers</h2>
            <p class="section-subtitle">Our impact in the global research community</p>
        </div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number" data-count="{{ \App\Models\Publication::where('status', 'approved')->count() }}">0</h3>
                    <p class="stat-label">Published Papers</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number" data-count="{{ \App\Models\User::count() }}">0</h3>
                    <p class="stat-label">Active Researchers</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-university"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number" data-count="{{ \App\Models\College::where('is_active', true)->count() }}">0</h3>
                    <p class="stat-label">Partner Institutions</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-globe"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number" data-count="1">0</h3>
                    <p class="stat-label">Countries Represented</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Share Your Research?</h2>
            <p>Join thousands of researchers who have published their work on our platform. Experience seamless submission, rigorous peer review, and global visibility.</p>
            <div class="cta-buttons">
                @guest
                <a href="{{ route('register') }}" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Submit Your Paper
                </a>
                @else
                @if(auth()->user()->hasRole('Student'))
                <a href="{{ route('publications.create') }}" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Submit Your Paper
                </a>
                @else
                <a href="{{ route('admin.home') }}" class="btn btn-primary">
                    <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                </a>
                @endif
                @endguest
                <a href="#" class="btn btn-outline">
                    <i class="fas fa-book"></i> Author Guidelines
                </a>
            </div>
        </div>
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
function loadPublicationModal(id) {
    const modal = document.getElementById('publication-modal');
    const modalBody = document.getElementById('modal-body');
    
    if (!modal || !modalBody) return;
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    modalBody.innerHTML = `
        <div style="text-align: center; padding: 2rem;">
            <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--accent-color);"></i>
            <p>Loading publication details...</p>
        </div>
    `;

    fetch(`/publications/${id}`, {
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
                            <a href="/publications/${pub.id}" class="btn btn-outline"><i class="fas fa-external-link-alt"></i> Full Page View</a>
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
                    <button class="btn btn-primary" onclick="closePublicationModal()">Close</button>
                </div>
            `;
        });
}

function closePublicationModal() {
    const modal = document.getElementById('publication-modal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('publication-modal');
    const modalClose = document.getElementById('modal-close');
    
    if (modalClose) {
        modalClose.addEventListener('click', closePublicationModal);
    }
    
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closePublicationModal();
            }
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                closePublicationModal();
            }
        });
    }
});
</script>
@endpush
@endsection
