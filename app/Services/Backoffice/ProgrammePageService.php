<?php

namespace App\Services\Backoffice;

use App\Models\MainPage;
use App\Models\MainPageLink;
use App\Models\ProgrammePage;
use App\Models\ProgrammePageBook;
use App\Models\Speaker;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProgrammePageService
{
    public function __construct(private readonly MainPageService $mainPageService) {}

    /** @param array<string, mixed> $filters */
    public function getPages(string $type, array $filters, int $perPage): LengthAwarePaginator
    {
        $query = ProgrammePage::query()
            ->with(['creator:id,name', 'books', 'mainPageLink.mainPage:id,folder_name,event_name'])
            ->where('type', $type);

        if (($filters['is_linked'] ?? '') !== '') {
            $filters['is_linked'] === '1'
                ? $query->whereHas('mainPageLink')
                : $query->whereDoesntHave('mainPageLink');
        }

        if (! empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }
        if (! empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }
        if (! empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($keywordQuery) use ($keyword): void {
                $keywordQuery->where('page_title', 'like', '%'.$keyword.'%')
                    ->orWhere('title', 'like', '%'.$keyword.'%')
                    ->orWhere('subtitle', 'like', '%'.$keyword.'%')
                    ->orWhere('book_title', 'like', '%'.$keyword.'%')
                    ->orWhereHas('books', function ($bookQuery) use ($keyword): void {
                        $bookQuery->where('title', 'like', '%'.$keyword.'%')
                            ->orWhere('link', 'like', '%'.$keyword.'%')
                            ->orWhere('file_name', 'like', '%'.$keyword.'%');
                    })
                    ->orWhereHas('mainPageLink.mainPage', function ($mainPageQuery) use ($keyword): void {
                        $mainPageQuery->where('folder_name', 'like', '%'.$keyword.'%')
                            ->orWhere('event_name', 'like', '%'.$keyword.'%');
                    });
            });
        }

        return $query->latest('id')->paginate($perPage)->withQueryString();
    }

    /** @return Collection<int, MainPage> */
    public function mainPageOptions(): Collection
    {
        return MainPage::query()->latest('id')->get(['id', 'folder_name', 'event_name']);
    }

    /** @return Collection<int, Speaker> */
    public function speakerOptions(): Collection
    {
        return Speaker::query()->where('is_active', true)
            ->orderBy('first_name')->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name', 'position', 'affiliation']);
    }

    public function selectedMainPageId(ProgrammePage $page): ?int
    {
        $page->loadMissing('mainPageLink');

        return $page->mainPageLink?->main_page_id;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array<string, mixed>>  $sessions
     * @param  array<int, array<string, mixed>>  $books
     */
    public function createPage(
        string $type,
        array $data,
        array $sessions,
        array $books,
        ?int $adminId,
    ): ProgrammePage {
        $newPaths = [];

        try {
            return DB::transaction(function () use ($type, $data, $sessions, $books, $adminId, &$newPaths): ProgrammePage {
                $mainPageId = $this->pullMainPageId($data);
                $page = ProgrammePage::query()->create([
                    ...$data,
                    'type' => $type,
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]);
                $this->syncSessions($page, $sessions);
                $this->syncBooks($page, $books, $newPaths);
                $this->syncMainPage($page, $mainPageId);

                return $page->fresh(['sessions.speakers', 'books', 'mainPageLink.mainPage']);
            });
        } catch (Throwable $exception) {
            $this->deleteStoredFiles($newPaths);
            throw $exception;
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array<string, mixed>>  $sessions
     * @param  array<int, array<string, mixed>>  $books
     */
    public function updatePage(
        ProgrammePage $page,
        array $data,
        array $sessions,
        array $books,
        ?int $adminId,
    ): ProgrammePage {
        $newPaths = [];
        $oldPaths = [];

        try {
            DB::transaction(function () use ($page, $data, $sessions, $books, $adminId, &$newPaths, &$oldPaths): void {
                $mainPageId = $this->pullMainPageId($data);
                if ($page->type === ProgrammePage::TYPE_BOOK) {
                    $data = [
                        ...$data,
                        'book_title' => null,
                        'book_file_path' => null,
                        'book_file_name' => null,
                        'book_file_size' => null,
                        'book_link' => null,
                    ];
                }
                $page->update([...$data, 'updated_by' => $adminId]);
                $this->syncSessions($page, $sessions);
                $this->syncBooks($page, $books, $newPaths, $oldPaths);
                $this->syncMainPage($page, $mainPageId);
            });

            $this->deleteStoredFiles($oldPaths);

            return $page->fresh(['sessions.speakers', 'books', 'mainPageLink.mainPage']);
        } catch (Throwable $exception) {
            $this->deleteStoredFiles($newPaths);
            throw $exception;
        }
    }

    public function deletePage(ProgrammePage $page): void
    {
        $paths = $this->bookFilePaths($page);

        DB::transaction(function () use ($page): void {
            $page->mainPageLink()->delete();
            $page->books()->delete();
            $page->delete();
        });

        $this->deleteStoredFiles($paths);
    }

    /** @param array<int, int|string> $ids */
    public function deletePages(string $type, array $ids): int
    {
        $paths = [];
        $deleted = DB::transaction(function () use ($type, $ids, &$paths): int {
            $pages = ProgrammePage::query()->with('books')->where('type', $type)->whereIn('id', $ids)->get();
            foreach ($pages as $page) {
                $paths = [...$paths, ...$this->bookFilePaths($page)];
                $page->mainPageLink()->delete();
                $page->books()->delete();
                $page->delete();
            }

            return $pages->count();
        });

        $this->deleteStoredFiles($paths);

        return $deleted;
    }

    public function slotForType(string $type): string
    {
        return match ($type) {
            ProgrammePage::TYPE_THEME => MainPageLink::SLOT_PROGRAMME_THEME,
            ProgrammePage::TYPE_PROGRAMME => MainPageLink::SLOT_PROGRAMME,
            ProgrammePage::TYPE_SPEAKERS => MainPageLink::SLOT_PROGRAMME_SPEAKERS,
            ProgrammePage::TYPE_BOOK => MainPageLink::SLOT_PROGRAMME_BOOK,
            ProgrammePage::TYPE_ARCHIVE_THEME => MainPageLink::SLOT_ARCHIVE_THEME,
            ProgrammePage::TYPE_ARCHIVE_PROGRAMME => MainPageLink::SLOT_ARCHIVE_PROGRAMME,
            ProgrammePage::TYPE_ARCHIVE_SPEAKERS => MainPageLink::SLOT_ARCHIVE_SPEAKERS,
            ProgrammePage::TYPE_ARCHIVE_LEGACY => MainPageLink::SLOT_ARCHIVE_LEGACY,
            default => abort(404),
        };
    }

    /** @param array<string, mixed> $data */
    private function pullMainPageId(array &$data): ?int
    {
        $mainPageId = isset($data['main_page_id']) && $data['main_page_id'] !== ''
            ? (int) $data['main_page_id']
            : null;
        unset($data['main_page_id'], $data['remove_book_file'], $data['return_url']);

        return $mainPageId;
    }

    private function syncMainPage(ProgrammePage $page, ?int $mainPageId): void
    {
        $slot = $this->slotForType($page->type);
        $page->mainPageLink()->where('slot', $slot)->delete();

        if ($mainPageId !== null) {
            $this->mainPageService->mapContent(MainPage::query()->findOrFail($mainPageId), $slot, $page);
        }
    }

    /** @param array<int, array<string, mixed>> $sessions */
    private function syncSessions(ProgrammePage $page, array $sessions): void
    {
        if (! in_array($page->type, [ProgrammePage::TYPE_SPEAKERS, ProgrammePage::TYPE_ARCHIVE_SPEAKERS], true)) {
            return;
        }

        foreach ([1, 2] as $dayNumber) {
            $sessionData = $sessions[$dayNumber - 1] ?? [];
            $session = $page->sessions()->updateOrCreate(
                ['day_number' => $dayNumber],
                [
                    'session_name' => $sessionData['session_name'] ?? null,
                    'is_active' => $dayNumber === 1 || (bool) ($sessionData['is_active'] ?? false),
                    'sort_order' => $dayNumber,
                ],
            );
            $speakerIds = array_values(array_unique(array_map('intval', (array) ($sessionData['speaker_ids'] ?? []))));
            $syncData = [];
            foreach ($speakerIds as $index => $speakerId) {
                $syncData[$speakerId] = ['sort_order' => $index + 1];
            }
            $session->speakers()->sync($syncData);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $books
     * @param  array<int, string>  $newPaths
     * @param  array<int, string>  $oldPaths
     */
    private function syncBooks(ProgrammePage $page, array $books, array &$newPaths, array &$oldPaths = []): void
    {
        if ($page->type !== ProgrammePage::TYPE_BOOK) {
            return;
        }

        $existingBooks = $page->books()->get()->keyBy('id');
        $keptIds = [];

        foreach (array_values($books) as $index => $bookData) {
            $bookId = isset($bookData['id']) ? (int) $bookData['id'] : null;
            /** @var ProgrammePageBook $book */
            $book = $bookId && $existingBooks->has($bookId)
                ? $existingBooks->get($bookId)
                : $page->books()->make();
            $uploadedFile = $bookData['file'] ?? null;

            $book->title = $bookData['title'] ?? null;
            $book->link = $bookData['link'] ?? null;
            $book->sort_order = $index + 1;

            if ($uploadedFile instanceof UploadedFile) {
                $newPath = $uploadedFile->store('programme/books', 'public');
                $newPaths[] = $newPath;
                if ($book->file_path) {
                    $oldPaths[] = $book->file_path;
                }
                $book->file_path = $newPath;
                $book->file_name = $uploadedFile->getClientOriginalName();
                $book->file_size = $uploadedFile->getSize();
            } elseif ((bool) ($bookData['remove_file'] ?? false)) {
                if ($book->file_path) {
                    $oldPaths[] = $book->file_path;
                }
                $book->file_path = null;
                $book->file_name = null;
                $book->file_size = null;
            }

            $book->save();
            $keptIds[] = $book->id;
        }

        $existingBooks->except($keptIds)->each(function (ProgrammePageBook $book) use (&$oldPaths): void {
            if ($book->file_path) {
                $oldPaths[] = $book->file_path;
            }
            $book->delete();
        });
    }

    /** @return array<int, string> */
    private function bookFilePaths(ProgrammePage $page): array
    {
        $page->loadMissing('books');

        return $page->books->pluck('file_path')
            ->push($page->book_file_path)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /** @param array<int, string> $paths */
    private function deleteStoredFiles(array $paths): void
    {
        $paths = array_values(array_unique(array_filter($paths)));
        if ($paths !== []) {
            Storage::disk('public')->delete($paths);
        }
    }
}
