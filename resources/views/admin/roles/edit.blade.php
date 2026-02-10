@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.role.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.roles.update", [$role->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="title">{{ trans('cruds.role.fields.title') }}</label>
                <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title" id="title" value="{{ old('title', $role->title) }}" required>
                @if($errors->has('title'))
                    <div class="invalid-feedback">
                        {{ $errors->first('title') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.role.fields.title_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="permissions">{{ trans('cruds.role.fields.permissions') }}</label>
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
                    <div class="flex-grow-1 mr-2" style="max-width: 320px;">
                        <input
                            type="text"
                            id="permission-search"
                            class="form-control form-control-sm"
                            placeholder="Search permission or module..."
                            autocomplete="off"
                        >
                    </div>
                    <div class="btn-group btn-group-sm mt-1 mt-md-0" role="group">
                        <button type="button" class="btn btn-outline-secondary js-perm-select-all">Select all</button>
                        <button type="button" class="btn btn-outline-secondary js-perm-deselect-all">Deselect all</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0">
                        <thead>
                            <tr>
                                <th style="width: 200px;">Module</th>
                                <th>Permissions</th>
                            </tr>
                        </thead>
                        <tbody id="permissions-table-body">
                            @foreach($permissions as $module => $modulePermissions)
                                <tr
                                    class="js-permission-row"
                                    data-permission-text="{{ strtolower($module) }} {{ strtolower(implode(' ', $modulePermissions->pluck('title')->toArray())) }}"
                                >
                                    <td>
                                        <strong>{{ ucwords(str_replace('_', ' ', $module)) }}</strong>
                                    </td>
                                    <td>
                                        @foreach($modulePermissions as $permission)
                                            @php
                                                $parts = explode('_', $permission->title, 2);
                                                $action = $parts[1] ?? $parts[0];
                                                $checked = in_array(
                                                    $permission->id,
                                                    old('permissions', $role->permissions->pluck('id')->toArray())
                                                );
                                            @endphp
                                            <div class="form-check form-check-inline mb-1">
                                                <input
                                                    class="form-check-input js-permission-checkbox"
                                                    type="checkbox"
                                                    name="permissions[]"
                                                    id="perm_{{ $permission->id }}"
                                                    value="{{ $permission->id }}"
                                                    {{ $checked ? 'checked' : '' }}
                                                >
                                                <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                    {{ ucwords(str_replace('_', ' ', $action)) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($errors->has('permissions'))
                    <div class="invalid-feedback">
                        {{ $errors->first('permissions') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.role.fields.permissions_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection

@section('scripts')
@parent
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var searchInput = document.getElementById('permission-search');
        var rows = document.querySelectorAll('.js-permission-row');

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                var term = this.value.toLowerCase();

                rows.forEach(function (row) {
                    var text = (row.getAttribute('data-permission-text') || '').toLowerCase();
                    row.style.display = term === '' || text.indexOf(term) !== -1 ? '' : 'none';
                });
            });
        }

        function setAllPermissions(checked) {
            document.querySelectorAll('.js-permission-checkbox').forEach(function (cb) {
                if (!cb.disabled) {
                    cb.checked = checked;
                }
            });
        }

        document.querySelectorAll('.js-perm-select-all').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                setAllPermissions(true);
            });
        });

        document.querySelectorAll('.js-perm-deselect-all').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                setAllPermissions(false);
            });
        });
    });
</script>
@endsection