<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Models\MediaContent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MediaContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_visible' => $this->boolean('is_visible'),
            'remove_image' => $this->boolean('remove_image'),
        ]);
    }

    public function rules(): array
    {
        $type = (string) $this->route('media_type');
        $rules = [
            'is_visible' => ['required', 'boolean'],
            'return_url' => ['nullable', 'url'],
        ];

        if ($type === MediaContent::TYPE_PHOTO_FOLDER) {
            return [...$rules,
                'page_title' => ['required', 'string', 'max:255'],
                'subtitle' => ['nullable', 'string'],
                'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
                'remove_image' => ['boolean'],
            ];
        }

        if ($type === MediaContent::TYPE_PHOTO_ITEM) {
            $content = $this->route('mediaContent');

            return [...$rules,
                'category_id' => $this->categoryRules(Category::GROUP_CODE_PHOTO_GALLERY),
                'title' => ['required', 'string', 'max:255'],
                'image' => [$content instanceof MediaContent && $content->image_path ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
                'remove_image' => ['boolean'],
            ];
        }

        if ($type === MediaContent::TYPE_NEWS_ITEM) {
            return [...$rules,
                'category_id' => $this->categoryRules(Category::GROUP_CODE_NEWS_CLIPPINGS),
                'title' => ['required', 'string', 'max:255'],
                'content' => ['nullable', 'string'],
                'published_date' => ['nullable', 'date'],
                'view_count' => ['nullable', 'integer', 'min:0'],
                'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
                'remove_image' => ['boolean'],
            ];
        }

        abort_unless($type === MediaContent::TYPE_YOUTUBE, 404);

        return [...$rules,
            'page_title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string'],
            'title' => ['required', 'string', 'max:255'],
            'link' => ['required', 'url', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'page_title.required' => (string) $this->route('media_type') === MediaContent::TYPE_PHOTO_FOLDER
                ? '제목을 입력해주세요.'
                : '제목(폴더명)을 입력해주세요.',
            'title.required' => '제목을 입력해주세요.',
            'category_id.required' => '분류를 선택해주세요.',
            'category_id.exists' => '사용할 수 없는 분류입니다.',
            'view_count.integer' => '조회수는 숫자만 입력해주세요.',
            'view_count.min' => '조회수는 0 이상이어야 합니다.',
            'link.required' => '링크를 입력해주세요.',
            'link.url' => '올바른 URL 형식으로 입력해주세요.',
            'image.required' => 'Photo 이미지를 등록해주세요.',
            'image.mimes' => 'JPG 또는 PNG 이미지만 등록할 수 있습니다.',
            'image.max' => '이미지는 5MB 이하만 등록할 수 있습니다.',
        ];
    }

    /** @return array<int, mixed> */
    private function categoryRules(string $groupCode): array
    {
        $groupId = Category::query()
            ->where('code', $groupCode)
            ->where('depth', 0)
            ->whereNull('parent_id')
            ->value('id');

        return [
            'required',
            'integer',
            Rule::exists('categories', 'id')->where(function ($query) use ($groupId): void {
                $query->where('parent_id', $groupId ?? 0)
                    ->where('depth', 1)
                    ->where('is_active', true);
            }),
        ];
    }
}
