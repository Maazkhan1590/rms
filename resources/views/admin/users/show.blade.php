@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <strong>{{ $user->name }}</strong>
            <small class="text-muted d-block">{{ trans('cruds.user.title_singular') }} #{{ $user->id }}</small>
        </div>
        <div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-secondary">
                {{ trans('global.back_to_list') }}
            </a>
            @can('user_edit')
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary">
                    {{ trans('global.edit') }}
                </a>
            @endcan
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-3">Basic Information</h5>
                <table class="table table-borderless table-sm">
                    <tbody>
                        <tr>
                            <th style="width: 35%;">{{ trans('cruds.user.fields.name') }}</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.user.fields.email') }}</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($user->status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($user->status === 'active')
                                    <span class="badge badge-success">Active</span>
                                @elseif($user->status === 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                @elseif($user->status === 'inactive')
                                    <span class="badge badge-secondary">Inactive</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($user->status ?? 'N/A') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.user.fields.email_verified_at') }}</th>
                            <td>{{ $user->email_verified_at ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.user.fields.roles') }}</th>
                            <td>
                                @foreach($user->roles as $roles)
                                    <span class="badge badge-info mr-1">{{ $roles->title }}</span>
                                @endforeach
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <h5 class="mb-3">Research Profile</h5>
                <table class="table table-borderless table-sm">
                    <tbody>
                        <tr>
                            <th style="width: 40%;">Google Scholar</th>
                            <td>
                                @if($user->google_scholar)
                                    <a href="{{ $user->google_scholar }}" target="_blank" rel="noopener noreferrer">View profile</a>
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Citation number</th>
                            <td>{{ $user->citation_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>H-index</th>
                            <td>{{ $user->h_index ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Scopus scholar link</th>
                            <td>
                                @if($user->research_gate)
                                    <a href="{{ $user->research_gate }}" target="_blank" rel="noopener noreferrer">View Scopus</a>
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Sohar Affiliation</th>
                            <td>{{ $user->sohar_affiliation ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>ORCID Connected</th>
                            <td>{{ $user->orcid_connected ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Number of papers (Scopus)</th>
                            <td>{{ $user->scopus_papers ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



@endsection