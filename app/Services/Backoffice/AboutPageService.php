<?php

namespace App\Services\Backoffice;

use App\Models\AboutPage;
use App\Models\MainPage;
use App\Models\MainPageLink;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AboutPageService
{
    public function __construct(private readonly MainPageService $mainPageService) {}

    /**
     * @param  array<string, mixed>  $filters
     */
    public function getPages(string $type, array $filters, int $perPage): LengthAwarePaginator
    {
        $query = AboutPage::query()
            ->with(['creator:id,name', 'mainPageLink.mainPage:id,folder_name,event_name'])
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
                    ->orWhere('subtitle', 'like', '%'.$keyword.'%')
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
        return MainPage::query()
            ->latest('id')
            ->get(['id', 'folder_name', 'event_name']);
    }

    public function selectedMainPageId(AboutPage $page): ?int
    {
        $page->loadMissing('mainPageLink');

        return $page->mainPageLink?->main_page_id;
    }

    public function syncMainPage(AboutPage $page, ?int $mainPageId): void
    {
        $slot = $this->slotForType($page->type);
        $page->mainPageLink()->where('slot', $slot)->delete();

        if ($mainPageId !== null) {
            $mainPage = MainPage::query()->findOrFail($mainPageId);
            $this->mainPageService->mapContent($mainPage, $slot, $page);
        }

        $page->updateQuietly(['is_main_page_visible' => $mainPageId !== null]);
    }

    public function deletePage(AboutPage $page): void
    {
        DB::transaction(function () use ($page): void {
            $page->mainPageLink()->delete();
            $page->delete();
        });
    }

    /** @param array<int, int|string> $ids */
    public function deletePages(string $type, array $ids): int
    {
        return DB::transaction(function () use ($type, $ids): int {
            $pages = AboutPage::query()->where('type', $type)->whereIn('id', $ids)->get();
            foreach ($pages as $page) {
                $page->mainPageLink()->delete();
                $page->delete();
            }

            return $pages->count();
        });
    }

    public function slotForType(string $type): string
    {
        return match ($type) {
            AboutPage::TYPE_FORUM => MainPageLink::SLOT_ABOUT_FORUM,
            AboutPage::TYPE_STEERING_COMMITTEE => MainPageLink::SLOT_STEERING_COMMITTEE,
            AboutPage::TYPE_CO_ORGANIZERS => MainPageLink::SLOT_CO_ORGANIZERS,
            AboutPage::TYPE_VENUE => MainPageLink::SLOT_VENUE,
            default => abort(404),
        };
    }
}
