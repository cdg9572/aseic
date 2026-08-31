<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $contacts = (array) $this->input('contacts', []);
        foreach ($contacts as $index => $contact) {
            $contacts[$index]['is_subscribed'] = array_key_exists('is_subscribed', (array) $contact)
                ? $this->boolean('contacts.'.$index.'.is_subscribed')
                : true;
        }
        $this->merge(['contacts' => $contacts]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'contacts' => ['nullable', 'array'],
            'contacts.*.name' => ['nullable', 'required_with:contacts.*.email', 'string', 'max:255'],
            'contacts.*.email' => ['nullable', 'required_with:contacts.*.name', 'email', 'max:255', 'distinct'],
            'contacts.*.is_subscribed' => ['boolean'],
            'import_file' => ['nullable', 'file', 'mimes:csv,txt,xlsx', 'max:10240'],
            'continue_contacts' => ['nullable', 'boolean'],
            'return_url' => ['nullable', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '주소록명을 입력해주세요.',
            'contacts.*.name.required_with' => '연락처 이름과 이메일을 모두 입력해주세요.',
            'contacts.*.email.required_with' => '연락처 이름과 이메일을 모두 입력해주세요.',
            'contacts.*.email.email' => '올바른 이메일 형식으로 입력해주세요.',
            'contacts.*.email.distinct' => '중복된 이메일이 있습니다.',
            'import_file.mimes' => 'CSV 또는 XLSX 파일만 불러올 수 있습니다.',
        ];
    }
}
