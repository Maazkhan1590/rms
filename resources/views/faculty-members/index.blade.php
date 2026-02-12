@extends('layouts.public')

@section('title', 'Faculty Members | Academic Research Portal')

@push('styles')
<style>
    .faculty-members-page {
        padding: 6.75rem 0 3.5rem;
        background: #f3f4f6;
    }
    .page-header-section {
        background: #ffffff;
        padding: 3rem 0;
        color: #111827;
        margin-bottom: 2rem;
        border-bottom: 1px solid #e5e7eb;
    }
    .page-header-section h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #111827;
    }
    .page-header-section p {
        font-size: 1.1rem;
        color: #6b7280;
    }
    .search-section {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 2rem;
    }
</style>
@endpush

@section('content')
<div class="faculty-members-page">
    <!-- Faculty Members Header -->
    <header class="page-header-section">
        <div class="container">
            <h1>Faculty Members</h1>
            <p>Browse our faculty members and their research contributions</p>
        </div>
    </header>

    <!-- Faculty Members Filter -->
    <section class="search-section">
        <div class="container">
            <form action="{{ route('faculty-members.index') }}" method="GET" style="display: flex; gap: 0.75rem; max-width: 700px; margin: 0 auto; align-items: stretch;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." style="flex: 1; padding: 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem; transition: border-color 0.2s;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'">
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; font-size: 0.95rem; white-space: nowrap;">
                    <i class="fas fa-search"></i> Search
                </button>
                @if(request('search'))
                <a href="{{ route('faculty-members.index') }}" class="btn btn-secondary" style="padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; font-size: 0.95rem; background: #6b7280; border: none; color: white; text-decoration: none; display: inline-flex; align-items: center; white-space: nowrap;" title="Clear search">
                    <i class="fas fa-times"></i> Reset
                </a>
                @endif
            </form>
            @if(request('search'))
            <div style="text-align: center; margin-top: 1rem; color: #6b7280; font-size: 0.9rem;">
                <i class="fas fa-info-circle"></i> Searching across all faculty members for: <strong>"{{ request('search') }}"</strong>
            </div>
            @endif
        </div>
    </section>

    <!-- Faculty Members Grid -->
    <section class="publications-grid-section">
        <div class="container">
            @if($facultyMembers->count() > 0)
            <div class="faculty-members-grid" id="facultyMembersGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
                @include('faculty-members.partials.card-grid', ['facultyMembers' => $facultyMembers])
            </div>

            <!-- Load More Button -->
            @if($facultyMembers->hasMorePages())
            <div style="margin-top: 2.5rem; text-align: center;">
                <button id="loadMoreBtn" class="btn btn-primary" style="padding: 0.875rem 2.5rem; border-radius: 8px; font-weight: 600; font-size: 1rem; background: linear-gradient(135deg, #3b82f6, #8b5cf6); border: none; color: white; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 12px rgba(59,130,246,0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(59,130,246,0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(59,130,246,0.3)'">
                    <span id="loadMoreText">Load More</span>
                    <span id="loadMoreSpinner" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> Loading...
                    </span>
                </button>
                <div style="margin-top: 1rem; color: #6b7280; font-size: 0.9rem;">
                    Showing <span id="showingCount">{{ $facultyMembers->count() }}</span> of <span id="totalCount">{{ $facultyMembers->total() }}</span> faculty members
                </div>
            </div>
            @else
            <div style="margin-top: 2rem; text-align: center; color: #6b7280; font-size: 0.95rem;">
                Showing all {{ $facultyMembers->total() }} faculty members
            </div>
            @endif
            @else
            <div style="text-align: center; padding: 4rem 2rem; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <i class="fas fa-users" style="font-size: 3rem; color: #9ca3af; margin-bottom: 1rem;"></i>
                <p style="font-size: 1.125rem; color: #6b7280; font-weight: 500;">No faculty members found.</p>
                @if(request('search'))
                    <p style="font-size: 0.95rem; color: #9ca3af; margin-top: 0.5rem;">Try adjusting your search criteria.</p>
                @endif
            </div>
            @endif
        </div>
    </section>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let isLoading = false;
    
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    if (!loadMoreBtn) return;
    
    const loadMoreText = document.getElementById('loadMoreText');
    const loadMoreSpinner = document.getElementById('loadMoreSpinner');
    const showingCount = document.getElementById('showingCount');
    const totalCount = document.getElementById('totalCount');
    const grid = document.getElementById('facultyMembersGrid');
    
    loadMoreBtn.addEventListener('click', function() {
        if (isLoading) return;
        
        isLoading = true;
        loadMoreBtn.disabled = true;
        loadMoreText.style.display = 'none';
        loadMoreSpinner.style.display = 'inline';
        
        // Build URL with search params
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('page', currentPage + 1);
        
        fetch('{{ route("faculty-members.index") }}?' + urlParams.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Append new cards to grid
            grid.insertAdjacentHTML('beforeend', data.html);
            
            // Update counters
            showingCount.textContent = data.showing;
            totalCount.textContent = data.total;
            
            // Update page counter
            currentPage = data.nextPage - 1;
            
            // Hide button if no more pages
            if (!data.hasMore) {
                loadMoreBtn.parentElement.style.display = 'none';
                grid.parentElement.insertAdjacentHTML('afterend', 
                    '<div style="margin-top: 2rem; text-align: center; color: #6b7280; font-size: 0.95rem;">Showing all ' + data.total + ' faculty members</div>'
                );
            }
            
            isLoading = false;
            loadMoreBtn.disabled = false;
            loadMoreText.style.display = 'inline';
            loadMoreSpinner.style.display = 'none';
        })
        .catch(error => {
            console.error('Error loading more faculty members:', error);
            alert('Error loading more faculty members. Please try again.');
            isLoading = false;
            loadMoreBtn.disabled = false;
            loadMoreText.style.display = 'inline';
            loadMoreSpinner.style.display = 'none';
        });
    });
});
</script>
@endpush
@endsection
