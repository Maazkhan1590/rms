@extends('layouts.admin')
@section('content')

@if(session('force_password_change'))
    <div class="alert alert-warning" role="alert">
        <strong>Security notice:</strong> As a Dean or Research Coordinator, you must set a new password before continuing to use the system.
    </div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                {{ trans('global.my_profile') }}
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route("profile.password.updateProfile") }}">
                    @csrf
                    <div class="form-group">
                        <label class="required" for="name">{{ trans('cruds.user.fields.name') }}</label>
                        <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" required>
                        @if($errors->has('name'))
                            <div class="invalid-feedback">
                                {{ $errors->first('name') }}
                            </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label class="required" for="title">{{ trans('cruds.user.fields.email') }}</label>
                        <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="text" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" required>
                        @if($errors->has('email'))
                            <div class="invalid-feedback">
                                {{ $errors->first('email') }}
                            </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <button class="btn btn-danger" type="submit">
                            {{ trans('global.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                {{ trans('global.change_password') }}
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route("profile.password.update") }}">
                    @csrf
                    <div class="form-group">
                        <label class="required" for="password">New {{ trans('cruds.user.fields.password') }}</label>
                        <div class="position-relative">
                            <input
                                class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                type="password"
                                name="password"
                                id="password"
                                required
                                autocomplete="new-password"
                                style="padding-right: 2.5rem;"
                            >
                            <button
                                class="js-toggle-password"
                                type="button"
                                data-target="password"
                                aria-label="Show password"
                                title="Show password"
                                style="position:absolute;top:50%;right:0.75rem;transform:translateY(-50%);border:none;background:transparent;padding:0;cursor:pointer;outline:none;"
                            >
                                <span class="material-icons-outlined" aria-hidden="true" style="font-size:20px;line-height:1;">visibility</span>
                            </button>
                        </div>
                        @if($errors->has('password'))
                            <div class="invalid-feedback">
                                {{ $errors->first('password') }}
                            </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label class="required" for="password_confirmation">Repeat New {{ trans('cruds.user.fields.password') }}</label>
                        <div class="position-relative">
                            <input
                                class="form-control"
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                required
                                autocomplete="new-password"
                                style="padding-right: 2.5rem;"
                            >
                            <button
                                class="js-toggle-password"
                                type="button"
                                data-target="password_confirmation"
                                aria-label="Show password confirmation"
                                title="Show password"
                                style="position:absolute;top:50%;right:0.75rem;transform:translateY(-50%);border:none;background:transparent;padding:0;cursor:pointer;outline:none;"
                            >
                                <span class="material-icons-outlined" aria-hidden="true" style="font-size:20px;line-height:1;">visibility</span>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-danger" type="submit">
                            {{ trans('global.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row" style="display: none">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                {{ trans('global.delete_account') }}
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route("profile.password.destroyProfile") }}" onsubmit="return prompt('{{ __('global.delete_account_warning') }}') == '{{ auth()->user()->email }}'">
                    @csrf
                    <div class="form-group">
                        <button class="btn btn-danger" type="submit">
                            {{ trans('global.delete') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
@parent
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toggles = document.querySelectorAll('.js-toggle-password');
        toggles.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var targetId = btn.getAttribute('data-target');
                if (!targetId) return;

                var input = document.getElementById(targetId);
                if (!input) return;

                var icon = btn.querySelector('.material-icons-outlined');
                var isHidden = input.type === 'password';

                input.type = isHidden ? 'text' : 'password';

                if (icon) {
                    icon.textContent = isHidden ? 'visibility_off' : 'visibility';
                }

                btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
                btn.setAttribute('title', isHidden ? 'Hide password' : 'Show password');
            });
        });
    });
</script>
@endsection
