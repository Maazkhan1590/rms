@foreach($facultyMembers as $member)
@php
    // Use unique publications count to avoid double counting
    $totalPublications = $member->unique_publications_count ?? 0;
    $totalGrants = $member->grants_count ?? 0;
    $totalRtn = $member->rtn_submissions_count ?? 0;
    $totalRecognitions = $member->bonus_recognitions_count ?? 0;
@endphp
<div class="faculty-member-card" style="background: white; border-radius: 10px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: all 0.2s ease; cursor: pointer; border: 1px solid #e5e7eb; height: 100%; display: flex; flex-direction: column;" onclick="window.location.href='{{ route('faculty-members.show', $member->id) }}'" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'; this.style.borderColor='#3b82f6'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'; this.style.borderColor='#e5e7eb'">
    <div class="member-header" style="text-align: center; margin-bottom: 1.25rem;">
        <div class="member-avatar" style="width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #8b5cf6); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; font-size: 1.75rem; color: white; font-weight: 700; box-shadow: 0 2px 8px rgba(59,130,246,0.25);">
            {{ strtoupper(substr($member->name, 0, 1)) }}
        </div>
        <h3 style="font-size: 1.15rem; font-weight: 600; margin: 0 0 0.25rem 0; color: #111827; line-height: 1.4;">{{ $member->name }}</h3>
        @if($member->designation)
            <p style="color: #6b7280; font-size: 0.875rem; margin: 0 0 0.2rem 0; font-weight: 500;">{{ $member->designation }}</p>
        @endif
        @if($member->college)
            <p style="color: #9ca3af; font-size: 0.8rem; margin: 0 0 0.1rem 0;">{{ $member->college->name }}</p>
        @endif
        @if($member->department)
            <p style="color: #9ca3af; font-size: 0.8rem; margin: 0;">{{ $member->department->name }}</p>
        @endif
    </div>
    <div class="member-stats" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; padding-top: 1.25rem; border-top: 1px solid #e5e7eb; margin-top: auto;">
        <div style="text-align: center; padding: 0.625rem 0.5rem; background: #f8f9fa; border-radius: 6px;">
            <div style="font-size: 1.5rem; font-weight: 700; color: #3b82f6; margin-bottom: 0.15rem; line-height: 1.2;">{{ $totalPublications }}</div>
            <div style="font-size: 0.75rem; color: #6b7280; font-weight: 500;">Publications</div>
        </div>
        <div style="text-align: center; padding: 0.625rem 0.5rem; background: #f8f9fa; border-radius: 6px;">
            <div style="font-size: 1.5rem; font-weight: 700; color: #10b981; margin-bottom: 0.15rem; line-height: 1.2;">{{ $totalGrants }}</div>
            <div style="font-size: 0.75rem; color: #6b7280; font-weight: 500;">Grants</div>
        </div>
        <div style="text-align: center; padding: 0.625rem 0.5rem; background: #f8f9fa; border-radius: 6px;">
            <div style="font-size: 1.5rem; font-weight: 700; color: #8b5cf6; margin-bottom: 0.15rem; line-height: 1.2;">{{ $totalRtn }}</div>
            <div style="font-size: 0.75rem; color: #6b7280; font-weight: 500;">RTN</div>
        </div>
        <div style="text-align: center; padding: 0.625rem 0.5rem; background: #f8f9fa; border-radius: 6px;">
            <div style="font-size: 1.5rem; font-weight: 700; color: #f59e0b; margin-bottom: 0.15rem; line-height: 1.2;">{{ $totalRecognitions }}</div>
            <div style="font-size: 0.75rem; color: #6b7280; font-weight: 500;">Recognitions</div>
        </div>
    </div>
</div>
@endforeach
