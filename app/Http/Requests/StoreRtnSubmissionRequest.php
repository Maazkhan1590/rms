<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRtnSubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'rtn_type' => 'required|in:RTN_3,RTN_4',
            'title' => 'required|string|max:500',
            'description' => 'required|string',
            'year' => 'nullable|integer|min:2000|max:' . (now()->year + 1),
            'student_coauthors' => 'nullable|array',
            'student_coauthors.*.name' => 'required_with:student_coauthors|string',
            'student_coauthors.*.email' => 'nullable|email',
            'course_files_updated' => 'nullable|array',
            'course_files_updated.*.course_code' => 'required_with:course_files_updated|string',
            'course_files_updated.*.course_name' => 'required_with:course_files_updated|string',
            'course_files_updated.*.file_path' => 'nullable|string',
            'lecture_materials' => 'nullable|string',
            'assessment_redesign' => 'nullable|string',
            'case_study_documentation' => 'nullable|string',
        ];
    }
}

