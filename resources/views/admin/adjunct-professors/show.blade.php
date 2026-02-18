@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="mb-0">
            <span class="material-icons-outlined" style="vertical-align: middle;">person</span>
            <span style="vertical-align: middle;">Adjunct Professor Details</span>
        </h3>
        <div>
            <a href="{{ route('admin.adjunct-professors.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h4>{{ $adjunctProfessor->name }}</h4>
                
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Email</th>
                        <td>{{ $adjunctProfessor->email ?? 'N/A' }}</td>
                    </tr>
                    @if($adjunctProfessor->appointment_from)
                    <tr>
                        <th>Appointment From</th>
                        <td>{{ $adjunctProfessor->appointment_from->format('F d, Y') }}</td>
                    </tr>
                    @endif
                </table>

                @if($adjunctProfessor->google_scholar || $adjunctProfessor->gs_citation_number || $adjunctProfessor->gs_h_index)
                <div class="mt-4">
                    <h5>Google Scholar Information</h5>
                    <table class="table table-bordered">
                        @if($adjunctProfessor->google_scholar)
                        <tr>
                            <th width="200">Profile URL</th>
                            <td><a href="{{ $adjunctProfessor->google_scholar }}" target="_blank">{{ $adjunctProfessor->google_scholar }}</a></td>
                        </tr>
                        @endif
                        @if($adjunctProfessor->gs_citation_number)
                        <tr>
                            <th>Citation Number</th>
                            <td>{{ $adjunctProfessor->gs_citation_number }}</td>
                        </tr>
                        @endif
                        @if($adjunctProfessor->gs_h_index)
                        <tr>
                            <th>H-Index</th>
                            <td>{{ $adjunctProfessor->gs_h_index }}</td>
                        </tr>
                        @endif
                        @if($adjunctProfessor->gs_papers_2025)
                        <tr>
                            <th>Papers in 2025</th>
                            <td>{{ $adjunctProfessor->gs_papers_2025 }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                @endif

                @if($adjunctProfessor->scopus_scholar || $adjunctProfessor->scopus_citation_number || $adjunctProfessor->scopus_h_index)
                <div class="mt-4">
                    <h5>Scopus Information</h5>
                    <table class="table table-bordered">
                        @if($adjunctProfessor->scopus_scholar)
                        <tr>
                            <th width="200">Profile URL</th>
                            <td><a href="{{ $adjunctProfessor->scopus_scholar }}" target="_blank">{{ $adjunctProfessor->scopus_scholar }}</a></td>
                        </tr>
                        @endif
                        @if($adjunctProfessor->scopus_citation_number)
                        <tr>
                            <th>Citation Number</th>
                            <td>{{ $adjunctProfessor->scopus_citation_number }}</td>
                        </tr>
                        @endif
                        @if($adjunctProfessor->scopus_h_index)
                        <tr>
                            <th>H-Index</th>
                            <td>{{ $adjunctProfessor->scopus_h_index }}</td>
                        </tr>
                        @endif
                        @if($adjunctProfessor->scopus_papers_2025)
                        <tr>
                            <th>Papers in 2025</th>
                            <td>{{ $adjunctProfessor->scopus_papers_2025 }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                @endif

                @if($adjunctProfessor->publication_with_sohar)
                <div class="mt-4">
                    <h5>Publications with Sohar</h5>
                    <p><strong>{{ $adjunctProfessor->publication_with_sohar }}</strong> publications</p>
                </div>
                @endif

                @if($adjunctProfessor->notes)
                <div class="mt-4">
                    <h5>Notes</h5>
                    <p>{{ $adjunctProfessor->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        @if($workflow)
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Workflow Status</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">Current Status</th>
                                <td>
                                    @if($workflow->status == 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($workflow->status == 'pending_coordinator')
                                        <span class="badge badge-warning">Pending Coordinator</span>
                                    @elseif($workflow->status == 'pending_dean')
                                        <span class="badge badge-info">Pending Dean</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $workflow->status)) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @if($workflow->assignee)
                            <tr>
                                <th>Assigned To</th>
                                <td>{{ $workflow->assignee->name }}</td>
                            </tr>
                            @endif
                        </table>

                        @if(in_array($workflow->status, ['pending_coordinator', 'pending_dean']) && $workflow->assigned_to == auth()->id())
                        <div class="mt-3">
                            <form action="{{ route('admin.adjunct-professors.approve', $adjunctProfessor->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.adjunct-professors.reject', $adjunctProfessor->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Adjunct Professor</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Reason for Rejection</label>
                        <textarea name="comments" class="form-control" rows="3" placeholder="Please provide a reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
