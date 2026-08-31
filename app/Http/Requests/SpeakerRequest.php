<?php

namespace App\Http\Requests;

use App\Models\Speaker;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SpeakerRequest extends FormRequest
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
            'presentation_subject' => ['nullable', 'string', 'max:500'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'role' => ['required', Rule::in(array_keys(Speaker::roleOptions()))],
            'is_active' => ['required', 'boolean'],
            'is_image_visible' => ['required', 'boolean'],
            'content' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,ppt,pptx,hwp,zip',
                'max:20480',
            ],
            'remove_profile_image' => ['boolean'],
            'remove_attachments' => ['nullable', 'array'],
            'remove_attachments.*' => ['integer', 'min:0', 'distinct'],
            'return_url' => ['nullable', 'url'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $speaker = $this->route('speaker');
            if (! $speaker instanceof Speaker) {
                return;
            }

            $existingFiles = $speaker->attachment_files;
            $removedIndexes = array_unique(array_map('intval', (array) $this->input('remove_attachments', [])));
            $removedCount = count(array_filter(
                $removedIndexes,
                static fn (int $index): bool => array_key_exists($index, $existingFiles),
            ));
            $newFilesCount = count((array) $this->file('attachments', []));

            if (count($existingFiles) - $removedCount + $newFilesCount > 5) {
                $validator->errors()->add('attachments', '첨부파일은 기존 파일을 포함해 최대 5개까지 등록할 수 있습니다.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name을 입력해주세요.',
            'last_name.required' => 'Last name을 입력해주세요.',
            'role.required' => '역할을 선택해주세요.',
            'role.in' => '올바른 역할을 선택해주세요.',
            'profile_image.image' => '프로필은 이미지 파일만 등록할 수 있습니다.',
            'profile_image.mimes' => '프로필은 JPG 또는 PNG 파일만 등록할 수 있습니다.',
            'profile_image.max' => '프로필 이미지는 5MB 이하만 등록할 수 있습니다.',
            'attachments.max' => '첨부파일은 최대 5개까지 등록할 수 있습니다.',
            'attachments.*.mimes' => '지원하지 않는 첨부파일 형식입니다.',
            'attachments.*.max' => '각 첨부파일은 20MB 이하만 등록할 수 있습니다.',
        ];
    }
}
