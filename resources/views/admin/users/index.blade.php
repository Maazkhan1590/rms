@extends('layouts.admin')
@section('content')
@can('user_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.users.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.user.title_singular') }}
            </a>
        </div>
    </div>
@endcan

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <div>
                <h3 style="margin: 0; display: inline-block;">{{ trans('cruds.user.title_singular') }} {{ trans('global.list') }}</h3>
                @if(isset($pendingCount) && $pendingCount > 0)
                    <span class="badge badge-warning" style="margin-left: 10px;">{{ $pendingCount }} PENDING APPROVAL</span>
                @endif
            </div>
            <div style="margin-top: 5px;">
                <a href="{{ route('admin.users.index', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') == 'pending' ? 'btn-warning' : 'btn-outline-warning' }}" style="margin-right: 5px;">
                    Pending @if(isset($pendingCount) && $pendingCount > 0)({{ $pendingCount }})@endif
                </a>
                <a href="{{ route('admin.users.index', ['status' => 'active']) }}" class="btn btn-sm {{ request('status') == 'active' ? 'btn-success' : 'btn-outline-success' }}" style="margin-right: 5px;">
                    Active
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-secondary' : 'btn-outline-secondary' }}">
                    All
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-User" style="min-width: 1400px;">
                <thead>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.user.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.user.fields.email') }}
                        </th>
                        <th>
                            {{ trans('cruds.user.fields.email_verified_at') }}
                        </th>
                        <th>
                            Google Scholar
                        </th>
                        <th>
                            Citations
                        </th>
                        <th>
                            H-index
                        </th>
                        <th>
                            Scopus Link
                        </th>
                        <th>
                            Sohar Affiliation
                        </th>
                        <th>
                            ORCID Connected
                        </th>
                        <th>
                            Scopus Papers
                        </th>
                        <th>
                            {{ trans('cruds.user.fields.roles') }}
                        </th>
                        <th>
                            Status
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $key => $user)
                        <tr data-entry-id="{{ $user->id }}">
                            <td>
                                {{ $user->id ?? '----' }}
                            </td>
                            <td>
                                {{ $user->name ?: '----' }}
                            </td>
                            <td>
                                {{ $user->email ?: '----' }}
                            </td>
                            <td>
                                {{ $user->email_verified_at ?: '----' }}
                            </td>
                            <td>
                                @if($user->google_scholar)
                                    <a href="{{ $user->google_scholar }}" target="_blank" rel="noopener noreferrer">Profile</a>
                                @else
                                    ----
                                @endif
                            </td>
                            <td>
                                {{ $user->citation_number !== null ? $user->citation_number : '----' }}
                            </td>
                            <td>
                                {{ $user->h_index !== null ? $user->h_index : '----' }}
                            </td>
                            <td>
                                @if($user->research_gate)
                                    <a href="{{ $user->research_gate }}" target="_blank" rel="noopener noreferrer">Scopus</a>
                                @else
                                    ----
                                @endif
                            </td>
                            <td>
                                {{ $user->sohar_affiliation ?? '----' }}
                            </td>
                            <td>
                                {{ $user->orcid_connected ?? '----' }}
                            </td>
                            <td>
                                {{ $user->scopus_papers !== null ? $user->scopus_papers : '----' }}
                            </td>
                            <td>
                                @if($user->roles->count())
                                    @foreach($user->roles as $key => $item)
                                        <span class="badge badge-info">{{ $item->title }}</span>
                                    @endforeach
                                @else
                                    ----
                                @endif
                            </td>
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
                            <td>
                                <div class="btn-group btn-group-sm" role="group" aria-label="User actions">
                                    @if($user->status === 'pending')
                                        @can('user_edit')
                                            <form action="{{ route('admin.users.approve', $user->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Approve this user? An email notification will be sent.');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Approve" aria-label="Approve">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16" fill="none">
                                                        <path d="M6.00039 10.7998L3.20039 7.9998L2.26606 8.93313L6.00039 12.6665L14.0004 4.66647L13.0671 3.73314L6.00039 10.7998Z" fill="currentColor"/>
                                                    </svg>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#rejectModal{{ $user->id }}" title="Reject" aria-label="Reject">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16" fill="none">
                                                    <path d="M4.222 4.222L11.778 11.778M11.778 4.222L4.222 11.778" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                                </svg>
                                            </button>
                                        @endcan
                                    @endif

                                    @can('user_show')
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.users.show', $user->id) }}" title="{{ trans('global.view') }}" aria-label="{{ trans('global.view') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                <path d="M12 5C7 5 3.3 8.11 2 12C3.3 15.89 7 19 12 19C17 19 20.7 15.89 22 12C20.7 8.11 17 5 12 5Z" fill="currentColor" fill-opacity="0.12"/>
                                                <path d="M12 9.25C10.48 9.25 9.25 10.48 9.25 12C9.25 13.52 10.48 14.75 12 14.75C13.52 14.75 14.75 13.52 14.75 12C14.75 10.48 13.52 9.25 12 9.25Z" fill="currentColor"/>
                                                <path d="M2 12C3.3 8.11 7 5 12 5C17 5 20.7 8.11 22 12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                    @endcan

                                    @can('user_edit')
                                        <a class="btn btn-sm btn-outline-info" href="{{ route('admin.users.edit', $user->id) }}" title="{{ trans('global.edit') }}" aria-label="{{ trans('global.edit') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                <path d="M15.75 4.25L18.75 7.25L9 17H6V14L15.75 4.25Z" fill="currentColor" fill-opacity="0.15"/>
                                                <path d="M15.75 4.25L18.75 7.25M7 20H20" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M6 14L15.75 4.25L18.75 7.25L9 17H6V14Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                    @endcan

                                    @can('user_delete')
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ trans('global.delete') }}" aria-label="{{ trans('global.delete') }}">
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
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reject Modals -->
@foreach($users as $user)
    @if($user->status === 'pending')
    <div class="modal fade" id="rejectModal{{ $user->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject User Account</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.users.reject', $user->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Are you sure you want to reject <strong>{{ $user->name }}</strong>'s account?</p>
                        <div class="form-group">
                            <label for="reason{{ $user->id }}">Reason (Optional)</label>
                            <textarea class="form-control" id="reason{{ $user->id }}" name="reason" rows="3" placeholder="Enter rejection reason..."></textarea>
                            <small class="form-text text-muted">This reason will be included in the email notification.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

@endsection
@section('scripts')
@parent
<script>
    $(function () {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

        $.extend(true, $.fn.dataTable.defaults, {
            orderCellsTop: true,
            order: [[ 0, 'desc' ]],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        });

        let table = $('.datatable-User:not(.ajaxTable)').DataTable({
            buttons: dtButtons,
            // Disable row selection / checkboxes for this table
            select: false,
            columnDefs: [
                { targets: -1, orderable: false, searchable: false, responsivePriority: 1 }
            ]
        })
        $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });
    })

</script>
@endsection