<?php

namespace App\Services\Backoffice;

use App\Models\AboutPage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SteeringCommitteeService
{
    public function __construct(private readonly AboutPageService $aboutPageService) {}

    /** @param array<string, mixed> $filters */
    public function getPages(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->aboutPageService->getPages(AboutPage::TYPE_STEERING_COMMITTEE, $filters, $perPage);
    }

    /** @param array<string, mixed> $data */
    public function createPage(array $data, ?int $adminId): AboutPage
    {
        return DB::transaction(function () use ($data, $adminId): AboutPage {
            $page = AboutPage::query()->create([
                'type' => AboutPage::TYPE_STEERING_COMMITTEE,
                'page_title' => $data['page_title'],
                'subtitle' => $data['subtitle'] ?? null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ]);
            $this->syncPartners($page, $data);
            $this->aboutPageService->syncMainPage($page, $this->mainPageId($data));

            return $page;
        });
    }

    /** @param array<string, mixed> $data */
    public function updatePage(AboutPage $page, array $data, ?int $adminId): AboutPage
    {
        return DB::transaction(function () use ($page, $data, $adminId): AboutPage {
            $page->update(['page_title' => $data['page_title'], 'subtitle' => $data['subtitle'] ?? null, 'updated_by' => $adminId]);
            $this->syncPartners($page, $data);
            $this->aboutPageService->syncMainPage($page, $this->mainPageId($data));

            return $page->refresh();
        });
    }

    public function deletePage(AboutPage $page): void
    {
        $this->aboutPageService->deletePage($page);
    }

    /** @param array<int, int|string> $ids */
    public function deletePages(array $ids): int
    {
        return $this->aboutPageService->deletePages(AboutPage::TYPE_STEERING_COMMITTEE, $ids);
    }

    /** @param array<string, mixed> $data */
    private function syncPartners(AboutPage $page, array $data): void
    {
        DB::table('about_steering_partners')->where('about_page_id', $page->id)->delete();
        $rows = [];
        foreach ([
            'organized_ids' => 'organized',
            'partnership_ids' => 'partnership',
        ] as $field => $type) {
            foreach (array_values(array_unique(array_map('intval', (array) Arr::get($data, $field, [])))) as $index => $partnerId) {
                $rows[] = [
                    'about_page_id' => $page->id,
                    'homepage_partner_id' => $partnerId,
                    'group_type' => $type,
                    'sort_order' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        if ($rows !== []) {
            DB::table('about_steering_partners')->insert($rows);
        }
    }

    /** @param array<string, mixed> $data */
    private function mainPageId(array $data): ?int
    {
        return filled($data['main_page_id'] ?? null) ? (int) $data['main_page_id'] : null;
    }
}
