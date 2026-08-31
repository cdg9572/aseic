<?php

namespace App\Services\Backoffice;

use App\Models\AboutPage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AboutVenueService
{
    public function __construct(private readonly AboutPageService $aboutPageService) {}

    /** @param array<string, mixed> $filters */
    public function getPages(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->aboutPageService->getPages(AboutPage::TYPE_VENUE, $filters, $perPage);
    }

    /** @param array<string, mixed> $data */
    public function createPage(array $data, ?int $adminId): AboutPage
    {
        return DB::transaction(function () use ($data, $adminId): AboutPage {
            $page = AboutPage::query()->create(['type' => AboutPage::TYPE_VENUE, 'page_title' => $data['page_title'], 'subtitle' => $data['subtitle'] ?? null, 'created_by' => $adminId, 'updated_by' => $adminId]);
            $page->venueDetail()->create($this->detailData($data));
            $this->aboutPageService->syncMainPage($page, $this->mainPageId($data));

            return $page->load('venueDetail');
        });
    }

    /** @param array<string, mixed> $data */
    public function updatePage(AboutPage $page, array $data, ?int $adminId): AboutPage
    {
        return DB::transaction(function () use ($page, $data, $adminId): AboutPage {
            $page->update(['page_title' => $data['page_title'], 'subtitle' => $data['subtitle'] ?? null, 'updated_by' => $adminId]);
            $page->venueDetail()->updateOrCreate([], $this->detailData($data));
            $this->aboutPageService->syncMainPage($page, $this->mainPageId($data));

            return $page->refresh()->load('venueDetail');
        });
    }

    public function deletePage(AboutPage $page): void
    {
        $this->aboutPageService->deletePage($page);
    }

    /** @param array<int, int|string> $ids */
    public function deletePages(array $ids): int
    {
        return $this->aboutPageService->deletePages(AboutPage::TYPE_VENUE, $ids);
    }

    /** @param array<string, mixed> $data @return array<string, mixed> */
    private function detailData(array $data): array
    {
        return Arr::only($data, ['postal_code', 'address', 'address_detail', 'venue_name', 'venue_description', 'event_date', 'format', 'bus_content', 'subway_content', 'taxi_content']);
    }

    /** @param array<string, mixed> $data */
    private function mainPageId(array $data): ?int
    {
        return filled($data['main_page_id'] ?? null) ? (int) $data['main_page_id'] : null;
    }
}
