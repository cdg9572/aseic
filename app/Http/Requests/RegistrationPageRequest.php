<?php

namespace App\Http\Requests;

use App\Models\RegistrationPage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistrationPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['use_custom_end_text' => $this->boolean('use_custom_end_text')]);
    }

    public function rules(): array
    {
        return [
            'main_page_id' => ['nullable', 'integer', Rule::exists('main_pages', 'id')->whereNull('deleted_at')],
            'page_title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string'],
            'participation_mode' => ['required', Rule::in([RegistrationPage::MODE_PARTICIPATING, RegistrationPage::MODE_NOT_PARTICIPATING])],
            'period_text' => ['nullable', 'required_if:participation_mode,'.RegistrationPage::MODE_PARTICIPATING, 'string', 'max:255'],
            'guide_step_1' => ['nullable', 'string', 'max:255'],
            'guide_step_2' => ['nullable', 'string', 'max:255'],
            'guide_step_3' => ['nullable', 'string', 'max:255'],
            'registration_start_date' => ['nullable', 'date'],
            'registration_end_date' => ['nullable', 'date', 'after_or_equal:registration_start_date'],
            'use_custom_end_text' => ['required', 'boolean'],
            'registration_end_text' => ['nullable', 'required_if:use_custom_end_text,1', 'string', 'max:255'],
            'closed_notice' => ['nullable', 'string'],
            'return_url' => ['nullable', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'page_title.required' => '제목을 입력해주세요.',
            'participation_mode.required' => '참여 또는 미참여를 선택해주세요.',
            'period_text.required_if' => 'Period를 입력해주세요.',
            'registration_end_date.after_or_equal' => '종료일은 시작일 이후여야 합니다.',
            'registration_end_text.required_if' => '직접 입력할 Registration 종료 문구를 입력해주세요.',
        ];
    }
}
