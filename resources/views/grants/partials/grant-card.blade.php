@php
    use Illuminate\Support\Str;
@endphp
@foreach($grants as $grant)
<div class="publication-card" data-grant-id="{{ $grant->id }}" onclick="window.location.href='{{ route('grants.show', $grant->id) }}'">
    <div class="publication-header">
        <h3 class="publication-title">{{ $grant->title }}</h3>
        <div class="publication-authors">
            <i class="fas fa-user-edit"></i>
            {{ $grant->submitter->name ?? 'Anonymous' }}
        </div>
    </div>
    <div class="publication-body">
        <p class="publication-abstract">
            {{ Str::limit(strip_tags($grant->summary ?? ''), 200) }}
        </p>
    </div>
    <div class="publication-footer">
        <span class="publication-category">{{ strtoupper(str_replace('_', ' ', $grant->grant_type ?? 'Grant')) }}</span>
        <div class="publication-meta">
            <div class="publication-date">
                <i class="far fa-calendar"></i>
                {{ $grant->award_year ?? 'N/A' }}
            </div>
            @if($grant->amount_omr)
            <div style="margin-left: 1rem; color: #10b981; font-weight: 600;">
                <i class="fas fa-money-bill-wave"></i>
                {{ number_format($grant->amount_omr, 2) }} OMR
            </div>
            @endif
        </div>
        <div class="publication-actions">
            <button class="btn btn-outline view-grant-btn" data-id="{{ $grant->id }}" onclick="event.stopPropagation();">
                <i class="fas fa-eye"></i> View Details
            </button>
        </div>
    </div>
</div>
@endforeach
