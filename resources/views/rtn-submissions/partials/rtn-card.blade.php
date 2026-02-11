@php
    use Illuminate\Support\Str;
@endphp
@foreach($rtnSubmissions as $rtn)
<div class="publication-card" data-rtn-id="{{ $rtn->id }}" onclick="window.location.href='{{ route('rtn-submissions.show', $rtn->id) }}'">
    <div class="publication-header">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
            <span class="publication-category" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.25) 0%, rgba(124, 58, 237, 0.2) 100%); border-color: rgba(139, 92, 246, 0.4);">{{ $rtn->rtn_type }}</span>
        </div>
        <h3 class="publication-title">{{ $rtn->title }}</h3>
        <div class="publication-authors">
            <i class="fas fa-user-edit"></i>
            {{ $rtn->user->name ?? 'Anonymous' }}
        </div>
    </div>
    <div class="publication-body">
        <p class="publication-abstract">
            {{ Str::limit(strip_tags($rtn->description ?? ''), 200) }}
        </p>
    </div>
    <div class="publication-footer">
        <span class="publication-category">{{ strtoupper($rtn->rtn_type ?? 'RTN') }}</span>
        <div class="publication-meta">
            <div class="publication-date">
                <i class="far fa-calendar"></i>
                {{ $rtn->year ?? 'N/A' }}
            </div>
            @if($rtn->amount_omr)
            <div style="margin-left: 1rem; color: #8b5cf6; font-weight: 600;">
                <i class="fas fa-money-bill-wave"></i>
                {{ number_format($rtn->amount_omr, 2) }} OMR
            </div>
            @endif
        </div>
        <div class="publication-actions">
            <button class="btn btn-outline view-rtn-btn" data-id="{{ $rtn->id }}" onclick="event.stopPropagation();">
                <i class="fas fa-eye"></i> View Details
            </button>
        </div>
    </div>
</div>
@endforeach
