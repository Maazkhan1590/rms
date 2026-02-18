@foreach($courses as $course)
<div class="publication-card">
    <div class="publication-card-header">
        <span class="publication-category">{{ str_replace('_', ' ', strtoupper($course->rtn_type ?? 'RTN')) }}</span>
        @if($course->status == 'approved')
            <span class="publication-status approved">Approved</span>
        @endif
    </div>
    <div class="publication-card-body">
        <h3 class="publication-card-title">
            <a href="{{ route('rtn-course-details.show', $course->id) }}">
                {{ $course->course_code }} - {{ Str::limit($course->course_name, 100) }}
            </a>
        </h3>
        <div class="publication-card-meta">
            @if($course->year)
                <span><i class="fas fa-calendar-alt"></i> {{ $course->year }}</span>
            @endif
            @if($course->submitter)
                <span><i class="fas fa-user"></i> {{ $course->submitter->name }}</span>
            @endif
        </div>
    </div>
    <div class="publication-card-footer">
        <a href="{{ route('rtn-course-details.show', $course->id) }}" class="btn btn-outline btn-sm">
            View Details <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
@endforeach
