@extends('layouts.public')

@section('title', 'Grants | Academic Research Portal')

@push('styles')
<style>
    .grants-page {
        padding: 6rem 0 3rem;
        background: #f3f4f6;
    }
    .page-header-section {
        background: #ffffff;
        padding: 2rem 0;
        color: #111827;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    .page-header-section h1 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.375rem;
        color: #111827;
    }
    .page-header-section p {
        font-size: 1rem;
        color: #6b7280;
        margin: 0;
    }
    .search-section {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="grants-page">
    <header class="page-header-section">
        <div class="container">
            <h1>Grants</h1>
            <p>Browse approved research grants and funding opportunities</p>
        </div>
    </header>

    <section class="search-section">
        <div class="container">
            <form action="{{ route('grants.index') }}" method="GET" style="display: flex; gap: 0.75rem; max-width: 700px; margin: 0 auto;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search grants..." style="flex: 1; padding: 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem; transition: border-color 0.2s;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'">
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; font-size: 0.95rem;">Search</button>
            </form>
        </div>
    </section>

    <section class="publications-grid-section">
        <div class="container">
            @if($grants->count() > 0)
            <div class="grants-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem;">
                @foreach($grants as $grant)
                <div class="grant-card" style="background: white; border-radius: 10px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: all 0.2s ease; cursor: pointer; border: 1px solid #e5e7eb;" onclick="window.location.href='{{ route('grants.show', $grant->id) }}'" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'; this.style.borderColor='#10b981'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'; this.style.borderColor='#e5e7eb'">
                    <h3 style="font-size: 1.15rem; font-weight: 600; margin: 0 0 0.75rem 0; color: #111827; line-height: 1.4;">{{ $grant->title }}</h3>
                    @if($grant->submitter)
                        <p style="color: #6b7280; font-size: 0.875rem; margin: 0 0 0.5rem 0;"><i class="fas fa-user" style="margin-right: 0.25rem;"></i>{{ $grant->submitter->name }}</p>
                    @endif
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #e5e7eb;">
                        @if($grant->sponsor)
                            <div style="font-size: 0.8rem; color: #6b7280;"><i class="fas fa-building" style="margin-right: 0.25rem;"></i>{{ $grant->sponsor }}</div>
                        @endif
                        @if($grant->award_year)
                            <div style="font-size: 0.8rem; color: #6b7280;"><i class="fas fa-calendar" style="margin-right: 0.25rem;"></i>{{ $grant->award_year }}</div>
                        @endif
                        @if($grant->amount_omr)
                            <div style="font-size: 0.8rem; color: #10b981; font-weight: 600;"><i class="fas fa-money-bill-wave" style="margin-right: 0.25rem;"></i>{{ number_format($grant->amount_omr, 2) }} OMR</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <div style="margin-top: 2rem; display: flex; justify-content: center;">
                {{ $grants->links() }}
            </div>
            @else
            <div style="text-align: center; padding: 4rem 2rem; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <i class="fas fa-hand-holding-usd" style="font-size: 3rem; color: #9ca3af; margin-bottom: 1rem;"></i>
                <p style="font-size: 1.125rem; color: #6b7280; font-weight: 500;">No grants found.</p>
                @if(request('search'))
                    <p style="font-size: 0.95rem; color: #9ca3af; margin-top: 0.5rem;">Try adjusting your search criteria.</p>
                @endif
            </div>
            @endif
        </div>
    </section>
</div>
@endsection
