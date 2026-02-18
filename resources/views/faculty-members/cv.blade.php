<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ $user->name }} – Curriculum Vitae</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9.5pt;
            line-height: 1.5;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }

        .cv-container {
            max-width: 100%;
            margin: 0 auto;
            background: white;
            padding: 0;
        }

        /* Header Section */
        .header-section {
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .header-flex {
            width: 100%;
        }

        .header-left {
            float: left;
            width: 60%;
        }

        .header-right {
            float: right;
            width: 38%;
            text-align: right;
            font-size: 8.5pt;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .cv-name {
            font-size: 26pt;
            font-weight: bold;
            color: #111827;
            margin: 0 0 8px 0;
            letter-spacing: 0.3px;
        }

        .cv-subtitle {
            font-size: 13pt;
            color: #1e40af;
            margin: 4px 0;
            font-weight: 600;
        }

        .cv-institution {
            font-size: 11pt;
            color: #4b5563;
            margin: 3px 0;
        }

        .contact-line {
            margin: 3px 0;
            color: #4b5563;
            font-size: 8.5pt;
            line-height: 1.5;
        }

        .contact-icon {
            margin-right: 5px;
            font-weight: bold;
            font-size: 13pt;
            font-family: 'DejaVu Sans', sans-serif;
            display: inline-block;
        }

        .contact-icon.email {
            color: #d97706;
        }

        .contact-icon.phone {
            color: #059669;
        }

        /* Section Titles */
        .section-title {
            font-size: 13pt;
            font-weight: 600;
            color: #111827;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 6px;
            margin: 20px 0 12px 0;
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            font-size: 9pt;
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 10px 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f3f4f6;
            font-weight: 600;
            color: #111827;
            font-size: 8.5pt;
        }

        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .profile-text {
            font-size: 10pt;
            line-height: 1.6;
            color: #374151;
            margin: 12px 0;
            text-align: justify;
            page-break-inside: avoid;
        }

        .authors-col {
            font-weight: normal;
        }

        .authors-col strong {
            font-weight: bold;
            color: #1e40af;
        }

        .title-col {
            font-weight: 500;
            line-height: 1.4;
        }

        .journal-col {
            font-style: italic;
            color: #374151;
        }

        .doi-link {
            color: #2563eb;
            text-decoration: none;
            font-size: 8pt;
        }

        /* Info Sections */
        .info-row {
            margin: 8px 0;
        }

        .info-label {
            font-weight: 600;
            color: #1e40af;
            display: inline-block;
            width: 30%;
            vertical-align: top;
        }

        .info-value {
            display: inline-block;
            width: 68%;
            color: #374151;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            font-size: 7.5pt;
            font-weight: 600;
            border-radius: 50px !important;
            color: white;
            text-transform: uppercase;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .status-badge:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        }

        .status-approved {
            background-color: #059669;
            border-color: #047857;
        }

        .status-approved:hover {
            background-color: #047857;
        }

        .status-submitted {
            background-color: #2563eb;
            border-color: #1d4ed8;
        }

        .status-submitted:hover {
            background-color: #1d4ed8;
        }

        .status-draft {
            background-color: #64748b;
            border-color: #475569;
        }

        .status-draft:hover {
            background-color: #475569;
        }

        .status-rejected {
            background-color: #ef4444;
            border-color: #dc2626;
        }

        .status-rejected:hover {
            background-color: #dc2626;
        }

        /* Footer */
        .cv-footer {
            margin-top: 30px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8pt;
            color: #999999;
            page-break-inside: avoid;
        }

        .no-data {
            text-align: center;
            padding: 15px;
            background: #f9fafb;
            color: #6b7280;
            font-style: italic;
            font-size: 9pt;
            margin: 10px 0;
        }
    </style>
</head>
<body>

<div class="cv-container">

    <!-- HEADER -->
    <div class="header-section clearfix">
        <div class="header-flex clearfix">
            <div class="header-left">
                <h1 class="cv-name">{{ $user->name ?? 'N/A' }}</h1>
                <p class="cv-subtitle">{{ $user->designation ?? 'Faculty Member' }}</p>
{{--                <p class="cv-institution">{{ $user->college->name ?? 'N/A' }}</p>--}}
            </div>
            <div class="header-right">
                @if($user->email)
                <div class="contact-line"><span class="contact-icon email">&#9993;</span> {{ $user->email }}</div>
                @else
                <div class="contact-line"><span class="contact-icon email">&#9993;</span> N/A</div>
                @endif
                @if($user->phone)
                <div class="contact-line"><span class="contact-icon phone">&#9742;</span> {{ $user->phone }}</div>
                @else
                <div class="contact-line"><span class="contact-icon phone">&#9742;</span> N/A</div>
                @endif
                @if($user->employee_id)
                <div class="contact-line">ID: {{ $user->employee_id }}</div>
                @endif
                @if($user->orcid)
                <div class="contact-line">ORCID: {{ $user->orcid }}</div>
                @endif
                @if($user->google_scholar)
                <div class="contact-line">Google Scholar: Available</div>
                @endif
                @if($user->research_gate)
                <div class="contact-line">ResearchGate: Available</div>
                @endif
            </div>
        </div>
    </div>

    <!-- RESEARCH PROFILE -->
    <div class="section-title">Research Profile</div>
    <p class="profile-text">
        @if($user->h_index || $user->citation_number || $user->scopus_papers)
            {{ $user->designation ?? 'Faculty Member' }} specializing in {{ $user->department->name ?? 'research' }}.
            @if($publications->count() > 0)
                {{ $publications->count() }} publications
            @endif
            @if($user->h_index)
                (h-index {{ $user->h_index }}
                @if($user->citation_number)
                    , {{ number_format($user->citation_number) }} citations
                @endif
                )
            @endif.
            @if($user->scopus_papers)
                {{ $user->scopus_papers }} Scopus-indexed papers
                @if($user->scopus_h_index)
                    (Scopus h-index {{ $user->scopus_h_index }})
                @endif
            @endif.
            @if($grants->count() > 0)
                Secured funding for {{ $grants->count() }} research {{ $grants->count() == 1 ? 'grant' : 'grants' }}
                @if($grants->sum('amount_omr') > 0)
                    totaling OMR {{ number_format($grants->sum('amount_omr'), 2) }}
                @endif.
            @endif
        @else
            Faculty member specializing in {{ $user->department->name ?? 'research' }} at {{ $user->college->name ?? 'the institution' }}.
        @endif
    </p>

    <!-- RESEARCH METRICS -->
    <div class="section-title">Research Impact Metrics</div>
    <table style="margin-bottom: 20px;">
        <tr>
            <th style="width: 30%;">Metric</th>
            <th style="width: 70%;">Value</th>
        </tr>
        <tr>
            <td><strong>H-Index</strong></td>
            <td>{{ $user->h_index ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Total Citations</strong></td>
            <td>{{ $user->citation_number ? number_format($user->citation_number) : 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Scopus H-Index</strong></td>
            <td>{{ $user->scopus_h_index ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Scopus Citations</strong></td>
            <td>{{ $user->scopus_citation_number ? number_format($user->scopus_citation_number) : 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Scopus Indexed Papers</strong></td>
            <td>{{ $user->scopus_papers ?? 'N/A' }}</td>
        </tr>
    </table>

    <!-- PUBLICATIONS -->
    <div class="section-title">Publications ({{ $publications->count() }})</div>

    @if($publications->count() > 0)
    <table>
        <thead>
        <tr>
            <th style="width: 4%;">#</th>
            <th style="width: 18%;">Authors</th>
            <th style="width: 34%;">Title</th>
            <th style="width: 24%;">Journal • Volume • Year</th>
            <th style="width: 12%;">DOI </th>
            <th style="width: 12%;">Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($publications as $index => $publication)
        <tr>
            <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
            <td class="authors-col">
                @if($publication->primaryAuthor)
                    <strong>{{ $publication->primaryAuthor->name }}</strong>
                @else
                    <strong>{{ $user->name }}</strong>
                @endif
                et al.
            </td>
            <td class="title-col">{{ $publication->title ?? 'Untitled' }}</td>
            <td class="journal-col">
                @if($publication->journal_name)
                    {{ $publication->journal_name }}
                @elseif($publication->conference_name)
                    {{ $publication->conference_name }}
                @else
                    N/A
                @endif
                @if($publication->volume)
                    <br>Vol. {{ $publication->volume }}
                    @if($publication->issue)({{ $publication->issue }})@endif
                    @if($publication->pages), pp. {{ $publication->pages }}@endif
                @endif
                <br>{{ $publication->publication_year ?? 'N/A' }}
            </td>
            <td style="font-size: 8pt;">
                @if($publication->doi)
                    <a href="https://doi.org/{{ $publication->doi }}" class="doi-link">doi:{{ $publication->doi }}</a><br>
                @endif

            </td>
            <td style="font-size: 8pt;">

                <span class="status-badge status-{{ $publication->status === 'approved' ? 'approved' : ($publication->status === 'rejected' ? 'rejected' : ($publication->status === 'submitted' ? 'submitted' : 'draft')) }}">
                    {{ ucfirst($publication->status ?? 'draft') }}
                </span>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        No publications recorded for this faculty member.
    </div>
    @endif

    <!-- GRANTS & FUNDING -->
    <div class="section-title">Grants ({{ $grants->count() }})</div>

    @if($grants->count() > 0)
    <table>
        <thead>
        <tr>
            <th style="width: 35%;">Project / Grant Title</th>
            <th style="width: 18%;">Funding Agency</th>
            <th style="width: 12%;">Amount (OMR)</th>
            <th style="width: 12%;">Duration</th>
            <th style="width: 10%;">Role</th>
            <th style="width: 10%;">Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($grants as $grant)
        <tr>
            <td class="title-col">{{ $grant->title ?? 'Untitled Grant' }}</td>
            <td>{{ $grant->sponsor_name ?? 'N/A' }}</td>
            <td style="text-align: right; font-weight: 600;">{{ $grant->amount_omr ? number_format($grant->amount_omr, 2) : 'N/A' }}</td>
            <td>{{ $grant->award_year ?? 'N/A' }}</td>
            <td>{{ $grant->role ?? 'PI' }}</td>
            <td style="text-align: center;">
                <span class="status-badge status-{{ $grant->status === 'approved' ? 'approved' : ($grant->status === 'rejected' ? 'rejected' : ($grant->status === 'submitted' ? 'submitted' : 'draft')) }}">
                    {{ ucfirst($grant->status ?? 'draft') }}
                </span>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        No grants or funded research recorded for this faculty member.
    </div>
    @endif

    <!-- RTN ACTIVITIES -->
    <div class="section-title">RTN ({{ $rtnSubmissions->count() }})</div>

    @if($rtnSubmissions->count() > 0)
    <table>
        <thead>
        <tr>
            <th style="width: 42%;">Activity Title</th>
            <th style="width: 20%;">Type</th>
            <th style="width: 10%;">Year</th>
            <th style="width: 14%;">Research Points</th>
            <th style="width: 12%;">Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($rtnSubmissions as $rtn)
        <tr>
            <td class="title-col">{{ $rtn->title ?? 'Untitled Activity' }}</td>
            <td>{{ $rtn->rtn_type ? ucfirst(str_replace('_', ' ', $rtn->rtn_type)) : 'N/A' }}</td>
            <td style="text-align: center;">{{ $rtn->year ?? 'N/A' }}</td>
            <td style="text-align: right; font-weight: 600; color: #1e40af;">{{ $rtn->points ? number_format($rtn->points, 2) : 'N/A' }}</td>
            <td style="text-align: center;">
                <span class="status-badge status-{{ $rtn->status === 'approved' ? 'approved' : ($rtn->status === 'rejected' ? 'rejected' : ($rtn->status === 'submitted' ? 'submitted' : 'draft')) }}">
                    {{ ucfirst($rtn->status ?? 'draft') }}
                </span>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        No RTN activities recorded for this faculty member.
    </div>
    @endif

    <!-- AWARDS & HONORS -->
    <div class="section-title">Bonus Recognition ({{ $bonusRecognitions->count() }})</div>

    @if($bonusRecognitions->count() > 0)
    <table>
        <thead>
        <tr>
            <th style="width: 42%;">Award Name</th>
            <th style="width: 30%;">Organization / Body</th>
            <th style="width: 10%;">Year</th>
            <th style="width: 15%;">Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($bonusRecognitions as $bonus)
        <tr>
            <td class="title-col">{{ $bonus->title ?? 'Untitled Recognition' }}</td>
            <td>{{ $bonus->organization ?? 'N/A' }}</td>
            <td style="text-align: center;">{{ $bonus->year ?? 'N/A' }}</td>
            <td style="text-align: center;">
                <span class="status-badge status-{{ $bonus->status === 'approved' ? 'approved' : ($bonus->status === 'rejected' ? 'rejected' : ($bonus->status === 'submitted' ? 'submitted' : 'draft')) }}">
                    {{ ucfirst($bonus->status ?? 'draft') }}
                </span>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        No bonus recognition recorded for this faculty member.
    </div>
    @endif

    <!-- PARTNERSHIPS & MOUs -->
    @if(isset($partnerships) && $partnerships->count() > 0)
    <div class="section-title">Partnerships & MOUs ({{ $partnerships->count() }})</div>
    <table>
        <thead>
        <tr>
            <th style="width: 40%;">Partner/Organization</th>
            <th style="width: 25%;">Type</th>
            <th style="width: 15%;">Year</th>
            <th style="width: 20%;">Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($partnerships as $partnership)
        <tr>
            <td class="title-col">{{ $partnership->partner_name ?? 'N/A' }}</td>
            <td>{{ $partnership->mou_type ?? 'N/A' }}</td>
            <td style="text-align: center;">{{ $partnership->year ?? 'N/A' }}</td>
            <td style="text-align: center;">
                <span class="status-badge status-{{ $partnership->status === 'approved' ? 'approved' : ($partnership->status === 'rejected' ? 'rejected' : 'submitted') }}">
                    {{ ucfirst($partnership->status ?? 'draft') }}
                </span>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif

    <!-- COMMERCIALIZATIONS -->
    @if(isset($commercializations) && $commercializations->count() > 0)
    <div class="section-title">Commercializations ({{ $commercializations->count() }})</div>
    <table>
        <thead>
        <tr>
            <th style="width: 50%;">Title</th>
            <th style="width: 20%;">Type</th>
            <th style="width: 15%;">Year</th>
            <th style="width: 15%;">Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($commercializations as $commercialization)
        <tr>
            <td class="title-col">{{ $commercialization->title ?? 'N/A' }}</td>
            <td>{{ $commercialization->commercialization_type ?? 'N/A' }}</td>
            <td style="text-align: center;">{{ $commercialization->year ?? 'N/A' }}</td>
            <td style="text-align: center;">
                <span class="status-badge status-{{ $commercialization->status === 'approved' ? 'approved' : ($commercialization->status === 'rejected' ? 'rejected' : 'submitted') }}">
                    {{ ucfirst($commercialization->status ?? 'draft') }}
                </span>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif

    <!-- CONSULTANCIES -->
    @if(isset($consultancies) && $consultancies->count() > 0)
    <div class="section-title">Consultancies & Knowledge Transfer ({{ $consultancies->count() }})</div>
    <table>
        <thead>
        <tr>
            <th style="width: 40%;">Title</th>
            <th style="width: 20%;">Type</th>
            <th style="width: 15%;">Year</th>
            <th style="width: 15%;">Amount (OMR)</th>
            <th style="width: 10%;">Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($consultancies as $consultancy)
        <tr>
            <td class="title-col">{{ $consultancy->title ?? 'N/A' }}</td>
            <td>{{ $consultancy->income_type ?? 'N/A' }}</td>
            <td style="text-align: center;">{{ $consultancy->year ?? 'N/A' }}</td>
            <td style="text-align: right;">{{ $consultancy->amount_omr ? number_format($consultancy->amount_omr, 2) : 'N/A' }}</td>
            <td style="text-align: center;">
                <span class="status-badge status-{{ $consultancy->status === 'approved' ? 'approved' : ($consultancy->status === 'rejected' ? 'rejected' : 'submitted') }}">
                    {{ ucfirst($consultancy->status ?? 'draft') }}
                </span>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif

    <!-- AWARDS -->
    @if(isset($awards) && $awards->count() > 0)
    <div class="section-title">Awards ({{ $awards->count() }})</div>
    <table>
        <thead>
        <tr>
            <th style="width: 45%;">Award Name</th>
            <th style="width: 30%;">Organization</th>
            <th style="width: 15%;">Year</th>
            <th style="width: 10%;">Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($awards as $award)
        <tr>
            <td class="title-col">{{ $award->title ?? 'N/A' }}</td>
            <td>{{ $award->organization ?? 'N/A' }}</td>
            <td style="text-align: center;">{{ $award->year ?? 'N/A' }}</td>
            <td style="text-align: center;">
                <span class="status-badge status-{{ $award->status === 'approved' ? 'approved' : ($award->status === 'rejected' ? 'rejected' : 'submitted') }}">
                    {{ ucfirst($award->status ?? 'draft') }}
                </span>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif

    <!-- RESEARCH INVESTMENTS -->
    @if(isset($researchInvestments) && $researchInvestments->count() > 0)
    <div class="section-title">Research Investments ({{ $researchInvestments->count() }})</div>
    <table>
        <thead>
        <tr>
            <th style="width: 35%;">Item</th>
            <th style="width: 20%;">Category</th>
            <th style="width: 15%;">Amount (OMR)</th>
            <th style="width: 15%;">Year</th>
            <th style="width: 15%;">Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($researchInvestments as $investment)
        <tr>
            <td class="title-col">{{ $investment->item ?? 'N/A' }}</td>
            <td>{{ ucfirst(str_replace('_', ' ', $investment->category ?? 'N/A')) }}</td>
            <td style="text-align: right;">{{ $investment->amount_omr ? number_format($investment->amount_omr, 2) : 'N/A' }}</td>
            <td style="text-align: center;">{{ $investment->year ?? 'N/A' }}</td>
            <td style="text-align: center;">
                <span class="status-badge status-{{ $investment->status === 'approved' ? 'approved' : ($investment->status === 'rejected' ? 'rejected' : 'submitted') }}">
                    {{ ucfirst($investment->status ?? 'draft') }}
                </span>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif

    <!-- CONFERENCE ACTIVITIES -->
    @if(isset($conferenceActivities) && $conferenceActivities->count() > 0)
    <div class="section-title">Conference Activities ({{ $conferenceActivities->count() }})</div>
    <table>
        <thead>
        <tr>
            <th style="width: 40%;">Conference</th>
            <th style="width: 20%;">Activity Type</th>
            <th style="width: 15%;">Country</th>
            <th style="width: 15%;">Date</th>
            <th style="width: 10%;">Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($conferenceActivities as $activity)
        <tr>
            <td class="title-col">{{ $activity->conference ?? 'N/A' }}</td>
            <td>{{ ucfirst(str_replace('_', ' ', $activity->activity_type ?? 'N/A')) }}</td>
            <td>{{ $activity->country ?? 'N/A' }}</td>
            <td style="text-align: center;">{{ $activity->date ? \Carbon\Carbon::parse($activity->date)->format('M Y') : 'N/A' }}</td>
            <td style="text-align: center;">
                <span class="status-badge status-{{ $activity->status === 'approved' ? 'approved' : ($activity->status === 'rejected' ? 'rejected' : 'submitted') }}">
                    {{ ucfirst($activity->status ?? 'draft') }}
                </span>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif

    <!-- SUPERVISION & EXAMS -->
    @if(isset($supervisionExams) && $supervisionExams->count() > 0)
    <div class="section-title">Supervision & Examinations ({{ $supervisionExams->count() }})</div>
    <table>
        <thead>
        <tr>
            <th style="width: 30%;">Student Name</th>
            <th style="width: 15%;">Role</th>
            <th style="width: 15%;">Degree</th>
            <th style="width: 25%;">Thesis Title</th>
            <th style="width: 15%;">Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($supervisionExams as $supervision)
        <tr>
            <td class="title-col">{{ $supervision->student_name ?? 'N/A' }}</td>
            <td>{{ ucfirst(str_replace('_', ' ', $supervision->role ?? 'N/A')) }}</td>
            <td>{{ $supervision->degree ?? 'N/A' }}</td>
            <td>{{ Str::limit($supervision->thesis_title ?? 'N/A', 50) }}</td>
            <td style="text-align: center;">
                <span class="status-badge status-{{ $supervision->workflow_status === 'approved' ? 'approved' : ($supervision->workflow_status === 'rejected' ? 'rejected' : 'submitted') }}">
                    {{ ucfirst($supervision->workflow_status ?? 'draft') }}
                </span>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif

    <!-- EDITORIAL APPOINTMENTS -->
    @if(isset($editorialAppointments) && $editorialAppointments->count() > 0)
    <div class="section-title">Editorial Appointments ({{ $editorialAppointments->count() }})</div>
    <table>
        <thead>
        <tr>
            <th style="width: 50%;">Journal/Conference</th>
            <th style="width: 20%;">Role</th>
            <th style="width: 15%;">Year</th>
            <th style="width: 15%;">Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($editorialAppointments as $appointment)
        <tr>
            <td class="title-col">{{ $appointment->journal_conference ?? 'N/A' }}</td>
            <td>{{ $appointment->role ?? 'N/A' }}</td>
            <td style="text-align: center;">{{ $appointment->year ?? 'N/A' }}</td>
            <td style="text-align: center;">
                <span class="status-badge status-{{ $appointment->status === 'approved' ? 'approved' : ($appointment->status === 'rejected' ? 'rejected' : 'submitted') }}">
                    {{ ucfirst($appointment->status ?? 'draft') }}
                </span>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif

    <!-- STUDENT INVOLVEMENTS -->
    @if(isset($studentInvolvements) && $studentInvolvements->count() > 0)
    <div class="section-title">Student Involvements ({{ $studentInvolvements->count() }})</div>
    <table>
        <thead>
        <tr>
            <th style="width: 30%;">Category</th>
            <th style="width: 20%;">Count</th>
            <th style="width: 25%;">Academic Year</th>
            <th style="width: 25%;">Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($studentInvolvements as $involvement)
        <tr>
            <td class="title-col">{{ ucfirst(str_replace('_', ' ', $involvement->category ?? 'N/A')) }}</td>
            <td style="text-align: center;">{{ $involvement->count ?? 'N/A' }}</td>
            <td style="text-align: center;">{{ $involvement->academic_year ?? 'N/A' }}</td>
            <td style="text-align: center;">
                <span class="status-badge status-{{ $involvement->status === 'approved' ? 'approved' : ($involvement->status === 'rejected' ? 'rejected' : 'submitted') }}">
                    {{ ucfirst($involvement->status ?? 'draft') }}
                </span>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif

    <!-- RESEARCH FELLOWS -->
    @if(isset($researchFellows) && $researchFellows->count() > 0)
    <div class="section-title">Research Fellows ({{ $researchFellows->count() }})</div>
    <table>
        <thead>
        <tr>
            <th style="width: 50%;">Publication Title</th>
            <th style="width: 25%;">Journal</th>
            <th style="width: 15%;">Year</th>
            <th style="width: 10%;">Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($researchFellows as $fellow)
        <tr>
            <td class="title-col">{{ Str::limit($fellow->publication_title ?? 'N/A', 60) }}</td>
            <td>{{ Str::limit($fellow->journal ?? 'N/A', 30) }}</td>
            <td style="text-align: center;">{{ $fellow->year ?? 'N/A' }}</td>
            <td style="text-align: center;">
                <span class="status-badge status-{{ $fellow->workflow_status === 'approved' ? 'approved' : ($fellow->workflow_status === 'rejected' ? 'rejected' : 'submitted') }}">
                    {{ ucfirst($fellow->workflow_status ?? 'draft') }}
                </span>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif

    <!-- SDG CONTRIBUTIONS -->
    @if(isset($sdgContributions) && $sdgContributions->count() > 0)
    <div class="section-title">SDG Contributions ({{ $sdgContributions->count() }})</div>
    <table>
        <thead>
        <tr>
            <th style="width: 40%;">Title</th>
            <th style="width: 15%;">SDG</th>
            <th style="width: 20%;">Type</th>
            <th style="width: 15%;">Year</th>
            <th style="width: 10%;">Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($sdgContributions as $contribution)
        <tr>
            <td class="title-col">{{ Str::limit($contribution->title ?? 'N/A', 50) }}</td>
            <td style="text-align: center;">SDG {{ $contribution->sdg ?? 'N/A' }}</td>
            <td>{{ ucfirst(str_replace('_', ' ', $contribution->type ?? 'N/A')) }}</td>
            <td style="text-align: center;">{{ $contribution->year ?? 'N/A' }}</td>
            <td style="text-align: center;">
                <span class="status-badge status-{{ $contribution->status === 'approved' ? 'approved' : ($contribution->status === 'rejected' ? 'rejected' : 'submitted') }}">
                    {{ ucfirst($contribution->status ?? 'draft') }}
                </span>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif

    <!-- INTERNAL FUNDINGS -->
    @if(isset($internalFundings) && $internalFundings->count() > 0)
    <div class="section-title">Internal Fundings ({{ $internalFundings->count() }})</div>
    <table>
        <thead>
        <tr>
            <th style="width: 40%;">Project Title</th>
            <th style="width: 25%;">Funding Source</th>
            <th style="width: 15%;">Amount (OMR)</th>
            <th style="width: 20%;">Year</th>
        </tr>
        </thead>
        <tbody>
        @foreach($internalFundings as $funding)
        <tr>
            <td class="title-col">{{ Str::limit($funding->project_title ?? 'N/A', 50) }}</td>
            <td>{{ $funding->funding_source ?? 'N/A' }}</td>
            <td style="text-align: right;">{{ $funding->amount_omr ? number_format($funding->amount_omr, 2) : 'N/A' }}</td>
            <td style="text-align: center;">{{ $funding->year ?? 'N/A' }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif

    <!-- BLOCK FUNDINGS -->
    @if(isset($blockFundings) && $blockFundings->count() > 0)
    <div class="section-title">Block Fundings ({{ $blockFundings->count() }})</div>
    <table>
        <thead>
        <tr>
            <th style="width: 40%;">Project Title</th>
            <th style="width: 25%;">Funding Source</th>
            <th style="width: 15%;">Amount (OMR)</th>
            <th style="width: 20%;">Year</th>
        </tr>
        </thead>
        <tbody>
        @foreach($blockFundings as $funding)
        <tr>
            <td class="title-col">{{ Str::limit($funding->project_title ?? 'N/A', 50) }}</td>
            <td>{{ $funding->funding_source ?? 'N/A' }}</td>
            <td style="text-align: right;">{{ $funding->amount_omr ? number_format($funding->amount_omr, 2) : 'N/A' }}</td>
            <td style="text-align: center;">{{ $funding->year ?? 'N/A' }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif

    <!-- RTN COURSE DETAILS -->
    @if(isset($rtnCourseDetails) && $rtnCourseDetails->count() > 0)
    <div class="section-title">RTN Course Details ({{ $rtnCourseDetails->count() }})</div>
    <table>
        <thead>
        <tr>
            <th style="width: 20%;">Course Code</th>
            <th style="width: 40%;">Course Name</th>
            <th style="width: 20%;">RTN Type</th>
            <th style="width: 20%;">Year</th>
        </tr>
        </thead>
        <tbody>
        @foreach($rtnCourseDetails as $course)
        <tr>
            <td>{{ $course->course_code ?? 'N/A' }}</td>
            <td class="title-col">{{ Str::limit($course->course_name ?? 'N/A', 50) }}</td>
            <td>{{ str_replace('_', ' ', strtoupper($course->rtn_type ?? 'N/A')) }}</td>
            <td style="text-align: center;">{{ $course->year ?? 'N/A' }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif

</div>

</body>
</html>
