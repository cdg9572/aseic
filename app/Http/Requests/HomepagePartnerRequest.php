<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HomepagePartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'is_image_visible' => $this->boolean('is_image_visible'),
            'remove_profile_image' => $this->boolean('remove_profile_image'),
        ]);
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'position' => ['nullable', 'string', 'max:255'],
            'affiliation' => ['nullable', 'string', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:2048'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'is_active' => ['required', 'boolean'],
            'is_image_visible' => ['required', 'boolean'],
            'content' => ['nullable', 'string'],
            'remove_profile_image' => ['boolean'],
            'return_url' => ['nullable', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name을 입력해주세요.',
            'last_name.required' => 'Last name을 입력해주세요.',
            'linkedin_url.url' => 'LinkedIn 링크는 올바른 URL 형식으로 입력해주세요.',
            'profile_image.image' => '프로필은 이미지 파일만 등록할 수 있습니다.',
            'profile_image.mimes' => '프로필은 JPG 또는 PNG 파일만 등록할 수 있습니다.',
            'profile_image.max' => '프로필 이미지는 5MB 이하만 등록할 수 있습니다.',
        ];
    }
}
