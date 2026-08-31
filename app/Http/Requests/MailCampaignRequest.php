<?php

namespace App\Http\Requests;

use App\Models\MailCampaign;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class MailCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sender_name' => ['required', 'string', 'max:255'],
            'sender_email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'target_type' => ['required', Rule::in([MailCampaign::TARGET_ADDRESS_BOOK, MailCampaign::TARGET_DIRECT])],
            'address_book_ids' => ['nullable', 'required_if:target_type,'.MailCampaign::TARGET_ADDRESS_BOOK, 'array', 'min:1'],
            'address_book_ids.*' => ['integer', 'distinct', Rule::exists('address_books', 'id')->whereNull('deleted_at')],
            'direct_recipients' => ['nullable', 'required_if:target_type,'.MailCampaign::TARGET_DIRECT, 'string'],
            'subscription_status' => ['required', Rule::in(['subscribed', 'unsubscribed'])],
            'content' => ['required', 'string'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:20480'],
            'remove_attachments' => ['nullable', 'array'],
            'remove_attachments.*' => ['integer', 'min:0', 'distinct'],
            'return_url' => ['nullable', 'url'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $campaign = $this->route('mailCampaign');
            $existing = $campaign instanceof MailCampaign ? (array) $campaign->attachments : [];
            $removed = array_unique(array_map('intval', (array) $this->input('remove_attachments', [])));
            $remaining = count(array_filter(array_keys($existing), fn ($index) => ! in_array($index, $removed, true)));
            if ($remaining + count((array) $this->file('attachments', [])) > 5) {
                $validator->errors()->add('attachments', '기존 파일을 포함해 최대 5개까지 등록할 수 있습니다.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'sender_name.required' => '발신자명을 입력해주세요.',
            'sender_email.required' => '발신 이메일을 입력해주세요.',
            'sender_email.email' => '올바른 발신 이메일을 입력해주세요.',
            'subject.required' => '제목을 입력해주세요.',
            'target_type.required' => '발송대상을 선택해주세요.',
            'address_book_ids.required_if' => '주소록을 하나 이상 선택해주세요.',
            'address_book_ids.min' => '주소록을 하나 이상 선택해주세요.',
            'direct_recipients.required_if' => '발송할 이메일을 하나 이상 입력해주세요.',
            'subscription_status.required' => '이메일 수신여부를 선택해주세요.',
            'content.required' => '내용을 입력해주세요.',
        ];
    }
}
