<?php

namespace App\Http\Requests;

use App\Models\AddressBook;
use App\Models\AddressBookContact;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddressBookContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var AddressBook|null $addressBook */
        $addressBook = $this->route('addressBook');
        /** @var AddressBookContact|null $contact */
        $contact = $this->route('contact');

        return [
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('address_book_contacts', 'email')
                    ->where(fn ($query) => $query->where('address_book_id', $addressBook?->id))
                    ->ignore($contact?->id),
            ],
            'editing_contact_id' => ['nullable', 'integer'],
            'return_url' => ['nullable', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'contact_name.required' => '이름을 입력해주세요.',
            'contact_email.required' => '이메일을 입력해주세요.',
            'contact_email.email' => '올바른 이메일 형식으로 입력해주세요.',
            'contact_email.unique' => '이미 등록된 이메일입니다.',
        ];
    }
}
