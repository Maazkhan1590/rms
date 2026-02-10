@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <strong>{{ $user->name }}</strong>
            <small class="text-muted d-block">{{ trans('cruds.user.title_singular') }} #{{ $user->id }}</small>
        </div>
        <div class="btn-toolbar" role="toolbar">
            <div class="btn-group btn-group-sm mr-2" role="group">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary" title="{{ trans('global.back_to_list') }}" aria-label="{{ trans('global.back_to_list') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M14 5L8 11L14 17" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
            <div class="btn-group btn-group-sm" role="group">
                @can('user_edit')
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline-primary" title="{{ trans('global.edit') }}" aria-label="{{ trans('global.edit') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M7 20H20" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            <path d="M7 14.5L15.75 5.75L18.75 8.75L10 17.5L7 17.5V14.5Z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                        </svg>
                    </a>
                @endcan
                @can('user_delete')
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display:inline;">
                        @method('DELETE')
                        @csrf
                        <button type="submit" class="btn btn-outline-danger" title="{{ trans('global.delete') }}" aria-label="{{ trans('global.delete') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M9 10V17M15 10V17" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                <path d="M6 7H18L17.2 18.2C17.12 19.3 16.2 20.15 15.09 20.15H8.91C7.8 20.15 6.88 19.3 6.8 18.2L6 7Z" fill="currentColor" fill-opacity="0.12" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                                <path d="M9 4H15C15.55 4 16 4.45 16 5V6H8V5C8 4.45 8.45 4 9 4Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M5 6H19" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-4">
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
            <div class="col-md-6 mb-4">
                <h5 class="mb-3">Research Profile</h5>
                <table class="table table-borderless table-sm mb-0">
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
            <div class="col-md-6">
                <h5 class="mb-3">Organization</h5>
                <table class="table table-borderless table-sm mb-0">
                    <tbody>
                        <tr>
                            <th style="width: 40%;">College</th>
                            <td>{{ optional($user->college)->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Department</th>
                            <td>{{ optional($user->department)->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Designation</th>
                            <td>{{ $user->designation ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Employee ID</th>
                            <td>{{ $user->employee_id ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td>{{ $user->phone ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



@endsection