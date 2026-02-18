@foreach($commercializations as $commercialization)
<div class="publication-card">
    <div class="publication-card-header">
        <span class="publication-category">{{ strtoupper($commercialization->type ?? 'COMMERCIALIZATION') }}</span>
        @if($commercialization->status == 'approved')
            <span class="publication-status approved">Approved</span>
        @endif
    </div>
    <div class="publication-card-body">
        <h3 class="publication-card-title">
            <a href="{{ route('commercializations.show', $commercialization->id) }}">
                {{ $commercialization->product_service_name }}
            </a>
        </h3>
        @if($commercialization->stage)
            <p class="publication-card-abstract">
                <strong>Stage:</strong> {{ ucfirst($commercialization->stage) }}
            </p>
        @endif
        <div class="publication-card-meta">
            @if($commercialization->launch_date)
                <span><i class="fas fa-calendar"></i> {{ $commercialization->launch_date->format('M d, Y') }}</span>
            @endif
            @if($commercialization->year)
                <span><i class="fas fa-calendar-alt"></i> {{ $commercialization->year }}</span>
            @endif
            @if($commercialization->revenue_omr)
                <span><i class="fas fa-dollar-sign"></i> {{ number_format($commercialization->revenue_omr, 2) }} OMR</span>
            @endif
            @if($commercialization->ownerTeam)
                <span><i class="fas fa-user"></i> {{ $commercialization->ownerTeam->name }}</span>
            @endif
        </div>
    </div>
    <div class="publication-card-footer">
        <a href="{{ route('commercializations.show', $commercialization->id) }}" class="btn btn-outline btn-sm">
            View Details <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
@endforeach
