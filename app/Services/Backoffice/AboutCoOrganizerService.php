<?php

namespace App\Services\Backoffice;

use App\Models\AboutCoOrganizerItem;
use App\Models\AboutPage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AboutCoOrganizerService
{
    public function __construct(private readonly AboutPageService $aboutPageService) {}

    /** @param array<string, mixed> $filters */
    public function getPages(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->aboutPageService->getPages(AboutPage::TYPE_CO_ORGANIZERS, $filters, $perPage);
    }

    /** @param array<string, mixed> $data @param array<int, mixed> $uploads */
    public function createPage(array $data, array $uploads, ?int $adminId): AboutPage
    {
        $newPaths = [];
        try {
            return DB::transaction(function () use ($data, $uploads, $adminId, &$newPaths): AboutPage {
                $page = AboutPage::query()->create(['type' => AboutPage::TYPE_CO_ORGANIZERS, 'page_title' => $data['page_title'], 'subtitle' => $data['subtitle'] ?? null, 'created_by' => $adminId, 'updated_by' => $adminId]);
                $discardedPaths = [];
                $this->syncItems($page, (array) ($data['items'] ?? []), $uploads, $newPaths, $discardedPaths);
                $this->aboutPageService->syncMainPage($page, $this->mainPageId($data));

                return $page->load('coOrganizerItems');
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($newPaths);
            throw $exception;
        }
    }

    /** @param array<string, mixed> $data @param array<int, mixed> $uploads */
    public function updatePage(AboutPage $page, array $data, array $uploads, ?int $adminId): AboutPage
    {
        $newPaths = [];
        $oldPaths = [];
        try {
            DB::transaction(function () use ($page, $data, $uploads, $adminId, &$newPaths, &$oldPaths): void {
                $page->update(['page_title' => $data['page_title'], 'subtitle' => $data['subtitle'] ?? null, 'updated_by' => $adminId]);
                $this->syncItems($page, (array) ($data['items'] ?? []), $uploads, $newPaths, $oldPaths);
                $this->aboutPageService->syncMainPage($page, $this->mainPageId($data));
            });
            Storage::disk('public')->delete(array_values(array_unique($oldPaths)));

            return $page->refresh()->load('coOrganizerItems');
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($newPaths);
            throw $exception;
        }
    }

    public function deletePage(AboutPage $page): void
    {
        $this->aboutPageService->deletePage($page);
    }

    /** @param array<int, int|string> $ids */
    public function deletePages(array $ids): int
    {
        return $this->aboutPageService->deletePages(AboutPage::TYPE_CO_ORGANIZERS, $ids);
    }

    /**
     * @param  array<int, mixed>  $items
     * @param  array<int, mixed>  $uploads
     * @param  array<int, string>  $newPaths
     * @param  array<int, string>  $oldPaths
     */
    private function syncItems(AboutPage $page, array $items, array $uploads, array &$newPaths, array &$oldPaths): void
    {
        $existing = $page->coOrganizerItems()->get()->keyBy('id');
        $keptIds = [];

        $sortOrder = 1;
        foreach ($items as $inputIndex => $itemData) {
            $itemData = is_array($itemData) ? $itemData : [];
            $itemId = filled($itemData['id'] ?? null) ? (int) $itemData['id'] : null;
            $item = $itemId !== null ? $existing->get($itemId) : null;
            $values = [
                'name' => $itemData['name'] ?? null,
                'description' => $itemData['description'] ?? null,
                'url' => $itemData['url'] ?? null,
                'sort_order' => $sortOrder++,
            ];

            $file = $uploads[$inputIndex]['logo'] ?? null;
            if ($file instanceof UploadedFile) {
                $path = $file->store('about/co-organizers/logos', 'public');
                $newPaths[] = $path;
                $values['logo_path'] = $path;
                $values['logo_name'] = $file->getClientOriginalName();
                if ($item?->logo_path) {
                    $oldPaths[] = $item->logo_path;
                }
            } elseif (! empty($itemData['remove_logo'])) {
                $values['logo_path'] = null;
                $values['logo_name'] = null;
                if ($item?->logo_path) {
                    $oldPaths[] = $item->logo_path;
                }
            }

            if ($item instanceof AboutCoOrganizerItem) {
                $item->update($values);
                $keptIds[] = $item->id;
            } else {
                $created = $page->coOrganizerItems()->create($values);
                $keptIds[] = $created->id;
            }
        }

        $existing->each(function (AboutCoOrganizerItem $item) use ($keptIds, &$oldPaths): void {
            if (in_array($item->id, $keptIds, true)) {
                return;
            }
            if ($item->logo_path) {
                $oldPaths[] = $item->logo_path;
            }
            $item->delete();
        });
    }

    /** @param array<string, mixed> $data */
    private function mainPageId(array $data): ?int
    {
        return filled($data['main_page_id'] ?? null) ? (int) $data['main_page_id'] : null;
    }
}
