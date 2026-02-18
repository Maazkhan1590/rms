@foreach($awards as $award)
<div class="publication-card">
    <div class="publication-card-header">
        <span class="publication-category">{{ strtoupper($award->award_type ?? 'AWARD') }}</span>
        @if($award->status == 'approved')
            <span class="publication-status approved">Approved</span>
        @endif
    </div>
    <div class="publication-card-body">
        <h3 class="publication-card-title">
            <a href="{{ route('awards.show', $award->id) }}">
                {{ $award->award_name }}
            </a>
        </h3>
        @if($award->awarding_organization)
            <p class="publication-card-abstract">
                <strong>Awarding Organization:</strong> {{ $award->awarding_organization }}
            </p>
        @endif
        @if($award->description)
            <p class="publication-card-abstract">
                {{ Str::limit(strip_tags($award->description), 150) }}
            </p>
        @endif
        <div class="publication-card-meta">
            @if($award->award_date)
                <span><i class="fas fa-calendar"></i> {{ $award->award_date->format('M d, Y') }}</span>
            @endif
            @if($award->year)
                <span><i class="fas fa-calendar-alt"></i> {{ $award->year }}</span>
            @endif
            @if($award->category)
                <span><i class="fas fa-tag"></i> {{ $award->category }}</span>
            @endif
            @if($award->submitter)
                <span><i class="fas fa-user"></i> {{ $award->submitter->name }}</span>
            @endif
        </div>
    </div>
    <div class="publication-card-footer">
        <a href="{{ route('awards.show', $award->id) }}" class="btn btn-outline btn-sm">
            View Details <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
@endforeach
