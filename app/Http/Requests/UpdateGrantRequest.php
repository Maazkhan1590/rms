<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGrantRequest extends FormRequest
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
            'title' => 'sometimes|required|string|max:500',
            'grant_type' => 'sometimes|required|in:external_grant,external_consultancy,matching_grant,grg_urg,patent_copyright,grant_application',
            'role' => 'sometimes|required|in:PI,Co_PI,Co_I,Advisor_Mentor,Applicant',
            'amount_omr' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'sponsor_name' => 'sometimes|required|string|max:255',
            'sponsor_type' => 'nullable|in:government,private,international,other',
            'reference_code' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'award_year' => 'sometimes|required|integer|min:2000|max:' . (now()->year + 1),
            'submission_year' => 'nullable|integer|min:2000|max:' . (now()->year + 1),
            'summary' => 'nullable|string',
            'matching_grant_moa' => 'nullable|string|max:255',
            'patent_registration_number' => 'nullable|string|max:255',
            'patent_su_registered' => 'nullable|boolean',
        ];
    }
}

