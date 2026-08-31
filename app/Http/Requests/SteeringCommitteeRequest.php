<?php

namespace App\Http\Requests;

use App\Models\HomepagePartner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SteeringCommitteeRequest extends FormRequest
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
            'organized_ids' => ['nullable', 'array'],
            'organized_ids.*' => ['integer', 'distinct', Rule::exists('homepage_partners', 'id')->whereNull('deleted_at')],
            'partnership_ids' => ['nullable', 'array'],
            'partnership_ids.*' => ['integer', 'distinct', Rule::exists('homepage_partners', 'id')->whereNull('deleted_at')],
            'return_url' => ['nullable', 'url'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $this->validatePartnerTypes($validator, 'organized_ids', HomepagePartner::TYPE_ORGANIZED);
            $this->validatePartnerTypes($validator, 'partnership_ids', HomepagePartner::TYPE_PARTNERSHIP);
        });
    }

    public function messages(): array
    {
        return [
            'page_title.required' => '제목을 입력해주세요.',
        ];
    }

    private function validatePartnerTypes(Validator $validator, string $field, string $type): void
    {
        $ids = array_map('intval', (array) $this->input($field, []));
        if ($ids === []) {
            return;
        }

        if (HomepagePartner::query()->whereIn('id', $ids)->where('type', $type)->count() !== count(array_unique($ids))) {
            $validator->errors()->add($field, '선택한 항목의 분류가 올바르지 않습니다.');
        }
    }
}
