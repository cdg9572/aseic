<?php

namespace App\Services\Backoffice;

use App\Models\Banner;
use App\Models\MainPage;
use App\Models\Popup;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MainPageAssetService
{
    /** @return Collection<int, MainPage> */
    public function mainPageOptions(): Collection
    {
        return MainPage::query()
            ->latest('id')
            ->get(['id', 'folder_name', 'event_name']);
    }

    public function selectedMainPageIdForBanner(Banner $banner): ?int
    {
        return $this->selectedMainPageId('banner_id', $banner->getKey());
    }

    public function selectedMainPageIdForPopup(Popup $popup): ?int
    {
        return $this->selectedMainPageId('popup_id', $popup->getKey());
    }

    public function syncBanner(Banner $banner, ?int $mainPageId): void
    {
        $this->sync('banner_id', $banner->getKey(), $mainPageId);
    }

    public function syncPopup(Popup $popup, ?int $mainPageId): void
    {
        $this->sync('popup_id', $popup->getKey(), $mainPageId);
    }

    public function normalizeSelections(MainPage $mainPage): void
    {
        foreach (['banner_id', 'popup_id'] as $column) {
            $assetId = $mainPage->{$column};
            if ($assetId === null) {
                continue;
            }

            MainPage::query()
                ->whereKeyNot($mainPage->getKey())
                ->where($column, $assetId)
                ->update([$column => null]);
        }
    }

    private function selectedMainPageId(string $column, int $assetId): ?int
    {
        return MainPage::query()
            ->where($column, $assetId)
            ->value('id');
    }

    private function sync(string $column, int $assetId, ?int $mainPageId): void
    {
        DB::transaction(function () use ($column, $assetId, $mainPageId): void {
            MainPage::query()
                ->where($column, $assetId)
                ->update([$column => null]);

            if ($mainPageId !== null) {
                MainPage::query()->findOrFail($mainPageId)->update([$column => $assetId]);
            }
        });
    }
}
