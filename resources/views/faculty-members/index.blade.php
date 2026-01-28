@extends('layouts.public')

@section('title', 'Faculty Members | Academic Research Portal')

@section('content')
<!-- Faculty Members Header -->
<header class="page-header">
    <div class="container">
        <h1>Faculty Members</h1>
        <p>Browse our faculty members and their research contributions</p>
    </div>
</header>

<!-- Faculty Members Filter -->
<section class="publications-filter">
    <div class="container">
        <form action="{{ route('faculty-members.index') }}" method="GET" class="filter-container">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search faculty members..." style="flex: 1; min-width: 300px; padding: 1rem 1.5rem; border: 1px solid var(--border-color); border-radius: var(--border-radius); font-size: 1rem;">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
</section>

<!-- Faculty Members Grid -->
<section class="publications-grid-section">
    <div class="container">
        @if($facultyMembers->count() > 0)
        <div class="faculty-members-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem; margin-top: 3rem;">
            @foreach($facultyMembers as $member)
            <div class="faculty-member-card" style="background: white; border-radius: 16px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; cursor: pointer;" onclick="window.location.href='{{ route('faculty-members.show', $member->id) }}'">
                <div class="member-header" style="text-align: center; margin-bottom: 1.5rem;">
                    <div class="member-avatar" style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 2.5rem; color: white; font-weight: 700;">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-color);">{{ $member->name }}</h3>
                    @if($member->college)
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">{{ $member->college->name }}</p>
                    @endif
                    @if($member->department)
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">{{ $member->department->name }}</p>
                    @endif
                </div>
                <div class="member-stats" style="display: flex; justify-content: space-around; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                    <div style="text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color);">
                            {{ ($member->publications_count ?? 0) + ($member->primary_author_publications_count ?? 0) }}
                        </div>
                        <div style="font-size: 0.875rem; color: var(--text-secondary);">Publications</div>
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
        <div style="text-align: center; padding: 4rem 2rem;">
            <p style="font-size: 1.125rem; color: var(--text-secondary);">No faculty members found.</p>
        </div>
        @endif
    </div>
</section>
@endsection
