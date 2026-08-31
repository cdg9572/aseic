<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AboutForumRequest extends FormRequest
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
            'overview' => ['nullable', 'string'],
            'forums_since_2015' => ['nullable', 'string', 'max:255', 'regex:/^[0-9+]+$/'],
            'participants' => ['nullable', 'string', 'max:255', 'regex:/^[0-9+]+$/'],
            'countries' => ['nullable', 'string', 'max:255', 'regex:/^[0-9+]+$/'],
            'organizations' => ['nullable', 'string', 'max:255', 'regex:/^[0-9+]+$/'],
            'backgrounds' => ['nullable', 'array', 'size:4'],
            'backgrounds.*.title' => ['nullable', 'string', 'max:255'],
            'backgrounds.*.content' => ['nullable', 'string'],
            'objectives' => ['nullable', 'array', 'size:3'],
            'objectives.*.title' => ['nullable', 'string', 'max:255'],
            'objectives.*.content' => ['nullable', 'string'],
            'return_url' => ['nullable', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'page_title.required' => '제목을 입력해주세요.',
            'forums_since_2015.regex' => 'Forums Since 2015는 숫자와 +만 입력할 수 있습니다.',
            'participants.regex' => 'Participants는 숫자와 +만 입력할 수 있습니다.',
            'countries.regex' => 'Countries는 숫자와 +만 입력할 수 있습니다.',
            'organizations.regex' => 'Organizations는 숫자와 +만 입력할 수 있습니다.',
            'backgrounds.size' => 'Background는 4개 항목으로 구성해야 합니다.',
            'objectives.size' => 'Objectives는 3개 항목으로 구성해야 합니다.',
        ];
    }
}
