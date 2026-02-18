@foreach($involvements as $involvement)
<div class="publication-card">
    <div class="publication-card-header">
        <span class="publication-category">{{ strtoupper($involvement->category ?? 'INVOLVEMENT') }}</span>
        @if($involvement->status == 'approved')
            <span class="publication-status approved">Approved</span>
        @endif
    </div>
    <div class="publication-card-body">
        <h3 class="publication-card-title">
            <a href="{{ route('student-involvements.show', $involvement->id) }}">
                {{ ucfirst($involvement->category) }}
            </a>
        </h3>
        <p class="publication-card-abstract">
            <strong>Count:</strong> {{ $involvement->count }} student(s)
        </p>
        <div class="publication-card-meta">
            @if($involvement->date)
                <span><i class="fas fa-calendar"></i> {{ $involvement->date->format('M d, Y') }}</span>
            @endif
            @if($involvement->academic_year)
                <span><i class="fas fa-calendar-alt"></i> {{ $involvement->academic_year }}</span>
            @endif
            @if($involvement->submitter)
                <span><i class="fas fa-user"></i> {{ $involvement->submitter->name }}</span>
            @endif
        </div>
    </div>
    <div class="publication-card-footer">
        <a href="{{ route('student-involvements.show', $involvement->id) }}" class="btn btn-outline btn-sm">
            View Details <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
@endforeach
