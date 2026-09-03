<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Models\ProgrammePage;
use App\Models\ProgrammePageBook;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ProgrammePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sessions = (array) $this->input('sessions', []);
        $sessions[1]['is_active'] = $this->boolean('sessions.1.is_active');
        $books = (array) $this->input('books', []);
        foreach ($books as $index => $book) {
            $books[$index]['remove_file'] = $this->boolean('books.'.$index.'.remove_file');
        }
        $this->merge([
            'sessions' => $sessions,
            'books' => $books,
        ]);
    }

    public function rules(): array
    {
        $type = (string) $this->route('programme_type');
        $rules = [
            'main_page_id' => ['nullable', 'integer', Rule::exists('main_pages', 'id')->whereNull('deleted_at')],
            'page_title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string'],
            'return_url' => ['nullable', 'url'],
        ];
        $categoryGroupCode = match ($type) {
            ProgrammePage::TYPE_ARCHIVE_THEME => Category::GROUP_CODE_ARCHIVE_THEME,
            ProgrammePage::TYPE_ARCHIVE_PROGRAMME => Category::GROUP_CODE_ARCHIVE_PROGRAMME,
            default => null,
        };
        if ($categoryGroupCode !== null) {
            $rules['category_id'] = $this->categoryRules($categoryGroupCode);
        }

        if (in_array($type, [
            ProgrammePage::TYPE_THEME,
            ProgrammePage::TYPE_ARCHIVE_THEME,
            ProgrammePage::TYPE_ARCHIVE_PROGRAMME,
        ], true)) {
            return [...$rules,
                'title' => ['nullable', 'string'],
                'location' => ['nullable', 'string', 'max:255'],
                'event_date' => ['nullable', 'string', 'max:255'],
                'content' => ['nullable', 'string'],
            ];
        }

        if (in_array($type, [
            ProgrammePage::TYPE_PROGRAMME,
            ProgrammePage::TYPE_ARCHIVE_LEGACY,
        ], true)) {
            return [...$rules,
                'content' => ['nullable', 'string'],
            ];
        }

        if (in_array($type, [ProgrammePage::TYPE_SPEAKERS, ProgrammePage::TYPE_ARCHIVE_SPEAKERS], true)) {
            return [...$rules,
                'sessions' => ['required', 'array', 'size:2'],
                'sessions.*' => ['array'],
                'sessions.*.session_name' => ['nullable', 'string', 'max:255'],
                'sessions.*.is_active' => ['boolean'],
                'sessions.*.speaker_ids' => ['nullable', 'array'],
                'sessions.*.speaker_ids.*' => ['integer', 'distinct', Rule::exists('speakers', 'id')->whereNull('deleted_at')],
            ];
        }

        abort_unless($type === ProgrammePage::TYPE_BOOK, 404);

        return [...$rules,
            'books' => ['required', 'array', 'min:1'],
            'books.*.id' => ['nullable', 'integer', Rule::exists('programme_page_books', 'id')],
            'books.*.title' => ['nullable', 'string', 'max:255'],
            'books.*.file' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,hwp,zip', 'max:20480'],
            'books.*.link' => ['nullable', 'url', 'max:2048'],
            'books.*.remove_file' => ['boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $type = (string) $this->route('programme_type');

            if (in_array($type, [ProgrammePage::TYPE_SPEAKERS, ProgrammePage::TYPE_ARCHIVE_SPEAKERS], true)) {
                $speakerSessions = collect((array) $this->input('sessions', []));
                $speakerOccurrences = [];

                $speakerSessions->each(function ($session, int $sessionIndex) use (&$speakerOccurrences): void {
                    if (! is_array($session)) {
                        return;
                    }

                    foreach ((array) ($session['speaker_ids'] ?? []) as $speakerId) {
                        $speakerOccurrences[(int) $speakerId][] = $sessionIndex;
                    }
                });

                foreach ($speakerOccurrences as $sessionIndexes) {
                    $uniqueSessionIndexes = array_unique($sessionIndexes);
                    if (count($uniqueSessionIndexes) < 2) {
                        continue;
                    }

                    foreach ($uniqueSessionIndexes as $sessionIndex) {
                        $validator->errors()->add(
                            'sessions.'.$sessionIndex.'.speaker_ids',
                            '동일한 Speaker는 DAY 1과 DAY 2에 중복 선택할 수 없습니다.',
                        );
                    }
                }
            }

            if ($type !== ProgrammePage::TYPE_BOOK) {
                return;
            }

            $page = $this->route('programmePage');
            if (! $page instanceof ProgrammePage) {
                return;
            }

            $submittedIds = collect((array) $this->input('books', []))
                ->pluck('id')
                ->filter()
                ->map(fn ($id): int => (int) $id)
                ->unique();

            if ($submittedIds->isEmpty()) {
                return;
            }

            $ownedCount = ProgrammePageBook::query()
                ->where('programme_page_id', $page->id)
                ->whereIn('id', $submittedIds)
                ->count();

            if ($ownedCount !== $submittedIds->count()) {
                $validator->errors()->add('books', '수정할 수 없는 Programme Book 항목이 포함되어 있습니다.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'page_title.required' => '제목을 입력해주세요.',
            'category_id.required' => '분류를 선택해주세요.',
            'category_id.exists' => '사용할 수 없는 분류입니다.',
            'sessions.size' => 'DAY 1과 DAY 2 세션 정보를 모두 전달해야 합니다.',
            'sessions.*.speaker_ids.*.distinct' => '동일한 Speaker는 중복 선택할 수 없습니다.',
            'sessions.*.speaker_ids.*.exists' => '선택한 Speaker를 찾을 수 없습니다.',
            'books.required' => 'Programme Book 항목을 한 개 이상 등록해주세요.',
            'books.min' => 'Programme Book 항목을 한 개 이상 등록해주세요.',
            'books.*.file.mimes' => 'Programme Book은 PDF, DOC, DOCX, PPT, PPTX, HWP, ZIP 파일만 등록할 수 있습니다.',
            'books.*.file.max' => 'Programme Book 파일은 20MB 이하만 등록할 수 있습니다.',
            'books.*.link.url' => 'Link는 올바른 URL 형식으로 입력해주세요.',
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
