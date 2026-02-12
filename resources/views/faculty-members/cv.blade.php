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
            padding: 2px 7px;
            font-size: 7pt;
            font-weight: bold;
            border-radius: 3px;
            color: white;
            text-transform: uppercase;
        }

        .status-approved {
            background-color: #059669;
        }

        .status-submitted {
            background-color: #2563eb;
        }

        .status-draft {
            background-color: #64748b;
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

                <span class="status-badge status-{{ $publication->status === 'approved' ? 'approved' : ($publication->status === 'submitted' ? 'submitted' : 'draft') }}">
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
                <span class="status-badge status-{{ $grant->status === 'approved' ? 'approved' : ($grant->status === 'submitted' ? 'submitted' : 'draft') }}">
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
                <span class="status-badge status-{{ $rtn->status === 'approved' ? 'approved' : ($rtn->status === 'submitted' ? 'submitted' : 'draft') }}">
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
                <span class="status-badge status-{{ $bonus->status === 'approved' ? 'approved' : ($bonus->status === 'submitted' ? 'submitted' : 'draft') }}">
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

</div>

</body>
</html>
