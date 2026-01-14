@extends('layouts.base')

@section('title', 'RTN Submission Details - RMS')

@section('content')
<div style="max-width: 1000px; margin: 2rem auto; padding: 0 2rem;">
    <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h1 style="margin-bottom: 1rem;">{{ $rtn->title }}</h1>
        
        <div style="margin-bottom: 1.5rem;">
            <strong>Status:</strong> 
            @if($rtn->status == 'approved')
                <span style="color: green;">Approved</span>
            @elseif($rtn->status == 'pending' || $rtn->status == 'submitted')
                <span style="color: orange;">Pending</span>
            @elseif($rtn->status == 'rejected')
                <span style="color: red;">Rejected</span>
            @else
                <span style="color: gray;">{{ ucfirst($rtn->status) }}</span>
            @endif
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <div>
                <strong>RTN Type:</strong><br>
                {{ $rtn->rtn_type ?? 'N/A' }}
            </div>
            <div>
                <strong>Year:</strong><br>
                {{ $rtn->year ?? 'N/A' }}
            </div>
        </div>

        @if($rtn->description)
        <div style="margin-bottom: 1.5rem;">
            <strong>Description:</strong>
            <p>{{ $rtn->description }}</p>
        </div>
        @endif

        @if($rtn->evidence_description)
        <div style="margin-bottom: 1.5rem;">
            <strong>Evidence Description:</strong>
            <p>{{ $rtn->evidence_description }}</p>
        </div>
        @endif

        @if($rtn->status == 'draft')
        <form action="{{ route('rtn-submissions.submit', $rtn->id) }}" method="POST" style="margin-top: 2rem;">
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
