<?php

namespace App\Http\Requests;

use App\Models\AboutPage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class AboutCoOrganizerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'main_page_id' => ['nullable', 'integer', Rule::exists('main_pages', 'id')->whereNull('deleted_at')],
            'page_title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'integer', 'exists:about_co_organizer_items,id'],
            'items.*.logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'items.*.remove_logo' => ['nullable', 'boolean'],
            'items.*.name' => ['nullable', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.url' => ['nullable', 'url', 'max:2048'],
            'return_url' => ['nullable', 'url'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $page = $this->route('aboutPage');
            if (! $page instanceof AboutPage) {
                return;
            }

            $submittedIds = array_values(array_filter(array_map(
                static fn ($item): ?int => filled($item['id'] ?? null) ? (int) $item['id'] : null,
                (array) $this->input('items', []),
            )));
            if ($submittedIds === []) {
                return;
            }

            if ($page->coOrganizerItems()->whereIn('id', $submittedIds)->count() !== count(array_unique($submittedIds))) {
                $validator->errors()->add('items', '공동 주관사 항목이 올바르지 않습니다.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'page_title.required' => '제목을 입력해주세요.',
            'items.required' => '공동 주관사를 1개 이상 입력해주세요.',
            'items.min' => '공동 주관사를 1개 이상 입력해주세요.',
            'items.*.logo.image' => '로고는 이미지 파일만 등록할 수 있습니다.',
            'items.*.logo.mimes' => '로고는 JPG 또는 PNG 파일만 등록할 수 있습니다.',
            'items.*.logo.max' => '로고는 5MB 이하만 등록할 수 있습니다.',
        ];
    }
}
