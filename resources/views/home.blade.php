@extends('layouts.public')

@section('title', 'Academic Research Portal | Home')

@section('content')
@auth
@if(auth()->user()->isAdmin || auth()->user()->isResearchCoordinator() || auth()->user()->isDean())
<meta http-equiv="refresh" content="0;url={{ route('admin.home') }}">
<script>
    // IMMEDIATE redirect admin/coordinator/dean to admin dashboard - no delay
    // Use replace() instead of href to prevent back button issues
    window.location.replace("{{ route('admin.home') }}");
</script>
<div style="text-align: center; padding: 2rem;">
    <p>Redirecting to dashboard...</p>
    <p><a href="{{ route('admin.home') }}">Click here if you are not redirected</a></p>
</div>
@endif
@endauth
<!-- Hero Section -->
<header class="hero">
    <div class="slider-container">
        <div class="slider">
            @forelse($sliders as $index => $slider)
            <div class="slide {{ $index === 0 ? 'active' : '' }}">
                <div class="slide-overlay"></div>
                @if($slider->image_url)
                    @if(filter_var($slider->image_url, FILTER_VALIDATE_URL))
                        <div class="slide-image" style="background-image: url('{{ $slider->image_url }}');"></div>
                    @else
                        <div class="slide-image" style="background-image: url('{{ asset('storage/' . $slider->image_url) }}');"></div>
                    @endif
                @else
                    <div class="slide-image" style="background-image: url('https://images.unsplash.com/photo-1532094349884-543bc11b234d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1950&q=80');"></div>
                @endif
                <div class="slide-content">
                    <div class="container">
                        @if($slider->tag)
                            <div class="slide-tag">{{ $slider->tag }}</div>
                        @endif
                        @if($slider->title)
                            <h1 class="slide-title">{!! $slider->title !!}</h1>
                        @endif
                        @if($slider->description)
                            <p class="slide-description">{{ $slider->description }}</p>
                        @endif
                        <div class="hero-buttons">
                            @if($slider->button_text && $slider->button_link)
                                <a href="{{ $slider->button_link }}" class="btn btn-primary">
                                    @if(strpos($slider->button_text, '<i') === false)
                                        <i class="fas fa-rocket"></i>
                                    @endif
                                    {!! $slider->button_text !!}
                                </a>
                            @endif
                            @if($slider->button_text_secondary && $slider->button_link_secondary)
                                <a href="{{ $slider->button_link_secondary }}" class="btn btn-outline">
                                    {!! $slider->button_text_secondary !!}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <!-- Default slider if no sliders in database -->
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
                            <a href="{{ route('publications.create') }}" class="btn btn-primary">
                                <i class="fas fa-rocket"></i> Submit
                            </a>
                            @endguest
                            <a href="{{ route('publications.index') }}" class="btn btn-outline">
                                <i class="fas fa-book-reader"></i> Explore Publications
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
        @if($sliders->count() > 0)
        <div class="slider-controls">
            <button class="slider-prev" aria-label="Previous slide">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="slider-dots">
                @foreach($sliders as $index => $slider)
                    <span class="dot {{ $index === 0 ? 'active' : '' }}"></span>
                @endforeach
            </div>
            <button class="slider-next" aria-label="Next slide">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        @endif
    </div>
</header>

