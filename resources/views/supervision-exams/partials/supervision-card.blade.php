@foreach($supervisions as $supervision)
<div class="publication-card">
    <div class="publication-card-header">
        <span class="publication-category">{{ strtoupper($supervision->role ?? 'SUPERVISION') }}</span>
        @if($supervision->workflow_status == 'approved')
            <span class="publication-status approved">Approved</span>
        @endif
    </div>
    <div class="publication-card-body">
        <h3 class="publication-card-title">
            <a href="{{ route('supervision-exams.show', $supervision->id) }}">
                {{ $supervision->student_name }}
            </a>
        </h3>
        @if($supervision->thesis_title)
            <p class="publication-card-abstract">
                <strong>Thesis:</strong> {{ Str::limit($supervision->thesis_title, 150) }}
            </p>
        @endif
        <div class="publication-card-meta">
            @if($supervision->degree)
                <span><i class="fas fa-graduation-cap"></i> {{ ucfirst($supervision->degree) }}</span>
            @endif
            @if($supervision->university)
                <span><i class="fas fa-university"></i> {{ $supervision->university }}</span>
            @endif
            @if($supervision->start_year)
                <span><i class="fas fa-calendar"></i> {{ $supervision->start_year }}@if($supervision->end_year) - {{ $supervision->end_year }}@endif</span>
            @endif
            @if($supervision->submitter)
                <span><i class="fas fa-user"></i> {{ $supervision->submitter->name }}</span>
            @endif
        </div>
    </div>
    <div class="publication-card-footer">
        <a href="{{ route('supervision-exams.show', $supervision->id) }}" class="btn btn-outline btn-sm">
            View Details <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
@endforeach
