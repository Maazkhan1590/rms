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
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0">
                        <thead>
                            <tr>
                                <th style="width: 200px;">Module</th>
                                <th>Permissions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissions as $module => $modulePermissions)
                                <tr>
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
                                                    class="form-check-input"
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