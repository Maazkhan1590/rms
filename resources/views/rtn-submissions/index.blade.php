@extends('layouts.public')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'RTN Submissions | Academic Research Portal')

@push('styles')
<style>
    .rtn-page {
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
<div class="rtn-page">
    <header class="page-header-section">
        <div class="container">
            <h1>RTN Submissions</h1>
            <p>Browse approved Research and Teaching Network submissions</p>
        </div>
    </header>

    <section class="search-section">
        <div class="container">
            <form action="{{ route('rtn-submissions.index') }}" method="GET" style="display: flex; gap: 0.75rem; max-width: 700px; margin: 0 auto;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search RTN submissions..." style="flex: 1; padding: 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem; transition: border-color 0.2s;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'">
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; font-size: 0.95rem;">Search</button>
            </form>
        </div>
    </section>

    <section class="publications-grid-section">
        <div class="container">
            @if($rtnSubmissions->count() > 0)
            <div class="rtn-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem;">
                @foreach($rtnSubmissions as $rtn)
                <div class="rtn-card" style="background: white; border-radius: 10px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: all 0.2s ease; cursor: pointer; border: 1px solid #e5e7eb;" onclick="window.location.href='{{ route('rtn-submissions.show', $rtn->id) }}'" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'; this.style.borderColor='#8b5cf6'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'; this.style.borderColor='#e5e7eb'">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                        <span style="padding: 0.25rem 0.75rem; border-radius: 6px; background: #8b5cf6; color: white; font-size: 0.75rem; font-weight: 600;">{{ $rtn->rtn_type }}</span>
                    </div>
                    <h3 style="font-size: 1.15rem; font-weight: 600; margin: 0 0 0.75rem 0; color: #111827; line-height: 1.4;">{{ $rtn->title }}</h3>
                    @if($rtn->user)
                        <p style="color: #6b7280; font-size: 0.875rem; margin: 0 0 0.5rem 0;"><i class="fas fa-user" style="margin-right: 0.25rem;"></i>{{ $rtn->user->name }}</p>
                    @endif
                    @if($rtn->description)
                        <p style="color: #6b7280; font-size: 0.85rem; margin: 0 0 0.75rem 0; line-height: 1.5;">{{ Str::limit($rtn->description, 120) }}</p>
                    @endif
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #e5e7eb;">
                        @if($rtn->year)
                            <div style="font-size: 0.8rem; color: #6b7280;"><i class="fas fa-calendar" style="margin-right: 0.25rem;"></i>{{ $rtn->year }}</div>
                        @endif
                        @if($rtn->amount_omr)
                            <div style="font-size: 0.8rem; color: #8b5cf6; font-weight: 600;"><i class="fas fa-money-bill-wave" style="margin-right: 0.25rem;"></i>{{ number_format($rtn->amount_omr, 2) }} OMR</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <div style="margin-top: 2rem; display: flex; justify-content: center;">
                {{ $rtnSubmissions->links() }}
            </div>
            @else
            <div style="text-align: center; padding: 4rem 2rem; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <i class="fas fa-certificate" style="font-size: 3rem; color: #9ca3af; margin-bottom: 1rem;"></i>
                <p style="font-size: 1.125rem; color: #6b7280; font-weight: 500;">No RTN submissions found.</p>
                @if(request('search'))
                    <p style="font-size: 0.95rem; color: #9ca3af; margin-top: 0.5rem;">Try adjusting your search criteria.</p>
                @endif
            </div>
            @endif
        </div>
    </section>
</div>
@endsection
