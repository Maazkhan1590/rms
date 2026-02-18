<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        abort_if(Gate::denies('profile_password_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . auth()->id()],
            'phone' => ['nullable', 'string', 'max:20'],
            'designation' => ['nullable', 'string', 'max:255'],
            'employee_id' => ['nullable', 'string', 'max:50'],
            'college_id' => ['nullable', 'exists:colleges,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'orcid' => ['nullable', 'string', 'max:255'],
            'google_scholar' => ['nullable', 'url', 'max:500'],
            'research_gate' => ['nullable', 'url', 'max:500'],
            'citation_number' => ['nullable', 'integer', 'min:0'],
            'h_index' => ['nullable', 'integer', 'min:0'],
            'scopus_citation_number' => ['nullable', 'integer', 'min:0'],
            'scopus_h_index' => ['nullable', 'integer', 'min:0'],
            'scopus_papers' => ['nullable', 'integer', 'min:0'],
            'sohar_affiliation' => ['nullable', 'boolean'],
            'orcid_connected' => ['nullable', 'boolean'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'credentials_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ];
    }
}
