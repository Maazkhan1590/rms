<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBonusRecognitionRequest extends FormRequest
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
            'recognition_type' => 'required|in:editorial_board,external_examiner,regulatory_body,workshop_seminar,keynote_plenary,journal_reviewer',
            'title' => 'required|string|max:500',
            'organization' => 'required|string|max:255',
            'role_description' => 'nullable|string',
            'journal_conference_name' => 'nullable|string|max:255',
            'event_name' => 'nullable|string|max:255',
            'event_date' => 'nullable|date',
            'year' => 'nullable|integer|min:2000|max:' . (now()->year + 1),
        ];
    }
}

