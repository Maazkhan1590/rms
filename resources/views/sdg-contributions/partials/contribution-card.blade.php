@foreach($contributions as $contribution)
<div class="publication-card">
    <div class="publication-card-header">
        <span class="publication-category">SDG {{ $contribution->sdg }}</span>
        @if($contribution->status == 'approved')
            <span class="publication-status approved">Approved</span>
        @endif
    </div>
    <div class="publication-card-body">
        <h3 class="publication-card-title">
            <a href="{{ route('sdg-contributions.show', $contribution->id) }}">
                {{ Str::limit($contribution->title, 150) }}
            </a>
        </h3>
        @if($contribution->type)
            <p class="publication-card-abstract">
                <strong>Type:</strong> {{ ucfirst($contribution->type) }}
            </p>
        @endif
        <div class="publication-card-meta">
            @if($contribution->date)
                <span><i class="fas fa-calendar"></i> {{ $contribution->date->format('M d, Y') }}</span>
            @endif
            @if($contribution->year)
                <span><i class="fas fa-calendar-alt"></i> {{ $contribution->year }}</span>
            @endif
            @if($contribution->submitter)
                <span><i class="fas fa-user"></i> {{ $contribution->submitter->name }}</span>
            @endif
        </div>
    </div>
    <div class="publication-card-footer">
        <a href="{{ route('sdg-contributions.show', $contribution->id) }}" class="btn btn-outline btn-sm">
            View Details <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
@endforeach
