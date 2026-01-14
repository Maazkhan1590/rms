@extends('layouts.base')

@section('title', 'Grant Details - RMS')

@section('content')
<div style="max-width: 1000px; margin: 2rem auto; padding: 0 2rem;">
    <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h1 style="margin-bottom: 1rem;">{{ $grant->title }}</h1>
        
        <div style="margin-bottom: 1.5rem;">
            <strong>Status:</strong> 
            @if($grant->status == 'approved')
                <span style="color: green;">Approved</span>
            @elseif($grant->status == 'pending' || $grant->status == 'submitted')
                <span style="color: orange;">Pending</span>
            @elseif($grant->status == 'rejected')
                <span style="color: red;">Rejected</span>
            @else
                <span style="color: gray;">{{ ucfirst($grant->status) }}</span>
            @endif
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <div>
                <strong>Grant Type:</strong><br>
                {{ ucfirst(str_replace('_', ' ', $grant->grant_type ?? 'N/A')) }}
            </div>
            <div>
                <strong>Role:</strong><br>
                {{ $grant->role ?? 'N/A' }}
            </div>
            <div>
                <strong>Award Year:</strong><br>
                {{ $grant->award_year ?? 'N/A' }}
            </div>
            @if($grant->amount_omr)
            <div>
                <strong>Amount:</strong><br>
                {{ number_format($grant->amount_omr, 2) }} OMR
            </div>
            @endif
        </div>

        @if($grant->summary)
        <div style="margin-bottom: 1.5rem;">
            <strong>Summary:</strong>
            <p>{{ $grant->summary }}</p>
        </div>
        @endif

        @if($grant->status == 'draft')
        <form action="{{ route('grants.submit', $grant->id) }}" method="POST" style="margin-top: 2rem;">
            @csrf
            <button type="submit" style="background: #0056b3; color: white; padding: 0.75rem 2rem; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                Submit for Approval
            </button>
        </form>
        @endif

        <div style="margin-top: 2rem;">
            <a href="{{ route('welcome') }}" style="color: #0056b3; text-decoration: none;">← Back to Home</a>
        </div>
    </div>
</div>
@endsection
