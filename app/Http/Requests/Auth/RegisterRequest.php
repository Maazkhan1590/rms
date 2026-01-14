<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            // Step 1: Personal Information
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            
            // Step 2: Academic Information
            'department' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'employee_id' => ['required', 'string', 'unique:users,employee_id'],
            'orcid' => ['nullable', 'string', 'max:19'],
            'google_scholar' => ['nullable', 'url', 'max:255'],
            'research_gate' => ['nullable', 'url', 'max:255'],
            
            // Step 3: Verification & Password
            'password' => ['required', 'string', 'confirmed', Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
            ],
            'credentials' => ['required', 'file', 'mimes:pdf', 'max:2048'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your full name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'phone.required' => 'Please enter your phone number.',
            
            'department.required' => 'Please select your department.',
            'designation.required' => 'Please enter your designation.',
            'employee_id.required' => 'Please enter your employee ID.',
            'employee_id.unique' => 'This employee ID is already registered.',
            
            'password.required' => 'Please create a password.',
            'password.confirmed' => 'Password confirmation does not match.',
            'credentials.required' => 'Please upload your credentials document.',
            'credentials.mimes' => 'Credentials must be a PDF file.',
            'credentials.max' => 'Credentials file must not exceed 2MB.',
            'profile_photo.image' => 'Profile photo must be an image.',
            'profile_photo.max' => 'Profile photo must not exceed 1MB.',
        ];
    }

    /**
     * Get custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'employee_id' => 'employee ID',
            'orcid' => 'ORCID',
            'google_scholar' => 'Google Scholar URL',
            'research_gate' => 'ResearchGate URL',
        ];
    }
}
