@foreach($fundings as $funding)
<div class="publication-card">
    <div class="publication-card-header">
        <span class="publication-category">INTERNAL FUNDING</span>
        @if($funding->status == 'approved')
            <span class="publication-status approved">Approved</span>
        @endif
    </div>
    <div class="publication-card-body">
        <h3 class="publication-card-title">
            <a href="{{ route('internal-fundings.show', $funding->id) }}">
                {{ Str::limit($funding->project_title, 150) }}
            </a>
        </h3>
        @if($funding->funding_source)
            <p class="publication-card-abstract">
                <strong>Funding Source:</strong> {{ $funding->funding_source }}
            </p>
        @endif
        <div class="publication-card-meta">
            @if($funding->amount_omr)
                <span><i class="fas fa-money-bill-wave"></i> {{ number_format($funding->amount_omr, 2) }} OMR</span>
            @endif
            @if($funding->start_date)
                <span><i class="fas fa-calendar"></i> {{ $funding->start_date->format('M d, Y') }}@if($funding->end_date) - {{ $funding->end_date->format('M d, Y') }}@endif</span>
            @endif
            @if($funding->year)
                <span><i class="fas fa-calendar-alt"></i> {{ $funding->year }}</span>
            @endif
            @if($funding->submitter)
                <span><i class="fas fa-user"></i> {{ $funding->submitter->name }}</span>
            @endif
        </div>
    </div>
    <div class="publication-card-footer">
        <a href="{{ route('internal-fundings.show', $funding->id) }}" class="btn btn-outline btn-sm">
            View Details <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
@endforeach
