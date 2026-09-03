<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FrontendRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'privacy_agree' => $this->boolean('privacy_agree'),
        ]);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'affiliation' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'attendance_mode' => ['required', Rule::in(['offline', 'online'])],
            'privacy_agree' => ['accepted'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Please enter your first name.',
            'last_name.required' => 'Please enter your last name.',
            'affiliation.required' => 'Please enter your affiliation.',
            'position.required' => 'Please enter your job title or position.',
            'mobile.required' => 'Please enter your mobile number.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'attendance_mode.required' => 'Please select an attendance mode.',
            'privacy_agree.accepted' => 'Please agree to the collection and use of personal information.',
        ];
    }
}
