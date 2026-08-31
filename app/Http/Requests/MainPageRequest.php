<?php

namespace App\Http\Requests;

use App\Models\AboutPage;
use App\Models\MainPage;
use App\Models\MainPageLink;
use App\Models\ProgrammePage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class MainPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_visible' => $this->boolean('is_visible'),
            'use_custom_event_date' => $this->boolean('use_custom_event_date'),
            'remove_programme_background' => $this->boolean('remove_programme_background'),
            'remove_register_background' => $this->boolean('remove_register_background'),
        ]);
    }

    public function rules(): array
    {
        $mainPage = $this->route('mainPage');
        $isUpdate = $mainPage instanceof MainPage;

        $folderRules = ['string', 'max:120', 'regex:/^[A-Za-z0-9_-]+$/', Rule::notIn(['default'])];
        $folderRules = $isUpdate
            ? array_merge(['prohibited'], $folderRules)
            : array_merge(['required'], $folderRules, [Rule::unique('main_pages', 'folder_name')]);

        return [
            'is_visible' => ['required', 'boolean'],
            'folder_name' => $folderRules,
            'event_name' => ['required', 'string', 'max:255'],
            'event_start_date' => ['nullable', 'required_unless:use_custom_event_date,1', 'date'],
            'event_end_date' => ['nullable', 'required_unless:use_custom_event_date,1', 'date', 'after_or_equal:event_start_date'],
            'use_custom_event_date' => ['required', 'boolean'],
            'event_date_text' => ['nullable', 'required_if:use_custom_event_date,1', 'string', 'max:255'],
            'banner_id' => ['nullable', Rule::exists('banners', 'id')->whereNull('deleted_at')],
            'popup_id' => ['nullable', Rule::exists('popups', 'id')->whereNull('deleted_at')],
            'speaker_ids' => ['nullable', 'array'],
            'speaker_ids.*' => ['integer', 'distinct', Rule::exists('speakers', 'id')->whereNull('deleted_at')],
            'programme_background' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'remove_programme_background' => ['boolean'],
            'programme_items' => ['required', 'array', 'size:4'],
            'programme_items.*.time' => ['nullable', 'string', 'max:100'],
            'programme_items.*.subject' => ['nullable', 'string', 'max:255'],
            'programme_items.*.content' => ['nullable', 'string', 'max:500'],
            'register_background' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'remove_register_background' => ['boolean'],
            'past_forum_video_url' => ['nullable', 'url', 'max:2048'],
            'host_images' => ['nullable', 'array', 'max:5'],
            'host_images.*' => ['image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'organizer_images' => ['nullable', 'array', 'max:5'],
            'organizer_images.*' => ['image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'co_organizer_images' => ['nullable', 'array', 'max:5'],
            'co_organizer_images.*' => ['image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'remove_host_images' => ['nullable', 'array'],
            'remove_host_images.*' => ['integer', 'min:0', 'distinct'],
            'remove_organizer_images' => ['nullable', 'array'],
            'remove_organizer_images.*' => ['integer', 'min:0', 'distinct'],
            'remove_co_organizer_images' => ['nullable', 'array'],
            'remove_co_organizer_images.*' => ['integer', 'min:0', 'distinct'],
            'links' => ['nullable', 'array'],
            'links.*' => ['nullable', 'integer', 'min:1'],
            'footer_text' => ['nullable', 'string', 'max:500'],
            'return_url' => ['nullable', 'url'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $mainPage = $this->route('mainPage');
            if ($mainPage instanceof MainPage) {
                $this->validateImageLimit($validator, 'host_images', 'remove_host_images', $mainPage->host_image_files);
                $this->validateImageLimit($validator, 'organizer_images', 'remove_organizer_images', $mainPage->organizer_image_files);
                $this->validateImageLimit($validator, 'co_organizer_images', 'remove_co_organizer_images', $mainPage->co_organizer_image_files);
            }

            foreach ((array) $this->input('links', []) as $slot => $contentId) {
                if ($contentId === null || $contentId === '') {
                    continue;
                }

                $modelClass = MainPageLink::modelMap()[$slot] ?? null;
                if ($modelClass === null) {
                    $validator->errors()->add("links.{$slot}", '해당 메뉴는 아직 연결할 콘텐츠가 없습니다.');

                    continue;
                }

                $query = $modelClass::query();
                if ($modelClass === AboutPage::class) {
                    $expectedType = MainPageLink::aboutPageTypeMap()[$slot] ?? null;
                    if ($expectedType !== null) {
                        $query->where('type', $expectedType);
                    }
                }
                if ($modelClass === ProgrammePage::class) {
                    $expectedType = MainPageLink::programmePageTypeMap()[$slot] ?? null;
                    if ($expectedType !== null) {
                        $query->where('type', $expectedType);
                    }
                }

                if (! $query->whereKey((int) $contentId)->exists()) {
                    $validator->errors()->add("links.{$slot}", '선택한 콘텐츠를 찾을 수 없습니다.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'folder_name.required' => '연도(폴더명)를 입력해주세요.',
            'folder_name.regex' => '연도(폴더명)는 공백과 한글 없이 영문, 숫자, 하이픈, 밑줄만 사용할 수 있습니다.',
            'folder_name.unique' => '이미 사용 중인 연도(폴더명)입니다.',
            'folder_name.not_in' => 'default는 기본 템플릿 폴더명으로 사용할 수 없습니다.',
            'folder_name.prohibited' => '연도(폴더명)는 생성 후 수정할 수 없습니다.',
            'event_name.required' => '행사명을 입력해주세요.',
            'event_start_date.required_unless' => '행사 시작일을 입력하거나 직접입력을 선택해주세요.',
            'event_end_date.required_unless' => '행사 종료일을 입력하거나 직접입력을 선택해주세요.',
            'event_end_date.after_or_equal' => '행사 종료일은 시작일 이후여야 합니다.',
            'event_date_text.required_if' => '직접 입력할 행사일시를 입력해주세요.',
            'programme_items.size' => 'Programme은 4개 항목으로 구성해야 합니다.',
            'past_forum_video_url.url' => '유튜브 링크는 올바른 URL 형식으로 입력해주세요.',
            'programme_background.image' => '이미지 파일만 등록할 수 있습니다.',
            'programme_background.mimes' => 'JPG 또는 PNG 이미지만 등록할 수 있습니다.',
            'programme_background.max' => '이미지는 5MB 이하만 등록할 수 있습니다.',
            'register_background.image' => '이미지 파일만 등록할 수 있습니다.',
            'register_background.mimes' => 'JPG 또는 PNG 이미지만 등록할 수 있습니다.',
            'register_background.max' => '이미지는 5MB 이하만 등록할 수 있습니다.',
            '*.image' => '이미지 파일만 등록할 수 있습니다.',
            '*.mimes' => 'JPG 또는 PNG 이미지만 등록할 수 있습니다.',
            '*.max' => '이미지는 5MB 이하만 등록할 수 있습니다.',
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $existingFiles
     */
    private function validateImageLimit(
        Validator $validator,
        string $fileField,
        string $removeField,
        array $existingFiles,
    ): void {
        $removedIndexes = array_unique(array_map('intval', (array) $this->input($removeField, [])));
        $removedCount = count(array_filter(
            $removedIndexes,
            static fn (int $index): bool => array_key_exists($index, $existingFiles),
        ));
        $newCount = count((array) $this->file($fileField, []));

        if (count($existingFiles) - $removedCount + $newCount > 5) {
            $validator->errors()->add($fileField, '기존 이미지를 포함해 최대 5개까지 등록할 수 있습니다.');
        }
    }
}
