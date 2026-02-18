@extends('layouts.public')

@section('title', 'Propose Adjunct Professor | Academic Research Portal')

@section('content')
<style>
    .auth-container {
        max-width: 1000px !important;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-error {
        color: var(--danger);
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }
    .section-divider {
        border-top: 2px solid #e5e7eb;
        margin: 2rem 0;
        padding-top: 2rem;
    }
</style>

<section class="auth-section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-header">
                <h1>Propose Adjunct Professor</h1>
                <p>Fill in the details below to propose a new adjunct professor</p>
            </div>
            <form method="POST" action="{{ route('adjunct-professors.store') }}" class="auth-form">
                @csrf

                @if ($errors->any())
                    <div style="background: #fee; color: #c33; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        <strong>Please fix the following errors:</strong>
                        <ul style="margin: 0.5rem 0 0 1.5rem;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group">
                    <label for="name">
                        Name <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="text" class="form-control" id="name" name="name" required 
                           value="{{ old('name') }}">
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="{{ old('email') }}">
                </div>

                <div class="section-divider">
                    <h3 style="margin-bottom: 1.5rem; color: #111827;">Google Scholar Information</h3>
                </div>

                <div class="form-group">
                    <label for="google_scholar">Google Scholar Profile URL</label>
                    <input type="url" class="form-control" id="google_scholar" name="google_scholar"
                           value="{{ old('google_scholar') }}" placeholder="https://scholar.google.com/...">
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="gs_citation_number">Citation Number</label>
                            <input type="number" class="form-control" id="gs_citation_number" name="gs_citation_number" 
                                   min="0" value="{{ old('gs_citation_number') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="gs_h_index">H-Index</label>
                            <input type="number" class="form-control" id="gs_h_index" name="gs_h_index" 
                                   min="0" value="{{ old('gs_h_index') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="gs_papers_2025">Papers in 2025</label>
                            <input type="number" class="form-control" id="gs_papers_2025" name="gs_papers_2025" 
                                   min="0" value="{{ old('gs_papers_2025') }}">
                        </div>
                    </div>
                </div>

                <div class="section-divider">
                    <h3 style="margin-bottom: 1.5rem; color: #111827;">Scopus Information</h3>
                </div>

                <div class="form-group">
                    <label for="scopus_scholar">Scopus Profile URL</label>
                    <input type="url" class="form-control" id="scopus_scholar" name="scopus_scholar"
                           value="{{ old('scopus_scholar') }}" placeholder="https://www.scopus.com/...">
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="scopus_citation_number">Citation Number</label>
                            <input type="number" class="form-control" id="scopus_citation_number" name="scopus_citation_number" 
                                   min="0" value="{{ old('scopus_citation_number') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="scopus_h_index">H-Index</label>
                            <input type="number" class="form-control" id="scopus_h_index" name="scopus_h_index" 
                                   min="0" value="{{ old('scopus_h_index') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="scopus_papers_2025">Papers in 2025</label>
                            <input type="number" class="form-control" id="scopus_papers_2025" name="scopus_papers_2025" 
                                   min="0" value="{{ old('scopus_papers_2025') }}">
                        </div>
                    </div>
                </div>

                <div class="section-divider">
                    <h3 style="margin-bottom: 1.5rem; color: #111827;">Additional Information</h3>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="publication_with_sohar">Publications with Sohar</label>
                            <input type="number" class="form-control" id="publication_with_sohar" name="publication_with_sohar" 
                                   min="0" value="{{ old('publication_with_sohar') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="appointment_from">Appointment From</label>
                            <input type="date" class="form-control" id="appointment_from" name="appointment_from" 
                                   value="{{ old('appointment_from') }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa fa-paper-plane"></i> Submit Proposal
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
