@php
    use Illuminate\Support\Str;
@endphp
@foreach($bonusRecognitions as $bonus)
<div class="publication-card" data-bonus-id="{{ $bonus->id }}" onclick="window.location.href='{{ route('bonus-recognitions.show', $bonus->id) }}'">
    <div class="publication-header">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
            <span class="publication-category" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.25) 0%, rgba(217, 119, 6, 0.2) 100%); border-color: rgba(245, 158, 11, 0.4);">{{ ucfirst(str_replace('_', ' ', $bonus->recognition_type)) }}</span>
        </div>
        <h3 class="publication-title">{{ $bonus->title }}</h3>
        <div class="publication-authors">
            <i class="fas fa-user-edit"></i>
            {{ $bonus->user->name ?? 'Anonymous' }}
        </div>
    </div>
    <div class="publication-body">
        <p class="publication-abstract">
            @if($bonus->description)
                {{ Str::limit(strip_tags($bonus->description ?? ''), 200) }}
            @elseif($bonus->organization)
                Organization: {{ $bonus->organization }}
            @else
                Recognition details available on full page view.
            @endif
        </p>
    </div>
    <div class="publication-footer">
        <span class="publication-category">{{ strtoupper(str_replace('_', ' ', $bonus->recognition_type ?? 'Recognition')) }}</span>
        <div class="publication-meta">
            <div class="publication-date">
                <i class="far fa-calendar"></i>
                {{ $bonus->year ?? 'N/A' }}
            </div>
            @if($bonus->points)
            <div style="margin-left: 1rem; color: #f59e0b; font-weight: 600;">
                <i class="fas fa-star"></i>
                {{ number_format($bonus->points, 2) }} Points
            </div>
            @endif
        </div>
        <div class="publication-actions">
            <button class="btn btn-outline view-bonus-btn" data-id="{{ $bonus->id }}" onclick="event.stopPropagation();">
                <i class="fas fa-eye"></i> View Details
            </button>
        </div>
    </div>
</div>
@endforeach
