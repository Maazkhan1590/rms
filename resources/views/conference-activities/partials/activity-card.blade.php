@foreach($activities as $activity)
<div class="publication-card">
    <div class="publication-card-header">
        <span class="publication-category">{{ strtoupper($activity->activity_type ?? 'CONFERENCE') }}</span>
        @if($activity->status == 'approved')
            <span class="publication-status approved">Approved</span>
        @endif
    </div>
    <div class="publication-card-body">
        <h3 class="publication-card-title">
            <a href="{{ route('conference-activities.show', $activity->id) }}">
                {{ $activity->conference }}
            </a>
        </h3>
        @if($activity->country)
            <p class="publication-card-abstract">
                <strong>Country:</strong> {{ $activity->country }}
            </p>
        @endif
        <div class="publication-card-meta">
            @if($activity->date)
                <span><i class="fas fa-calendar"></i> {{ $activity->date->format('M d, Y') }}</span>
            @endif
            @if($activity->year)
                <span><i class="fas fa-calendar-alt"></i> {{ $activity->year }}</span>
            @endif
            @if($activity->submitter)
                <span><i class="fas fa-user"></i> {{ $activity->submitter->name }}</span>
            @endif
        </div>
    </div>
    <div class="publication-card-footer">
        <a href="{{ route('conference-activities.show', $activity->id) }}" class="btn btn-outline btn-sm">
            View Details <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
@endforeach
