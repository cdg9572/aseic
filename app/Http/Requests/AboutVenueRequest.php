<?php

namespace App\Http\Requests;

use App\Models\AboutVenueDetail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AboutVenueRequest extends FormRequest
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
            'postal_code' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'address_detail' => ['nullable', 'string', 'max:500'],
            'venue_name' => ['nullable', 'string', 'max:255'],
            'venue_description' => ['nullable', 'string'],
            'event_date' => ['nullable', 'string', 'max:255'],
            'format' => ['nullable', Rule::in(array_keys(AboutVenueDetail::formatOptions()))],
            'bus_content' => ['nullable', 'string'],
            'subway_content' => ['nullable', 'string'],
            'taxi_content' => ['nullable', 'string'],
            'return_url' => ['nullable', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'page_title.required' => '제목을 입력해주세요.',
        ];
    }
}
