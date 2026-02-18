@extends('layouts.public')

@section('title', 'Awards | Academic Research Portal')

@section('content')
<header class="page-header">
    <div class="container">
        <h1>Awards</h1>
        <p>Browse approved awards and recognitions</p>
    </div>
</header>

<section class="publications-filter">
    <div class="container">
        <form action="{{ route('awards.index') }}" method="GET" class="filter-container">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search awards..." style="flex: 1; min-width: 300px; padding: 1rem 1.5rem; border: 1px solid var(--border-color); border-radius: var(--border-radius); font-size: 1rem;">
            <div class="filter-options">
                <select name="type" style="padding: 1rem 1.5rem; border: 1px solid var(--border-color); border-radius: var(--border-radius); background: white; font-size: 0.95rem;">
                    <option value="">All Types</option>
                    @foreach(['national', 'international', 'regional', 'institutional', 'other'] as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
                <select name="year" style="padding: 1rem 1.5rem; border: 1px solid var(--border-color); border-radius: var(--border-radius); background: white; font-size: 0.95rem;">
                    <option value="">All Years</option>
                    @foreach($awards->pluck('year')->unique()->filter()->sortDesc() as $year)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
                <select name="sort" style="padding: 1rem 1.5rem; border: 1px solid var(--border-color); border-radius: var(--border-radius); background: white; font-size: 0.95rem;">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                </select>
                <button type="submit" class="btn btn-primary">Apply Filters</button>
            </div>
        </form>
    </div>
</section>

<section class="publications-grid-section">
    <div class="container">
        @if($awards->count() > 0)
        <div class="publications-grid-full" id="awards-container">
            @include('awards.partials.award-card', ['awards' => $awards])
        </div>
        @if($hasMore ?? false)
        <div style="text-align: center; margin-top: 3rem;">
            <button id="load-more-btn" class="btn btn-primary" style="padding: 1rem 3rem; font-size: 1.1rem;">
                <i class="fas fa-arrow-down"></i> Load More Awards
            </button>
            <div id="loading-indicator" style="display: none; margin-top: 1rem;">
                <i class="fas fa-spinner fa-spin" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                <p style="margin-top: 0.5rem; color: var(--text-light);">Loading more awards...</p>
            </div>
        </div>
        @endif
        @else
        <div style="text-align: center; padding: 4rem; background: white; border-radius: 15px;">
            <i class="fas fa-trophy" style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
            <p style="color: var(--text-secondary); font-size: 1.125rem;">No awards available yet.</p>
        </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadMoreBtn = document.getElementById('load-more-btn');
    const loadingIndicator = document.getElementById('loading-indicator');
    const awardsContainer = document.getElementById('awards-container');
    let currentOffset = {{ $awards->count() }};
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
                type: urlParams.get('type') || '',
                year: urlParams.get('year') || '',
                sort: urlParams.get('sort') || 'newest',
            };

            fetch('{{ route("awards.load-more") }}?' + new URLSearchParams(params), {
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
                        awardsContainer.appendChild(card);
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
                        allLoadedMsg.innerHTML = '<i class="fas fa-check-circle"></i> All awards loaded';
                        loadingIndicator.parentElement.appendChild(allLoadedMsg);
                    }
                }
            })
            .catch(error => {
                console.error('Error loading more awards:', error);
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
