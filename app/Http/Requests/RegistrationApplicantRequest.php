<?php

namespace App\Http\Requests;

use App\Models\RegistrationApplicant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistrationApplicantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['agreed_privacy' => $this->boolean('agreed_privacy')]);
    }

    public function rules(): array
    {
        return [
            'registration_page_id' => ['required', Rule::exists('registration_pages', 'id')->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:255'],
            'affiliation' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'participation_type' => ['nullable', Rule::in(['offline', 'online'])],
            'status' => ['required', Rule::in([RegistrationApplicant::STATUS_PENDING, RegistrationApplicant::STATUS_APPROVED, RegistrationApplicant::STATUS_CANCELLED])],
            'note' => ['nullable', 'string'],
            'agreed_privacy' => ['required', 'boolean'],
            'return_url' => ['nullable', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'registration_page_id.required' => 'Registration을 선택해주세요.',
            'registration_page_id.exists' => '선택한 Registration을 찾을 수 없습니다.',
            'name.required' => '이름을 입력해주세요.',
            'email.required' => '이메일을 입력해주세요.',
            'email.email' => '올바른 이메일 형식으로 입력해주세요.',
            'participation_type.in' => '올바른 참여 형태를 선택해주세요.',
            'status.required' => '상태를 선택해주세요.',
            'status.in' => '올바른 상태를 선택해주세요.',
        ];
    }
}
