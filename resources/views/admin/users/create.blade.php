@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.user.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.users.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.user.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="email">{{ trans('cruds.user.fields.email') }}</label>
                <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email" name="email" id="email" value="{{ old('email') }}" required>
                @if($errors->has('email'))
                    <div class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.email_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="password">{{ trans('cruds.user.fields.password') }}</label>
                <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" type="password" name="password" id="password" required>
                @if($errors->has('password'))
                    <div class="invalid-feedback">
                        {{ $errors->first('password') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.password_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="roles">{{ trans('cruds.user.fields.roles') }}</label>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
                <select class="form-control select2 {{ $errors->has('roles') ? 'is-invalid' : '' }}" name="roles[]" id="roles" multiple required>
                    @foreach($roles as $id => $role)
                        <option value="{{ $id }}" {{ in_array($id, old('roles', [])) ? 'selected' : '' }}>{{ $role }}</option>
                    @endforeach
                </select>
                @if($errors->has('roles'))
                    <div class="invalid-feedback">
                        {{ $errors->first('roles') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.roles_helper') }}</span>
            </div>
            <hr>
            <h5>Research Profile (optional)</h5>
            <div class="form-group">
                <label for="google_scholar">Google Scholar link</label>
                <input class="form-control {{ $errors->has('google_scholar') ? 'is-invalid' : '' }}" type="url" name="google_scholar" id="google_scholar" value="{{ old('google_scholar') }}">
                @if($errors->has('google_scholar'))
                    <div class="invalid-feedback">
                        {{ $errors->first('google_scholar') }}
                    </div>
                @endif
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="citation_number">Citation number</label>
                    <input class="form-control {{ $errors->has('citation_number') ? 'is-invalid' : '' }}" type="number" name="citation_number" id="citation_number" value="{{ old('citation_number') }}">
                    @if($errors->has('citation_number'))
                        <div class="invalid-feedback">
                            {{ $errors->first('citation_number') }}
                        </div>
                    @endif
                </div>
                <div class="form-group col-md-4">
                    <label for="h_index">H-index</label>
                    <input class="form-control {{ $errors->has('h_index') ? 'is-invalid' : '' }}" type="number" name="h_index" id="h_index" value="{{ old('h_index') }}">
                    @if($errors->has('h_index'))
                        <div class="invalid-feedback">
                            {{ $errors->first('h_index') }}
                        </div>
                    @endif
                </div>
                <div class="form-group col-md-4">
                    <label for="scopus_papers">Number of papers (Scopus)</label>
                    <input class="form-control {{ $errors->has('scopus_papers') ? 'is-invalid' : '' }}" type="number" name="scopus_papers" id="scopus_papers" value="{{ old('scopus_papers') }}">
                    @if($errors->has('scopus_papers'))
                        <div class="invalid-feedback">
                            {{ $errors->first('scopus_papers') }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label for="research_gate">Scopus scholar link</label>
                <input class="form-control {{ $errors->has('research_gate') ? 'is-invalid' : '' }}" type="url" name="research_gate" id="research_gate" value="{{ old('research_gate') }}">
                @if($errors->has('research_gate'))
                    <div class="invalid-feedback">
                        {{ $errors->first('research_gate') }}
                    </div>
                @endif
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="sohar_affiliation">Sohar Affiliation</label>
                    <select class="form-control {{ $errors->has('sohar_affiliation') ? 'is-invalid' : '' }}" name="sohar_affiliation" id="sohar_affiliation">
                        <option value="" {{ old('sohar_affiliation', '') === '' ? 'selected' : '' }}>-- Select --</option>
                        <option value="Yes" {{ old('sohar_affiliation') === 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ old('sohar_affiliation') === 'No' ? 'selected' : '' }}>No</option>
                    </select>
                    @if($errors->has('sohar_affiliation'))
                        <div class="invalid-feedback">
                            {{ $errors->first('sohar_affiliation') }}
                        </div>
                    @endif
                </div>
                <div class="form-group col-md-6">
                    <label for="orcid_connected">ORCID Connected</label>
                    <select class="form-control {{ $errors->has('orcid_connected') ? 'is-invalid' : '' }}" name="orcid_connected" id="orcid_connected">
                        <option value="" {{ old('orcid_connected', '') === '' ? 'selected' : '' }}>-- Select --</option>
                        <option value="Yes" {{ old('orcid_connected') === 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ old('orcid_connected') === 'No' ? 'selected' : '' }}>No</option>
                    </select>
                    @if($errors->has('orcid_connected'))
                        <div class="invalid-feedback">
                            {{ $errors->first('orcid_connected') }}
                        </div>
                    @endif
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



@endsection