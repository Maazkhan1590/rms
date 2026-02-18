@foreach($consultancies as $consultancy)
<div class="publication-card">
    <div class="publication-card-header">
        <span class="publication-category">{{ strtoupper($consultancy->income_type ?? 'CONSULTANCY') }}</span>
        @if($consultancy->status == 'approved')
            <span class="publication-status approved">Approved</span>
        @endif
    </div>
    <div class="publication-card-body">
        <h3 class="publication-card-title">
            <a href="{{ route('consultancies.show', $consultancy->id) }}">
                {{ $consultancy->project_consultancy_name }}
            </a>
        </h3>
        @if($consultancy->client_sponsor)
            <p class="publication-card-abstract">
                <strong>Client/Sponsor:</strong> {{ $consultancy->client_sponsor }}
            </p>
        @endif
        <div class="publication-card-meta">
            @if($consultancy->start_date)
                <span><i class="fas fa-calendar"></i> {{ $consultancy->start_date->format('M d, Y') }}</span>
            @endif
            @if($consultancy->year)
                <span><i class="fas fa-calendar-alt"></i> {{ $consultancy->year }}</span>
            @endif
            @if($consultancy->amount_omr)
                <span><i class="fas fa-dollar-sign"></i> {{ number_format($consultancy->amount_omr, 2) }} OMR</span>
            @endif
            @if($consultancy->submitter)
                <span><i class="fas fa-user"></i> {{ $consultancy->submitter->name }}</span>
            @endif
        </div>
    </div>
    <div class="publication-card-footer">
        <a href="{{ route('consultancies.show', $consultancy->id) }}" class="btn btn-outline btn-sm">
            View Details <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
@endforeach