<!-- Stats Section -->
<section class="stats-section" style="padding: 3rem 0; background: #f8f9fa;">
    <div class="container" style="max-width: 1100px;">
        <div class="section-intro" style="text-align: center; margin-bottom: 2rem;">
            <h2 class="section-title" style="font-size: 1.75rem; margin-bottom: 0.5rem;">By The Numbers</h2>
            <p class="section-subtitle" style="font-size: 0.95rem; color: #6b7280;">Our impact in the global research community</p>
        </div>
        @php
            // Count faculty users - try multiple approaches to ensure we get the count
            try {
                // Method 1: Using whereHas (standard Laravel approach)
                $facultyCount = \App\Models\User::whereHas('roles', function($q) { 
                    $q->where('title', 'Faculty'); 
                })->count();
                
                // Method 2: If count is 0, try direct join (fallback)
                if ($facultyCount == 0) {
                    $facultyCount = \App\Models\User::join('role_user', 'users.id', '=', 'role_user.user_id')
                        ->join('roles', 'role_user.role_id', '=', 'roles.id')
                        ->where('roles.title', 'Faculty')
                        ->distinct('users.id')
                        ->count('users.id');
                }
            } catch (\Exception $e) {
                // Fallback: count all users if there's an error
                $facultyCount = \App\Models\User::count();
            }
            
            $publicationsCount = \App\Models\Publication::where('status', 'approved')->count();
            $grantsCount = \App\Models\Grant::where('status', 'approved')->count();
            $rtnCount = \App\Models\RtnSubmission::where('status', 'approved')->count();
            $bonusCount = \App\Models\BonusRecognition::where('status', 'approved')->count();
        @endphp
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1.25rem;">
            <a href="{{ route('publications.index') }}" class="stat-card-link" style="text-decoration: none; color: inherit;">
                <div class="stat-card" style="background: white; padding: 1.25rem; border-radius: 8px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s ease; cursor: pointer;">
                    <div class="stat-icon" style="font-size: 1.75rem; color: #3b82f6; margin-bottom: 0.75rem; transition: transform 0.3s ease;">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number" data-count="{{ $publicationsCount }}" style="font-size: 1.75rem; font-weight: 700; color: #111827; margin-bottom: 0.25rem; transition: color 0.3s ease;">0</h3>
                        <p class="stat-label" style="font-size: 0.85rem; color: #6b7280; margin: 0;">Published Papers</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('grants.index') }}" class="stat-card-link" style="text-decoration: none; color: inherit;">
                <div class="stat-card" style="background: white; padding: 1.25rem; border-radius: 8px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s ease; cursor: pointer;">
                    <div class="stat-icon" style="font-size: 1.75rem; color: #10b981; margin-bottom: 0.75rem; transition: transform 0.3s ease;">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number" data-count="{{ $grantsCount }}" style="font-size: 1.75rem; font-weight: 700; color: #111827; margin-bottom: 0.25rem; transition: color 0.3s ease;">0</h3>
                        <p class="stat-label" style="font-size: 0.85rem; color: #6b7280; margin: 0;">Grants</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('rtn-submissions.index') }}" class="stat-card-link" style="text-decoration: none; color: inherit;">
                <div class="stat-card" style="background: white; padding: 1.25rem; border-radius: 8px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s ease; cursor: pointer;">
                    <div class="stat-icon" style="font-size: 1.75rem; color: #8b5cf6; margin-bottom: 0.75rem; transition: transform 0.3s ease;">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number" data-count="{{ $rtnCount }}" style="font-size: 1.75rem; font-weight: 700; color: #111827; margin-bottom: 0.25rem; transition: color 0.3s ease;">0</h3>
                        <p class="stat-label" style="font-size: 0.85rem; color: #6b7280; margin: 0;">RTN Submissions</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('bonus-recognitions.index') }}" class="stat-card-link" style="text-decoration: none; color: inherit;">
                <div class="stat-card" style="background: white; padding: 1.25rem; border-radius: 8px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s ease; cursor: pointer;">
                    <div class="stat-icon" style="font-size: 1.75rem; color: #f59e0b; margin-bottom: 0.75rem; transition: transform 0.3s ease;">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number" data-count="{{ $bonusCount }}" style="font-size: 1.75rem; font-weight: 700; color: #111827; margin-bottom: 0.25rem; transition: color 0.3s ease;">0</h3>
                        <p class="stat-label" style="font-size: 0.85rem; color: #6b7280; margin: 0;">Recognitions</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('faculty-members.index') }}" class="stat-card-link" style="text-decoration: none; color: inherit;">
                <div class="stat-card" style="background: white; padding: 1.25rem; border-radius: 8px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s ease; cursor: pointer;">
                    <div class="stat-icon" style="font-size: 1.75rem; color: #ef4444; margin-bottom: 0.75rem; transition: transform 0.3s ease;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number" data-count="{{ $facultyCount }}" style="font-size: 1.75rem; font-weight: 700; color: #111827; margin-bottom: 0.25rem; transition: color 0.3s ease;">0</h3>
                        <p class="stat-label" style="font-size: 0.85rem; color: #6b7280; margin: 0;">Active Researchers</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <style>
        .stat-card-link {
            display: block;
        }
        .stat-card-link:hover .stat-card {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.12) !important;
        }
        .stat-card-link:hover .stat-icon {
            transform: scale(1.1);
        }
        .stat-card-link:nth-child(1):hover .stat-number {
            color: #3b82f6 !important;
        }
        .stat-card-link:nth-child(2):hover .stat-number {
            color: #10b981 !important;
        }
        .stat-card-link:nth-child(3):hover .stat-number {
            color: #8b5cf6 !important;
        }
        .stat-card-link:nth-child(4):hover .stat-number {
            color: #f59e0b !important;
        }
        .stat-card-link:nth-child(5):hover .stat-number {
            color: #ef4444 !important;
        }
        @media (max-width: 1024px) {
            .stats-section .stats-grid {
                grid-template-columns: repeat(3, 1fr) !important;
                gap: 1rem !important;
            }
        }
        @media (max-width: 768px) {
            .stats-section .stats-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 1rem !important;
            }
            .stats-section .stat-card {
                padding: 1rem !important;
            }
            .stats-section .stat-icon {
                font-size: 1.5rem !important;
            }
            .stats-section .stat-number {
                font-size: 1.5rem !important;
            }
        }
        @media (max-width: 480px) {
            .stats-section .stats-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</section>

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

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Share Your Research?</h2>
            <p>Join thousands of researchers who have published their work on our platform. Experience seamless submission, rigorous peer review, and global visibility.</p>
            <div class="cta-buttons">
                @guest
                <a href="{{ route('register') }}" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Submit
                </a>
                @else
                @php
                    $user = auth()->user();
                    $isPureFaculty = $user->hasRole('Faculty') && !$user->isAdmin && !$user->isResearchCoordinator() && !$user->isDean();
                @endphp
                @if($isPureFaculty)
                <a href="{{ route('publications.create') }}" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Submit
                </a>
                <a href="{{ route('faculty-members.show', $user->id) }}" class="btn btn-primary" style="margin-left: 1rem;">
                    <i class="fas fa-user-circle"></i> My Profile
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
// Counter animation for stats
document.addEventListener('DOMContentLoaded', function() {
    const statNumbers = document.querySelectorAll('.stat-number');
    
    const animateCounter = (element) => {
        const target = parseInt(element.getAttribute('data-count'));
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16); // 60fps
        let current = 0;
        
        const updateCounter = () => {
            current += increment;
            if (current < target) {
                element.textContent = Math.floor(current);
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = target;
            }
        };
        
        // Only animate if element is in viewport
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    updateCounter();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        observer.observe(element);
    };
    
    statNumbers.forEach(animateCounter);
});

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
