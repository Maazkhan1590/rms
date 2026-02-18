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
                @if(session('message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <form method="POST" action="{{ route("profile.password.updateProfile") }}" enctype="multipart/form-data">
                    @csrf
                    
                    <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="basic-tab" data-toggle="tab" href="#basic" role="tab">Basic Information</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="academic-tab" data-toggle="tab" href="#academic" role="tab">Academic & Professional</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="research-tab" data-toggle="tab" href="#research" role="tab">Research Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="files-tab" data-toggle="tab" href="#files" role="tab">Files & Documents</a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" id="profileTabsContent">
                        <!-- Basic Information Tab -->
                        <div class="tab-pane fade show active" id="basic" role="tabpanel">
                            <div class="form-group">
                                <label class="required" for="name">{{ trans('cruds.user.fields.name') }}</label>
                                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" required>
                                @if($errors->has('name'))
                                    <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                                @endif
                            </div>

                            <div class="form-group">
                                <label class="required" for="email">{{ trans('cruds.user.fields.email') }}</label>
                                <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" required>
                                @if($errors->has('email'))
                                    <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}" type="text" name="phone" id="phone" value="{{ old('phone', auth()->user()->phone) }}">
                                @if($errors->has('phone'))
                                    <div class="invalid-feedback">{{ $errors->first('phone') }}</div>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="employee_id">Employee ID</label>
                                <input class="form-control {{ $errors->has('employee_id') ? 'is-invalid' : '' }}" type="text" name="employee_id" id="employee_id" value="{{ old('employee_id', auth()->user()->employee_id) }}">
                                @if($errors->has('employee_id'))
                                    <div class="invalid-feedback">{{ $errors->first('employee_id') }}</div>
                                @endif
                            </div>
                        </div>

                        <!-- Academic & Professional Tab -->
                        <div class="tab-pane fade" id="academic" role="tabpanel">
                            <div class="form-group">
                                <label for="college_id">College</label>
                                <select class="form-control {{ $errors->has('college_id') ? 'is-invalid' : '' }}" name="college_id" id="college_id">
                                    <option value="">Select College</option>
                                    @foreach($colleges as $college)
                                        <option value="{{ $college->id }}" {{ old('college_id', auth()->user()->college_id) == $college->id ? 'selected' : '' }}>
                                            {{ $college->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->has('college_id'))
                                    <div class="invalid-feedback">{{ $errors->first('college_id') }}</div>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="department_id">Department</label>
                                <select class="form-control {{ $errors->has('department_id') ? 'is-invalid' : '' }}" name="department_id" id="department_id">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" data-college="{{ $department->college_id }}" {{ old('department_id', auth()->user()->department_id) == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->has('department_id'))
                                    <div class="invalid-feedback">{{ $errors->first('department_id') }}</div>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="designation">Designation</label>
                                <input class="form-control {{ $errors->has('designation') ? 'is-invalid' : '' }}" type="text" name="designation" id="designation" value="{{ old('designation', auth()->user()->designation) }}" placeholder="e.g., Assistant Professor, Associate Professor">
                                @if($errors->has('designation'))
                                    <div class="invalid-feedback">{{ $errors->first('designation') }}</div>
                                @endif
                            </div>
                        </div>

                        <!-- Research Profile Tab -->
                        <div class="tab-pane fade" id="research" role="tabpanel">
                            <h5 class="mb-3">Research Identifiers</h5>
                            <div class="form-group">
                                <label for="orcid">ORCID</label>
                                <input class="form-control {{ $errors->has('orcid') ? 'is-invalid' : '' }}" type="text" name="orcid" id="orcid" value="{{ old('orcid', auth()->user()->orcid) }}" placeholder="0000-0000-0000-0000">
                                @if($errors->has('orcid'))
                                    <div class="invalid-feedback">{{ $errors->first('orcid') }}</div>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="google_scholar">Google Scholar Profile URL</label>
                                <input class="form-control {{ $errors->has('google_scholar') ? 'is-invalid' : '' }}" type="url" name="google_scholar" id="google_scholar" value="{{ old('google_scholar', auth()->user()->google_scholar) }}" placeholder="https://scholar.google.com/...">
                                @if($errors->has('google_scholar'))
                                    <div class="invalid-feedback">{{ $errors->first('google_scholar') }}</div>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="research_gate">ResearchGate Profile URL</label>
                                <input class="form-control {{ $errors->has('research_gate') ? 'is-invalid' : '' }}" type="url" name="research_gate" id="research_gate" value="{{ old('research_gate', auth()->user()->research_gate) }}" placeholder="https://www.researchgate.net/...">
                                @if($errors->has('research_gate'))
                                    <div class="invalid-feedback">{{ $errors->first('research_gate') }}</div>
                                @endif
                            </div>

                            <hr class="my-4">
                            <h5 class="mb-3">Research Metrics</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="citation_number">Total Citations</label>
                                        <input class="form-control {{ $errors->has('citation_number') ? 'is-invalid' : '' }}" type="number" name="citation_number" id="citation_number" value="{{ old('citation_number', auth()->user()->citation_number) }}" min="0">
                                        @if($errors->has('citation_number'))
                                            <div class="invalid-feedback">{{ $errors->first('citation_number') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="h_index">H-Index</label>
                                        <input class="form-control {{ $errors->has('h_index') ? 'is-invalid' : '' }}" type="number" name="h_index" id="h_index" value="{{ old('h_index', auth()->user()->h_index) }}" min="0">
                                        @if($errors->has('h_index'))
                                            <div class="invalid-feedback">{{ $errors->first('h_index') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <h6 class="mt-4 mb-3">Scopus Metrics</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="scopus_citation_number">Scopus Citations</label>
                                        <input class="form-control {{ $errors->has('scopus_citation_number') ? 'is-invalid' : '' }}" type="number" name="scopus_citation_number" id="scopus_citation_number" value="{{ old('scopus_citation_number', auth()->user()->scopus_citation_number) }}" min="0">
                                        @if($errors->has('scopus_citation_number'))
                                            <div class="invalid-feedback">{{ $errors->first('scopus_citation_number') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="scopus_h_index">Scopus H-Index</label>
                                        <input class="form-control {{ $errors->has('scopus_h_index') ? 'is-invalid' : '' }}" type="number" name="scopus_h_index" id="scopus_h_index" value="{{ old('scopus_h_index', auth()->user()->scopus_h_index) }}" min="0">
                                        @if($errors->has('scopus_h_index'))
                                            <div class="invalid-feedback">{{ $errors->first('scopus_h_index') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="scopus_papers">Scopus Papers</label>
                                        <input class="form-control {{ $errors->has('scopus_papers') ? 'is-invalid' : '' }}" type="number" name="scopus_papers" id="scopus_papers" value="{{ old('scopus_papers', auth()->user()->scopus_papers) }}" min="0">
                                        @if($errors->has('scopus_papers'))
                                            <div class="invalid-feedback">{{ $errors->first('scopus_papers') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sohar_affiliation" id="sohar_affiliation" value="1" {{ old('sohar_affiliation', auth()->user()->sohar_affiliation) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sohar_affiliation">
                                        Sohar Affiliation
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Files & Documents Tab -->
                        <div class="tab-pane fade" id="files" role="tabpanel">
                            <div class="form-group">
                                <label for="profile_photo">Profile Photo</label>
                                @if(auth()->user()->profile_photo)
                                    <div class="mb-2">
                                        <img src="{{ Storage::disk('public')->url(auth()->user()->profile_photo) }}" alt="Profile Photo" style="max-width: 150px; max-height: 150px; border-radius: 8px;">
                                        <p class="text-muted small mt-1">Current photo</p>
                                    </div>
                                @endif
                                <input class="form-control-file {{ $errors->has('profile_photo') ? 'is-invalid' : '' }}" type="file" name="profile_photo" id="profile_photo" accept="image/jpeg,image/jpg,image/png">
                                <small class="form-text text-muted">Max size: 2MB. Formats: JPEG, JPG, PNG</small>
                                @if($errors->has('profile_photo'))
                                    <div class="invalid-feedback d-block">{{ $errors->first('profile_photo') }}</div>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="credentials_file">Credentials File</label>
                                @if(auth()->user()->credentials_file)
                                    <div class="mb-2">
                                        <a href="{{ Storage::disk('public')->url(auth()->user()->credentials_file) }}" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fas fa-download"></i> View Current File
                                        </a>
                                        <p class="text-muted small mt-1">Current credentials file</p>
                                    </div>
                                @endif
                                <input class="form-control-file {{ $errors->has('credentials_file') ? 'is-invalid' : '' }}" type="file" name="credentials_file" id="credentials_file" accept=".pdf,.doc,.docx">
                                <small class="form-text text-muted">Max size: 5MB. Formats: PDF, DOC, DOCX</small>
                                @if($errors->has('credentials_file'))
                                    <div class="invalid-feedback d-block">{{ $errors->first('credentials_file') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-save"></i> {{ trans('global.save') }} Profile
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
                        <div style="position: relative;">
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
                        <div style="position: relative;">
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

        // Filter departments based on selected college
        var collegeSelect = document.getElementById('college_id');
        var departmentSelect = document.getElementById('department_id');
        
        if (collegeSelect && departmentSelect) {
            collegeSelect.addEventListener('change', function() {
                var selectedCollege = this.value;
                var options = departmentSelect.querySelectorAll('option');
                
                options.forEach(function(option) {
                    if (option.value === '') {
                        option.style.display = 'block';
                    } else {
                        var collegeId = option.getAttribute('data-college');
                        if (collegeId === selectedCollege || !selectedCollege) {
                            option.style.display = 'block';
                        } else {
                            option.style.display = 'none';
                            if (option.selected) {
                                option.selected = false;
                                departmentSelect.value = '';
                            }
                        }
                    }
                });
            });
        }
    });
</script>
@endsection
