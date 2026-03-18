@php
    $historyEntries = ($workflow && $workflow->history)
        ? $workflow->history->sortBy('created_at')->values()
        : collect();

    $hasDraftEntry = $historyEntries->contains(function ($entry) {
        return isset($entry->new_status) && $entry->new_status === 'draft';
    });

    if (!$hasDraftEntry && $workflow && $workflow->created_at) {
        $draftEntry = new \stdClass();
        $draftEntry->action = 'submitted';
        $draftEntry->new_status = 'draft';
        $draftEntry->previous_status = null;
        $draftEntry->comments = 'Draft created';
        $draftEntry->created_at = $workflow->created_at;
        $draftEntry->performer = $workflow->submitter;

        $historyEntries = $historyEntries
            ->prepend($draftEntry)
            ->sortBy(function ($entry) {
                if (is_object($entry->created_at) && method_exists($entry->created_at, 'getTimestamp')) {
                    return $entry->created_at->getTimestamp();
                }
                if (is_string($entry->created_at)) {
                    return strtotime($entry->created_at);
                }
                return 0;
            })
            ->values();
    }
@endphp

@if($historyEntries->count() > 0)
    <h6 class="mt-4 mb-3">
        <span class="material-icons-outlined" style="font-size:20px;vertical-align:middle;color:#4f46e5;">history</span>
        <span style="vertical-align: middle;font-weight:600;">Approval Timeline</span>
    </h6>

    <div class="approval-timeline">
        @foreach($historyEntries as $history)
            @php
                $action = isset($history->action) ? $history->action : 'submitted';
                $newStatus = isset($history->new_status) ? $history->new_status : null;
                $previousStatus = isset($history->previous_status) ? $history->previous_status : null;

                $isApproved = $action === 'approved';
                $isRejected = $action === 'rejected';
                $isDraft = $newStatus === 'draft';

                if ($isApproved) {
                    $iconColor = '#22c55e';
                    $iconBg = '#f0fdf4';
                    $icon = 'check_circle';
                } elseif ($isRejected) {
                    $iconColor = '#ef4444';
                    $iconBg = '#fef2f2';
                    $icon = 'cancel';
                } elseif ($isDraft) {
                    $iconColor = '#f59e0b';
                    $iconBg = '#fef3c7';
                    $icon = 'drafts';
                } else {
                    $iconColor = '#3b82f6';
                    $iconBg = '#eff6ff';
                    $icon = 'pending';
                }

                $performerName = 'N/A';
                if (isset($history->performer) && is_object($history->performer) && isset($history->performer->name)) {
                    $performerName = $history->performer->name;
                } elseif (isset($history->performer) && is_string($history->performer)) {
                    $performerName = $history->performer;
                }

                $formattedTime = 'N/A';
                if (isset($history->created_at) && is_object($history->created_at) && method_exists($history->created_at, 'format')) {
                    $formattedTime = $history->created_at->format('M d, Y H:i');
                } elseif (isset($history->created_at) && is_string($history->created_at)) {
                    $formattedTime = date('M d, Y H:i', strtotime($history->created_at));
                }
            @endphp

            <div class="timeline-item" style="position:relative;padding-left:48px;padding-bottom:20px;">
                @if(!$loop->last)
                    <div style="position:absolute;left:18px;top:36px;bottom:-6px;width:2px;background:linear-gradient(180deg, {{ $iconColor }} 0%, #e5e7eb 100%);"></div>
                @endif

                <div style="position:absolute;left:0;top:0;width:36px;height:36px;border-radius:50%;background:{{ $iconBg }};border:2px solid {{ $iconColor }};display:flex;align-items:center;justify-content:center;z-index:1;">
                    <span class="material-icons-outlined" style="font-size:18px;color:{{ $iconColor }};">{{ $icon }}</span>
                </div>

                <div style="background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:12px;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;">
                        <div>
                            <span class="badge" style="background:{{ $iconColor }};color:#fff;font-size:12px;">{{ ucfirst($action) }}</span>
                            @if($previousStatus || $newStatus)
                                <span style="font-size:12px;color:#6b7280;margin-left:6px;">
                                    {{ $previousStatus ? ucwords(str_replace('_', ' ', $previousStatus)) : 'N/A' }}
                                    @if($newStatus) → {{ ucwords(str_replace('_', ' ', $newStatus)) }} @endif
                                </span>
                            @endif
                        </div>
                        <span style="font-size:12px;color:#6b7280;white-space:nowrap;">{{ $formattedTime }}</span>
                    </div>

                    <div style="margin-top:8px;font-size:14px;color:#111827;">
                        <strong>{{ $performerName }}</strong>
                    </div>

                    @if(isset($history->comments) && $history->comments)
                        <div style="margin-top:8px;padding:8px;background:#f9fafb;border-left:3px solid {{ $iconColor }};border-radius:4px;">
                            <div style="font-size:11px;color:#6b7280;text-transform:uppercase;font-weight:600;">Comments</div>
                            <div style="font-size:13px;color:#374151;">{{ $history->comments }}</div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif
