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
            <form action="{{ route('faculty-members.index') }}" method="GET" style="display: flex; gap: 1rem; max-width: 800px; margin: 0 auto;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." style="flex: 1; padding: 0.875rem 1.25rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: border-color 0.3s;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'">
                <button type="submit" class="btn btn-primary" style="padding: 0.875rem 2rem; border-radius: 8px; font-weight: 600;">Search</button>
            </form>
        </div>
    </section>

    <!-- Faculty Members Grid -->
    <section class="publications-grid-section">
        <div class="container">
            @if($facultyMembers->count() > 0)
            <div class="faculty-members-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
            @foreach($facultyMembers as $member)
            @php
                // Use unique publications count to avoid double counting
                $totalPublications = $member->unique_publications_count ?? 0;
                $totalGrants = $member->grants_count ?? 0;
                $totalRtn = $member->rtn_submissions_count ?? 0;
                $totalRecognitions = $member->bonus_recognitions_count ?? 0;
            @endphp
            <div class="faculty-member-card" style="background: white; border-radius: 10px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: all 0.2s ease; cursor: pointer; border: 1px solid #e5e7eb; height: 100%; display: flex; flex-direction: column;" onclick="window.location.href='{{ route('faculty-members.show', $member->id) }}'" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'; this.style.borderColor='#3b82f6'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'; this.style.borderColor='#e5e7eb'">
                <div class="member-header" style="text-align: center; margin-bottom: 1.25rem;">
                    <div class="member-avatar" style="width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #8b5cf6); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; font-size: 1.75rem; color: white; font-weight: 700; box-shadow: 0 2px 8px rgba(59,130,246,0.25);">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <h3 style="font-size: 1.15rem; font-weight: 600; margin: 0 0 0.25rem 0; color: #111827; line-height: 1.4;">{{ $member->name }}</h3>
                    @if($member->designation)
                        <p style="color: #6b7280; font-size: 0.875rem; margin: 0 0 0.2rem 0; font-weight: 500;">{{ $member->designation }}</p>
                    @endif
                    @if($member->college)
                        <p style="color: #9ca3af; font-size: 0.8rem; margin: 0 0 0.1rem 0;">{{ $member->college->name }}</p>
                    @endif
                    @if($member->department)
                        <p style="color: #9ca3af; font-size: 0.8rem; margin: 0;">{{ $member->department->name }}</p>
                    @endif
                </div>
                <div class="member-stats" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; padding-top: 1.25rem; border-top: 1px solid #e5e7eb; margin-top: auto;">
                    <div style="text-align: center; padding: 0.625rem 0.5rem; background: #f8f9fa; border-radius: 6px;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: #3b82f6; margin-bottom: 0.15rem; line-height: 1.2;">{{ $totalPublications }}</div>
                        <div style="font-size: 0.75rem; color: #6b7280; font-weight: 500;">Publications</div>
                    </div>
                    <div style="text-align: center; padding: 0.625rem 0.5rem; background: #f8f9fa; border-radius: 6px;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: #10b981; margin-bottom: 0.15rem; line-height: 1.2;">{{ $totalGrants }}</div>
                        <div style="font-size: 0.75rem; color: #6b7280; font-weight: 500;">Grants</div>
                    </div>
                    <div style="text-align: center; padding: 0.625rem 0.5rem; background: #f8f9fa; border-radius: 6px;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: #8b5cf6; margin-bottom: 0.15rem; line-height: 1.2;">{{ $totalRtn }}</div>
                        <div style="font-size: 0.75rem; color: #6b7280; font-weight: 500;">RTN</div>
                    </div>
                    <div style="text-align: center; padding: 0.625rem 0.5rem; background: #f8f9fa; border-radius: 6px;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: #f59e0b; margin-bottom: 0.15rem; line-height: 1.2;">{{ $totalRecognitions }}</div>
                        <div style="font-size: 0.75rem; color: #6b7280; font-weight: 500;">Recognitions</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

            <!-- Pagination -->
            <div style="margin-top: 3rem; display: flex; justify-content: center;">
                {{ $facultyMembers->links() }}
            </div>
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
@endsection
