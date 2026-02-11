@extends('layouts.admin')

@section('page-title', 'Edit Publication')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Publication</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('faculty.publications.update', $publication) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="title">Publication Title <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            class="form-control @error('title') is-invalid @enderror"
                            id="title"
                            name="title"
                            value="{{ old('title', $publication->title) }}"
                            required
                        >
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="publication_type">Publication Type <span class="text-danger">*</span></label>
                        <select
                            class="form-control @error('publication_type') is-invalid @enderror"
                            id="publication_type"
                            name="publication_type"
                            required
                        >
                            <option value="">Select Type</option>
                            <option value="journal_paper" {{ old('publication_type', $publication->publication_type) == 'journal_paper' ? 'selected' : '' }}>Journal Paper</option>
                            <option value="conference_paper" {{ old('publication_type', $publication->publication_type) == 'conference_paper' ? 'selected' : '' }}>Conference Paper</option>
                            <option value="book" {{ old('publication_type', $publication->publication_type) == 'book' ? 'selected' : '' }}>Book</option>
                            <option value="book_chapter" {{ old('publication_type', $publication->publication_type) == 'book_chapter' ? 'selected' : '' }}>Book Chapter</option>
                            <option value="non_indexed_journal" {{ old('publication_type', $publication->publication_type) == 'non_indexed_journal' ? 'selected' : '' }}>Non-Indexed Journal</option>
                        </select>
                        @error('publication_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="year">Publication Year <span class="text-danger">*</span></label>
                        <input
                            type="number"
                            class="form-control @error('year') is-invalid @enderror"
                            id="year"
                            name="year"
                            value="{{ old('year', $publication->year ?? date('Y')) }}"
                            min="2000"
                            max="{{ date('Y') + 1 }}"
                            required
                        >
                        @error('year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6" id="journal_category_group">
                    <div class="form-group">
                        <label for="journal_category">Journal Category</label>
                        <select
                            class="form-control @error('journal_category') is-invalid @enderror"
                            id="journal_category"
                            name="journal_category"
                        >
                            <option value="">Select Category</option>
                            <option value="scopus" {{ old('journal_category', $publication->journal_category) == 'scopus' ? 'selected' : '' }}>Scopus</option>
                            <option value="international_refereed" {{ old('journal_category', $publication->journal_category) == 'international_refereed' ? 'selected' : '' }}>International Refereed</option>
                            <option value="su_approved_arabic" {{ old('journal_category', $publication->journal_category) == 'su_approved_arabic' ? 'selected' : '' }}>SU Approved Arabic</option>
                            <option value="non_indexed" {{ old('journal_category', $publication->journal_category) == 'non_indexed' ? 'selected' : '' }}>Non-Indexed</option>
                        </select>
                        @error('journal_category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6" id="quartile_group">
                    <div class="form-group">
                        <label for="quartile">Quartile (for indexed journals)</label>
                        <select
                            class="form-control @error('quartile') is-invalid @enderror"
                            id="quartile"
                            name="quartile"
                        >
                            <option value="">Select Quartile</option>
                            <option value="Q1" {{ old('quartile', $publication->quartile) == 'Q1' ? 'selected' : '' }}>Q1</option>
                            <option value="Q2" {{ old('quartile', $publication->quartile) == 'Q2' ? 'selected' : '' }}>Q2</option>
                            <option value="Q3" {{ old('quartile', $publication->quartile) == 'Q3' ? 'selected' : '' }}>Q3</option>
                            <option value="Q4" {{ old('quartile', $publication->quartile) == 'Q4' ? 'selected' : '' }}>Q4</option>
                        </select>
                        @error('quartile')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6" id="journal_name_group">
                    <div class="form-group">
                        <label for="journal_name">Journal Name</label>
                        <input
                            type="text"
                            class="form-control @error('journal_name') is-invalid @enderror"
                            id="journal_name"
                            name="journal_name"
                            value="{{ old('journal_name', $publication->journal_name) }}"
                        >
                        @error('journal_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6" id="conference_name_group" style="display: none;">
                    <div class="form-group">
                        <label for="conference_name">Conference Name</label>
                        <input
                            type="text"
                            class="form-control @error('conference_name') is-invalid @enderror"
                            id="conference_name"
                            name="conference_name"
                            value="{{ old('conference_name', $publication->conference_name) }}"
                        >
                        @error('conference_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="doi">DOI</label>
                        <input
                            type="text"
                            class="form-control @error('doi') is-invalid @enderror"
                            id="doi"
                            name="doi"
                            value="{{ old('doi', $publication->doi) }}"
                        >
                        @error('doi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="isbn">ISBN (for books/chapters)</label>
                        <input
                            type="text"
                            class="form-control @error('isbn') is-invalid @enderror"
                            id="isbn"
                            name="isbn"
                            value="{{ old('isbn', $publication->isbn) }}"
                        >
                        @error('isbn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="abstract">Abstract</label>
                        <textarea
                            class="form-control @error('abstract') is-invalid @enderror"
                            id="abstract"
                            name="abstract"
                            rows="5"
                        >{{ old('abstract', $publication->abstract) }}</textarea>
                        @error('abstract')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="published_link">Published Link</label>
                        <input
                            type="url"
                            class="form-control @error('published_link') is-invalid @enderror"
                            id="published_link"
                            name="published_link"
                            value="{{ old('published_link', $publication->published_link) }}"
                            placeholder="https://..."
                        >
                        @error('published_link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="proceedings_link">Proceedings Link</label>
                        <input
                            type="url"
                            class="form-control @error('proceedings_link') is-invalid @enderror"
                            id="proceedings_link"
                            name="proceedings_link"
                            value="{{ old('proceedings_link', $publication->proceedings_link) }}"
                            placeholder="https://..."
                        >
                        @error('proceedings_link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary">
                    <span class="material-icons-outlined" style="font-size:18px;vertical-align:middle;">save</span>
                    <span style="vertical-align: middle;">Update Publication</span>
                </button>
                <a href="{{ route('faculty.publications.show', $publication) }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Show/hide fields based on publication type
    $('#publication_type').on('change', function() {
        const type = $(this).val();

        // Show/hide journal fields
        if (type === 'journal_paper' || type === 'non_indexed_journal') {
            $('#journal_name_group').show();
            $('#journal_category_group').show();
            $('#quartile_group').show();
            $('#conference_name_group').hide();
        } else if (type === 'conference_paper') {
            $('#conference_name_group').show();
            $('#journal_name_group').hide();
            $('#journal_category_group').hide();
            $('#quartile_group').hide();
        } else {
            $('#journal_name_group').hide();
            $('#journal_category_group').hide();
            $('#quartile_group').hide();
            $('#conference_name_group').hide();
        }
    });

    // Trigger on page load
    $('#publication_type').trigger('change');
</script>
@endpush
@endsection

