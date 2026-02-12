<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $user->name }} - Curriculum Vitae</title>
    <style>
        @page {
            margin: 2cm 1.5cm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }
        h1 {
            font-size: 24pt;
            font-weight: bold;
            color: #1a1a1a;
            margin: 0 0 5px 0;
            padding: 0;
        }
        h2 {
            font-size: 14pt;
            font-weight: bold;
            color: #2c5aa0;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #2c5aa0;
        }
        h3 {
            font-size: 11pt;
            font-weight: bold;
            color: #333;
            margin: 8px 0 4px 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2c5aa0;
        }
        .contact-info {
            text-align: center;
            font-size: 9pt;
            color: #555;
            margin: 10px 0;
        }
        .contact-info span {
            margin: 0 10px;
        }
        .section {
            margin-bottom: 20px;
        }
        .item {
            margin-bottom: 12px;
            page-break-inside: avoid;
        }
        .item-title {
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 3px;
        }
        .item-meta {
            font-size: 9pt;
            color: #666;
            margin-bottom: 4px;
        }
        .item-description {
            font-size: 9pt;
            color: #444;
            line-height: 1.3;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 8pt;
            font-weight: bold;
            border-radius: 3px;
            color: white;
        }
        .badge-approved {
            background-color: #10b981;
        }
        .badge-submitted {
            background-color: #3b82f6;
        }
        .badge-draft {
            background-color: #6b7280;
        }
        .links {
            font-size: 9pt;
            color: #2c5aa0;
            margin-top: 8px;
        }
        .links a {
            color: #2c5aa0;
            text-decoration: none;
            margin-right: 15px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #999;
            padding: 10px 0;
            border-top: 1px solid #ddd;
        }
        .page-number:after {
            content: counter(page);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th {
            background-color: #f3f4f6;
            padding: 6px;
            text-align: left;
            font-size: 9pt;
            border-bottom: 2px solid #e5e7eb;
        }
        td {
            padding: 6px;
            font-size: 9pt;
            border-bottom: 1px solid #f3f4f6;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $user->name }}</h1>
        @if($user->designation)
            <div style="font-size: 11pt; color: #555; margin: 5px 0;">{{ $user->designation }}</div>
        @endif
        @if($user->college)
            <div style="font-size: 10pt; color: #666;">{{ $user->college->name }}</div>
        @endif
        @if($user->department)
            <div style="font-size: 10pt; color: #666;">{{ $user->department->name }}</div>
        @endif
        <div class="contact-info">
            @if($user->email)
                <span>✉ {{ $user->email }}</span>
            @endif
            @if($user->phone)
                <span>☎ {{ $user->phone }}</span>
            @endif
            @if($user->employee_id)
                <span>ID: {{ $user->employee_id }}</span>
            @endif
        </div>
        @if($user->orcid || $user->google_scholar || $user->research_gate)
            <div class="links">
                @if($user->orcid)
                    <a href="{{ $user->orcid }}">ORCID</a>
                @endif
                @if($user->google_scholar)
                    <a href="{{ $user->google_scholar }}">Google Scholar</a>
                @endif
                @if($user->research_gate)
                    <a href="{{ $user->research_gate }}">ResearchGate</a>
                @endif
            </div>
        @endif
    </div>

    <!-- Research Metrics Summary -->
    @if($user->h_index || $user->citation_number || $user->scopus_h_index || $user->scopus_citation_number)
    <div class="section">
        <h2>Research Metrics</h2>
        <table>
            <tr>
                @if($user->h_index)
                    <td><strong>H-Index:</strong> {{ $user->h_index }}</td>
                @endif
                @if($user->citation_number)
                    <td><strong>Citations:</strong> {{ number_format($user->citation_number) }}</td>
                @endif
            </tr>
            <tr>
                @if($user->scopus_h_index)
                    <td><strong>Scopus H-Index:</strong> {{ $user->scopus_h_index }}</td>
                @endif
                @if($user->scopus_citation_number)
                    <td><strong>Scopus Citations:</strong> {{ number_format($user->scopus_citation_number) }}</td>
                @endif
            </tr>
            @if($user->scopus_papers)
            <tr>
                <td colspan="2"><strong>Scopus Papers:</strong> {{ $user->scopus_papers }}</td>
            </tr>
            @endif
        </table>
    </div>
    @endif

    <!-- Publications -->
    @if($publications->count() > 0)
    <div class="section">
        <h2>Publications ({{ $publications->count() }})</h2>
        @foreach($publications as $index => $publication)
            <div class="item">
                <div class="item-title">
                    {{ $index + 1 }}. {{ $publication->title }}
                </div>
                <div class="item-meta">
                    @if($publication->publication_type)
                        <strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $publication->publication_type)) }}
                    @endif
                    @if($publication->publication_year)
                        | <strong>Year:</strong> {{ $publication->publication_year }}
                    @endif
                    @if($publication->journal_name)
                        | <strong>Journal:</strong> {{ $publication->journal_name }}
                    @endif
                    @if($publication->conference_name)
                        | <strong>Conference:</strong> {{ $publication->conference_name }}
                    @endif
                    @if($publication->status)
                        | <span class="badge badge-{{ $publication->status === 'approved' ? 'approved' : ($publication->status === 'submitted' ? 'submitted' : 'draft') }}">
                            {{ ucfirst($publication->status) }}
                        </span>
                    @endif
                </div>
                @if($publication->abstract)
                    <div class="item-description">
                        {{ Str::limit(strip_tags($publication->abstract), 200) }}
                    </div>
                @endif
                @if($publication->doi)
                    <div style="font-size: 8pt; color: #2c5aa0; margin-top: 3px;">
                        DOI: {{ $publication->doi }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>
    @endif

    <!-- Grants -->
    @if($grants->count() > 0)
    <div class="section">
        <h2>Grants & Funded Research ({{ $grants->count() }})</h2>
        @foreach($grants as $index => $grant)
            <div class="item">
                <div class="item-title">
                    {{ $index + 1 }}. {{ $grant->title }}
                </div>
                <div class="item-meta">
                    @if($grant->grant_type)
                        <strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $grant->grant_type)) }}
                    @endif
                    @if($grant->award_year)
                        | <strong>Year:</strong> {{ $grant->award_year }}
                    @endif
                    @if($grant->amount_omr)
                        | <strong>Amount:</strong> OMR {{ number_format($grant->amount_omr, 2) }}
                    @endif
                    @if($grant->sponsor_name)
                        | <strong>Sponsor:</strong> {{ $grant->sponsor_name }}
                    @endif
                    @if($grant->status)
                        | <span class="badge badge-{{ $grant->status === 'approved' ? 'approved' : ($grant->status === 'submitted' ? 'submitted' : 'draft') }}">
                            {{ ucfirst($grant->status) }}
                        </span>
                    @endif
                </div>
                @if($grant->description)
                    <div class="item-description">
                        {{ Str::limit(strip_tags($grant->description), 200) }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>
    @endif

    <!-- RTN Submissions -->
    @if($rtnSubmissions->count() > 0)
    <div class="section">
        <h2>Research, Teaching & Networking (RTN) Activities ({{ $rtnSubmissions->count() }})</h2>
        @foreach($rtnSubmissions as $index => $rtn)
            <div class="item">
                <div class="item-title">
                    {{ $index + 1 }}. {{ $rtn->title }}
                </div>
                <div class="item-meta">
                    @if($rtn->rtn_type)
                        <strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $rtn->rtn_type)) }}
                    @endif
                    @if($rtn->year)
                        | <strong>Year:</strong> {{ $rtn->year }}
                    @endif
                    @if($rtn->points)
                        | <strong>Points:</strong> {{ number_format($rtn->points, 2) }}
                    @endif
                    @if($rtn->status)
                        | <span class="badge badge-{{ $rtn->status === 'approved' ? 'approved' : ($rtn->status === 'submitted' ? 'submitted' : 'draft') }}">
                            {{ ucfirst($rtn->status) }}
                        </span>
                    @endif
                </div>
                @if($rtn->description)
                    <div class="item-description">
                        {{ Str::limit(strip_tags($rtn->description), 150) }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>
    @endif

    <!-- Bonus Recognitions -->
    @if($bonusRecognitions->count() > 0)
    <div class="section">
        <h2>Awards & Recognitions ({{ $bonusRecognitions->count() }})</h2>
        @foreach($bonusRecognitions as $index => $bonus)
            <div class="item">
                <div class="item-title">
                    {{ $index + 1 }}. {{ $bonus->title }}
                </div>
                <div class="item-meta">
                    @if($bonus->recognition_type)
                        <strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $bonus->recognition_type)) }}
                    @endif
                    @if($bonus->year)
                        | <strong>Year:</strong> {{ $bonus->year }}
                    @endif
                    @if($bonus->organization)
                        | <strong>Organization:</strong> {{ $bonus->organization }}
                    @endif
                    @if($bonus->status)
                        | <span class="badge badge-{{ $bonus->status === 'approved' ? 'approved' : ($bonus->status === 'submitted' ? 'submitted' : 'draft') }}">
                            {{ ucfirst($bonus->status) }}
                        </span>
                    @endif
                </div>
                @if($bonus->description)
                    <div class="item-description">
                        {{ Str::limit(strip_tags($bonus->description), 150) }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div>Generated from Research Management System | {{ date('F d, Y') }}</div>
        <div>Page <span class="page-number"></span></div>
    </div>
</body>
</html>
