@extends('layouts.base')

@section('title', 'Bonus Recognition Details - RMS')

@section('content')
<div style="max-width: 1000px; margin: 2rem auto; padding: 0 2rem;">
    <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h1 style="margin-bottom: 1rem;">{{ $bonus->title }}</h1>
        
        <div style="margin-bottom: 1.5rem;">
            <strong>Status:</strong> 
            @if($bonus->status == 'approved')
                <span style="color: green;">Approved</span>
            @elseif($bonus->status == 'pending' || $bonus->status == 'submitted')
                <span style="color: orange;">Pending</span>
            @elseif($bonus->status == 'rejected')
                <span style="color: red;">Rejected</span>
            @else
                <span style="color: gray;">{{ ucfirst($bonus->status) }}</span>
            @endif
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <div>
                <strong>Recognition Type:</strong><br>
                {{ ucfirst(str_replace('_', ' ', $bonus->recognition_type ?? 'N/A')) }}
            </div>
            <div>
                <strong>Year:</strong><br>
                {{ $bonus->year ?? 'N/A' }}
            </div>
            @if($bonus->organization)
            <div>
                <strong>Organization:</strong><br>
                {{ $bonus->organization }}
            </div>
            @endif
        </div>

        @if($bonus->description)
        <div style="margin-bottom: 1.5rem;">
            <strong>Description:</strong>
            <p>{{ $bonus->description }}</p>
        </div>
        @endif

        @if($bonus->status == 'draft')
        <form action="{{ route('bonus-recognitions.submit', $bonus->id) }}" method="POST" style="margin-top: 2rem;">
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
