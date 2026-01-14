<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePublicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:500',
            'publication_type' => 'required|in:journal_paper,conference_paper,book,book_chapter,non_indexed_journal',
            'journal_category' => 'nullable|in:scopus,international_refereed,su_approved_arabic,non_indexed',
            'quartile' => 'nullable|in:Q1,Q2,Q3,Q4',
            'year' => 'required|integer|min:2000|max:' . (now()->year + 1),
            'submission_year' => 'nullable|integer|min:2000|max:' . (now()->year + 1),
            'abstract' => 'nullable|string',
            'authors' => 'nullable|array',
            'authors.*.name' => 'required_with:authors|string',
            'authors.*.email' => 'nullable|email',
            'authors.*.is_primary' => 'nullable|boolean',
            'co_authors' => 'nullable|array',
            'journal_name' => 'nullable|string|max:255',
            'conference_name' => 'nullable|string|max:255',
            'doi' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'published_link' => 'nullable|url|max:500',
            'proceedings_link' => 'nullable|url|max:500',
            'college' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
        ];
    }
}

