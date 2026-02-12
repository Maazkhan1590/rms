<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $user->name }} - Curriculum Vitae</title>
    <style>
        @page {
            margin: 2.5cm 2cm;
            @bottom-center {
                content: "Page " counter(page) " of " counter(pages);
            }
        }
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 10.5pt;
            line-height: 1.5;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }
        
        /* Header Styles */
        .cv-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: white;
            padding: 30px 25px;
            margin: -2.5cm -2cm 25px -2cm;
            text-align: center;
        }
        .cv-name {
            font-size: 28pt;
            font-weight: bold;
            margin: 0 0 8px 0;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .cv-title {
            font-size: 13pt;
            margin: 0 0 15px 0;
            opacity: 0.95;
            font-weight: normal;
        }
        .cv-institution {
            font-size: 11pt;
            margin: 5px 0;
            opacity: 0.9;
        }
        .cv-contact {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255,255,255,0.3);
            font-size: 9.5pt;
        }
        .cv-contact-item {
            display: inline-block;
            margin: 0 15px;
            opacity: 0.95;
        }
        .cv-links {
            margin-top: 10px;
            font-size: 9pt;
        }
        .cv-links a {
            color: white;
            text-decoration: none;
            margin: 0 12px;
            opacity: 0.9;
            border-bottom: 1px solid rgba(255,255,255,0.5);
        }
        
        /* Section Headers */
        h2 {
            font-size: 15pt;
            font-weight: bold;
            color: #1e3a8a;
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 3px solid #2563eb;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        h2:first-of-type {
            margin-top: 0;
        }
        
        /* Research Metrics Box */
        .metrics-box {
            background: #f8fafc;
            border: 2px solid #2563eb;
            border-radius: 8px;
            padding: 15px 20px;
            margin: 15px 0 20px 0;
        }
        .metrics-grid {
            display: table;
            width: 100%;
        }
        .metrics-row {
            display: table-row;
        }
        .metrics-cell {
            display: table-cell;
            padding: 8px 15px;
            width: 50%;
            font-size: 10pt;
        }
        .metrics-label {
            font-weight: bold;
            color: #1e3a8a;
        }
        .metrics-value {
            color: #334155;
            font-size: 11pt;
            font-weight: 600;
        }
        
        /* Publication Items */
        .section {
            margin-bottom: 25px;
        }
        .cv-item {
            margin-bottom: 18px;
            page-break-inside: avoid;
            padding-left: 25px;
            position: relative;
        }
        .cv-item-number {
            position: absolute;
            left: 0;
            top: 2px;
            font-weight: bold;
            color: #2563eb;
            font-size: 10pt;
        }
        .cv-item-title {
            font-weight: bold;
            color: #1a1a1a;
            font-size: 10.5pt;
            margin-bottom: 5px;
            line-height: 1.4;
        }
        .cv-item-meta {
            font-size: 9.5pt;
            color: #475569;
            margin-bottom: 6px;
            line-height: 1.4;
        }
        .cv-item-meta strong {
            color: #1e3a8a;
            font-weight: 600;
        }
        .cv-item-description {
            font-size: 9.5pt;
            color: #475569;
            line-height: 1.4;
            margin-top: 5px;
            font-style: italic;
        }
        .cv-item-doi {
            font-size: 8.5pt;
            color: #2563eb;
            margin-top: 5px;
            font-family: monospace;
        }
        
        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            font-size: 8pt;
            font-weight: bold;
            border-radius: 4px;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
        
        /* Summary Counters */
        .summary-count {
            font-size: 12pt;
            color: #2563eb;
            font-weight: bold;
        }
        
        /* Footer */
        .cv-footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            font-size: 8.5pt;
            color: #64748b;
        }
        .cv-footer-date {
            font-weight: 600;
            color: #475569;
        }
        
        /* Empty State */
        .no-items {
            text-align: center;
            padding: 20px;
            color: #64748b;
            font-style: italic;
            background: #f8fafc;
            border-radius: 6px;
        }
        
        /* Professional Info Table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .info-table tr {
            border-bottom: 1px solid #e2e8f0;
        }
        .info-table td {
            padding: 10px 15px;
            font-size: 10pt;
        }
        .info-table td:first-child {
            font-weight: bold;
            color: #1e3a8a;
            width: 35%;
        }
        .info-table td:last-child {
            color: #334155;
        }
        .na-text {
            color: #94a3b8;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Professional Header -->
    <div class="cv-header">
        <div class="cv-name">{{ $user->name ?? 'N/A' }}</div>
        <div class="cv-title">{{ $user->designation ?? 'Faculty Member' }}</div>
        <div class="cv-institution">{{ $user->college->name ?? 'N/A' }}</div>
        <div class="cv-institution">{{ $user->department->name ?? 'N/A' }}</div>
        <div class="cv-contact">
            <span class="cv-contact-item">✉ {{ $user->email ?? 'N/A' }}</span>
            <span class="cv-contact-item">☎ {{ $user->phone ?? 'N/A' }}</span>
            @if($user->employee_id)
                <span class="cv-contact-item">ID: {{ $user->employee_id }}</span>
            @endif
        </div>
        @if($user->orcid || $user->google_scholar || $user->research_gate)
            <div class="cv-links">
                @if($user->orcid)
                    <a href="{{ $user->orcid }}">ORCID Profile</a>
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

    <!-- Professional Information -->
    <div class="section">
        <h2>Professional Information</h2>
        <table class="info-table">
            <tr>
                <td>Full Name</td>
                <td>{{ $user->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Employee ID</td>
                <td>{{ $user->employee_id ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Designation</td>
                <td>{{ $user->designation ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>College</td>
                <td>{{ $user->college->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Department</td>
                <td>{{ $user->department->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>{{ $user->email ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Phone</td>
                <td>{{ $user->phone ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>ORCID</td>
                <td>{{ $user->orcid ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Google Scholar</td>
                <td>{{ $user->google_scholar ? 'Available' : 'N/A' }}</td>
            </tr>
            <tr>
                <td>ResearchGate</td>
                <td>{{ $user->research_gate ? 'Available' : 'N/A' }}</td>
            </tr>
            <tr>
                <td>Sohar Affiliation</td>
                <td>{{ $user->sohar_affiliation ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <!-- Research Impact Metrics -->
    <div class="section">
        <h2>Research Impact Metrics</h2>
        <div class="metrics-box">
            <div class="metrics-grid">
                <div class="metrics-row">
                    <div class="metrics-cell">
                        <span class="metrics-label">H-Index:</span>
                        <span class="metrics-value">{{ $user->h_index ?? 'N/A' }}</span>
                    </div>
                    <div class="metrics-cell">
                        <span class="metrics-label">Total Citations:</span>
                        <span class="metrics-value">{{ $user->citation_number ? number_format($user->citation_number) : 'N/A' }}</span>
                    </div>
                </div>
                <div class="metrics-row">
                    <div class="metrics-cell">
                        <span class="metrics-label">Scopus H-Index:</span>
                        <span class="metrics-value">{{ $user->scopus_h_index ?? 'N/A' }}</span>
                    </div>
                    <div class="metrics-cell">
                        <span class="metrics-label">Scopus Citations:</span>
                        <span class="metrics-value">{{ $user->scopus_citation_number ? number_format($user->scopus_citation_number) : 'N/A' }}</span>
                    </div>
                </div>
                <div class="metrics-row">
                    <div class="metrics-cell" style="width: 100%;">
                        <span class="metrics-label">Scopus Indexed Papers:</span>
                        <span class="metrics-value">{{ $user->scopus_papers ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Publications -->
    <div class="section">
        <h2>Publications <span class="summary-count">({{ $publications->count() }})</span></h2>
        @if($publications->count() > 0)
        @foreach($publications as $index => $publication)
            <div class="cv-item">
                <div class="cv-item-number">[{{ $index + 1 }}]</div>
                <div class="cv-item-title">
                    {{ $publication->title ?? 'Untitled' }}
                </div>
                <div class="cv-item-meta">
                    <strong>Type:</strong> {{ $publication->publication_type ? ucfirst(str_replace('_', ' ', $publication->publication_type)) : 'N/A' }}
                    &nbsp;|&nbsp; <strong>Year:</strong> {{ $publication->publication_year ?? 'N/A' }}
                    @if($publication->journal_name)
                        &nbsp;|&nbsp; <strong>Journal:</strong> {{ $publication->journal_name }}
                    @endif
                    @if($publication->conference_name)
                        &nbsp;|&nbsp; <strong>Conference:</strong> {{ $publication->conference_name }}
                    @endif
                    @if($publication->volume)
                        &nbsp;|&nbsp; <strong>Vol.</strong> {{ $publication->volume }}
                    @endif
                    @if($publication->issue)
                        &nbsp;|&nbsp; <strong>Issue</strong> {{ $publication->issue }}
                    @endif
                    @if($publication->pages)
                        &nbsp;|&nbsp; <strong>Pages:</strong> {{ $publication->pages }}
                    @endif
                    &nbsp;|&nbsp; <span class="status-badge status-{{ $publication->status === 'approved' ? 'approved' : ($publication->status === 'submitted' ? 'submitted' : 'draft') }}">
                        {{ ucfirst($publication->status ?? 'draft') }}
                    </span>
                </div>
                @if($publication->abstract)
                    <div class="cv-item-description">
                        {{ Str::limit(strip_tags($publication->abstract), 250) }}
                    </div>
                @endif
                @if($publication->doi)
                    <div class="cv-item-doi">
                        DOI: {{ $publication->doi }}
                    </div>
                @endif
            </div>
        @endforeach
        @else
            <div class="no-items">
                No publications recorded for this faculty member.
            </div>
        @endif
    </div>

    <!-- Grants -->
    <div class="section">
        <h2>Research Grants & Funding <span class="summary-count">({{ $grants->count() }})</span></h2>
        @if($grants->count() > 0)
        @foreach($grants as $index => $grant)
            <div class="cv-item">
                <div class="cv-item-number">[{{ $index + 1 }}]</div>
                <div class="cv-item-title">
                    {{ $grant->title ?? 'Untitled Grant' }}
                </div>
                <div class="cv-item-meta">
                    <strong>Type:</strong> {{ $grant->grant_type ? ucfirst(str_replace('_', ' ', $grant->grant_type)) : 'N/A' }}
                    &nbsp;|&nbsp; <strong>Award Year:</strong> {{ $grant->award_year ?? 'N/A' }}
                    &nbsp;|&nbsp; <strong>Funding:</strong> {{ $grant->amount_omr ? 'OMR ' . number_format($grant->amount_omr, 2) : 'N/A' }}
                    &nbsp;|&nbsp; <strong>Sponsor:</strong> {{ $grant->sponsor_name ?? 'N/A' }}
                    @if($grant->role)
                        &nbsp;|&nbsp; <strong>Role:</strong> {{ $grant->role }}
                    @endif
                    &nbsp;|&nbsp; <span class="status-badge status-{{ $grant->status === 'approved' ? 'approved' : ($grant->status === 'submitted' ? 'submitted' : 'draft') }}">
                        {{ ucfirst($grant->status ?? 'draft') }}
                    </span>
                </div>
                @if($grant->description)
                    <div class="cv-item-description">
                        {{ Str::limit(strip_tags($grant->description), 250) }}
                    </div>
                @endif
            </div>
        @endforeach
        @else
            <div class="no-items">
                No grants or funded research recorded for this faculty member.
            </div>
        @endif
    </div>

    <!-- RTN Submissions -->
    <div class="section">
        <h2>Research, Teaching & Networking Activities <span class="summary-count">({{ $rtnSubmissions->count() }})</span></h2>
        @if($rtnSubmissions->count() > 0)
        @foreach($rtnSubmissions as $index => $rtn)
            <div class="cv-item">
                <div class="cv-item-number">[{{ $index + 1 }}]</div>
                <div class="cv-item-title">
                    {{ $rtn->title ?? 'Untitled Activity' }}
                </div>
                <div class="cv-item-meta">
                    <strong>Type:</strong> {{ $rtn->rtn_type ? ucfirst(str_replace('_', ' ', $rtn->rtn_type)) : 'N/A' }}
                    &nbsp;|&nbsp; <strong>Year:</strong> {{ $rtn->year ?? 'N/A' }}
                    &nbsp;|&nbsp; <strong>Research Points:</strong> {{ $rtn->points ? number_format($rtn->points, 2) : 'N/A' }}
                    &nbsp;|&nbsp; <span class="status-badge status-{{ $rtn->status === 'approved' ? 'approved' : ($rtn->status === 'submitted' ? 'submitted' : 'draft') }}">
                        {{ ucfirst($rtn->status ?? 'draft') }}
                    </span>
                </div>
                @if($rtn->description)
                    <div class="cv-item-description">
                        {{ Str::limit(strip_tags($rtn->description), 200) }}
                    </div>
                @endif
            </div>
        @endforeach
        @else
            <div class="no-items">
                No RTN activities recorded for this faculty member.
            </div>
        @endif
    </div>

    <!-- Bonus Recognitions -->
    <div class="section">
        <h2>Awards & Professional Recognition <span class="summary-count">({{ $bonusRecognitions->count() }})</span></h2>
        @if($bonusRecognitions->count() > 0)
        @foreach($bonusRecognitions as $index => $bonus)
            <div class="cv-item">
                <div class="cv-item-number">[{{ $index + 1 }}]</div>
                <div class="cv-item-title">
                    {{ $bonus->title ?? 'Untitled Recognition' }}
                </div>
                <div class="cv-item-meta">
                    <strong>Type:</strong> {{ $bonus->recognition_type ? ucfirst(str_replace('_', ' ', $bonus->recognition_type)) : 'N/A' }}
                    &nbsp;|&nbsp; <strong>Year:</strong> {{ $bonus->year ?? 'N/A' }}
                    &nbsp;|&nbsp; <strong>Organization:</strong> {{ $bonus->organization ?? 'N/A' }}
                    &nbsp;|&nbsp; <span class="status-badge status-{{ $bonus->status === 'approved' ? 'approved' : ($bonus->status === 'submitted' ? 'submitted' : 'draft') }}">
                        {{ ucfirst($bonus->status ?? 'draft') }}
                    </span>
                </div>
                @if($bonus->description)
                    <div class="cv-item-description">
                        {{ Str::limit(strip_tags($bonus->description), 200) }}
                    </div>
                @endif
            </div>
        @endforeach
        @else
            <div class="no-items">
                No awards or recognitions recorded for this faculty member.
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="cv-footer">
        <div class="cv-footer-date">
            Curriculum Vitae - Generated on {{ date('F d, Y') }}
        </div>
        <div style="margin-top: 5px;">
            Research Management System | University Academic Portal
        </div>
    </div>
</body>
</html>
