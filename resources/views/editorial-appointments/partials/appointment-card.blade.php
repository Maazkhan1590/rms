@foreach($appointments as $appointment)
<div class="publication-card">
    <div class="publication-card-header">
        <span class="publication-category">{{ strtoupper($appointment->role ?? 'EDITORIAL') }}</span>
        @if($appointment->status == 'approved')
            <span class="publication-status approved">Approved</span>
        @endif
    </div>
    <div class="publication-card-body">
        <h3 class="publication-card-title">
            <a href="{{ route('editorial-appointments.show', $appointment->id) }}">
                {{ $appointment->journal_conference }}
            </a>
        </h3>
        <div class="publication-card-meta">
            @if($appointment->start_date)
                <span><i class="fas fa-calendar"></i> {{ $appointment->start_date->format('M d, Y') }}@if($appointment->end_date) - {{ $appointment->end_date->format('M d, Y') }}@endif</span>
            @endif
            @if($appointment->year)
                <span><i class="fas fa-calendar-alt"></i> {{ $appointment->year }}</span>
            @endif
            @if($appointment->submitter)
                <span><i class="fas fa-user"></i> {{ $appointment->submitter->name }}</span>
            @endif
        </div>
    </div>
    <div class="publication-card-footer">
        <a href="{{ route('editorial-appointments.show', $appointment->id) }}" class="btn btn-outline btn-sm">
            View Details <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
@endforeach
