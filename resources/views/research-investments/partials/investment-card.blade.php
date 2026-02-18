@foreach($investments as $investment)
<div class="publication-card">
    <div class="publication-card-header">
        <span class="publication-category">{{ strtoupper($investment->category ?? 'INVESTMENT') }}</span>
        @if($investment->status == 'approved')
            <span class="publication-status approved">Approved</span>
        @endif
    </div>
    <div class="publication-card-body">
        <h3 class="publication-card-title">
            <a href="{{ route('research-investments.show', $investment->id) }}">
                {{ $investment->item }}
            </a>
        </h3>
        @if($investment->funding_source)
            <p class="publication-card-abstract">
                <strong>Funding Source:</strong> {{ $investment->funding_source }}
            </p>
        @endif
        <div class="publication-card-meta">
            @if($investment->date)
                <span><i class="fas fa-calendar"></i> {{ $investment->date->format('M d, Y') }}</span>
            @endif
            @if($investment->year)
                <span><i class="fas fa-calendar-alt"></i> {{ $investment->year }}</span>
            @endif
            @if($investment->amount_omr)
                <span><i class="fas fa-dollar-sign"></i> {{ number_format($investment->amount_omr, 2) }} OMR</span>
            @endif
            @if($investment->submitter)
                <span><i class="fas fa-user"></i> {{ $investment->submitter->name }}</span>
            @endif
        </div>
    </div>
    <div class="publication-card-footer">
        <a href="{{ route('research-investments.show', $investment->id) }}" class="btn btn-outline btn-sm">
            View Details <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
@endforeach
