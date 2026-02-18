@foreach($partnerships as $partnership)
<div class="publication-card">
    <div class="publication-card-header">
        <span class="publication-category">{{ strtoupper($partnership->type ?? 'PARTNERSHIP') }}</span>
        @if($partnership->status == 'approved')
            <span class="publication-status approved">Approved</span>
        @endif
    </div>
    <div class="publication-card-body">
        <h3 class="publication-card-title">
            <a href="{{ route('partnerships.show', $partnership->id) }}">
                {{ $partnership->partner_organization }}
            </a>
        </h3>
        @if($partnership->scope_theme)
            <p class="publication-card-abstract">
                {{ Str::limit(strip_tags($partnership->scope_theme), 150) }}
            </p>
        @endif
        <div class="publication-card-meta">
            @if($partnership->date_signed)
                <span><i class="fas fa-calendar"></i> {{ $partnership->date_signed->format('M d, Y') }}</span>
            @endif
            @if($partnership->year)
                <span><i class="fas fa-calendar-alt"></i> {{ $partnership->year }}</span>
            @endif
            @if($partnership->leadStaff)
                <span><i class="fas fa-user"></i> {{ $partnership->leadStaff->name }}</span>
            @endif
        </div>
    </div>
    <div class="publication-card-footer">
        <a href="{{ route('partnerships.show', $partnership->id) }}" class="btn btn-outline btn-sm">
            View Details <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
@endforeach
