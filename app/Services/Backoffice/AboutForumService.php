<?php

namespace App\Services\Backoffice;

use App\Models\AboutPage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AboutForumService
{
    public function __construct(private readonly AboutPageService $aboutPageService) {}

    /**
     * @param  array<string, mixed>  $filters
     */
    public function getPages(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->aboutPageService->getPages(AboutPage::TYPE_FORUM, $filters, $perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createPage(array $data, ?int $adminId): AboutPage
    {
        return DB::transaction(function () use ($data, $adminId): AboutPage {
            $page = AboutPage::query()->create($this->pageData($data, $adminId, true));
            $page->forumDetail()->create($this->detailData($data));
            $this->aboutPageService->syncMainPage($page, $this->mainPageId($data));

            return $page->load('forumDetail');
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updatePage(AboutPage $page, array $data, ?int $adminId): AboutPage
    {
        return DB::transaction(function () use ($page, $data, $adminId): AboutPage {
            $page->update($this->pageData($data, $adminId, false));
            $page->forumDetail()->updateOrCreate([], $this->detailData($data));
            $this->aboutPageService->syncMainPage($page, $this->mainPageId($data));

            return $page->refresh()->load('forumDetail');
        });
    }

    public function deletePage(AboutPage $page): void
    {
        $this->aboutPageService->deletePage($page);
    }

    /**
     * @param  array<int, int|string>  $ids
     */
    public function deletePages(array $ids): int
    {
        return $this->aboutPageService->deletePages(AboutPage::TYPE_FORUM, $ids);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function pageData(array $data, ?int $adminId, bool $creating): array
    {
        $pageData = Arr::only($data, [
            'page_title',
            'subtitle',
        ]);
        $pageData['type'] = AboutPage::TYPE_FORUM;
        $pageData['updated_by'] = $adminId;

        if ($creating) {
            $pageData['created_by'] = $adminId;
        }

        return $pageData;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function detailData(array $data): array
    {
        $detailData = Arr::only($data, [
            'overview',
            'forums_since_2015',
            'participants',
            'countries',
            'organizations',
        ]);
        $detailData['backgrounds'] = $this->normalizeItems($data['backgrounds'] ?? [], 4);
        $detailData['objectives'] = $this->normalizeItems($data['objectives'] ?? [], 3);

        return $detailData;
    }

    /** @param array<string, mixed> $data */
    private function mainPageId(array $data): ?int
    {
        return filled($data['main_page_id'] ?? null) ? (int) $data['main_page_id'] : null;
    }

    /**
     * @param  array<int, mixed>  $items
     * @return array<int, array{title: string|null, content: string|null}>
     */
    private function normalizeItems(array $items, int $count): array
    {
        return collect(range(0, $count - 1))
            ->map(function (int $index) use ($items): array {
                $item = is_array($items[$index] ?? null) ? $items[$index] : [];

                return [
                    'title' => $item['title'] ?? null,
                    'content' => $item['content'] ?? null,
                ];
            })
            ->all();
    }
}
