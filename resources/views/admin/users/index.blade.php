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
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#rejectModal{{ $user->id }}" title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endcan
                                    @endif

                                    @can('user_show')
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.users.show', $user->id) }}" title="{{ trans('global.view') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endcan

                                    @can('user_edit')
                                        <a class="btn btn-sm btn-outline-info" href="{{ route('admin.users.edit', $user->id) }}" title="{{ trans('global.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan

                                    @can('user_delete')
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ trans('global.delete') }}">
                                                <i class="fas fa-trash-alt"></i>
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