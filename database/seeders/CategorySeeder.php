<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * 현재 탭 관리에서 사용하는 0차 메뉴만 동기화합니다.
     *
     * 운영 중 관리자가 등록한 1차 이하 탭은 삭제하거나 수정하지 않습니다.
     */
    public function run(): void
    {
        $groups = [
            $this->group(Category::GROUP_CODE_PHOTO_GALLERY, 'Photo Gallery', 3),
            $this->group(Category::GROUP_CODE_NEWS_CLIPPINGS, 'News Clippings', 4),
            $this->group(Category::GROUP_CODE_ARCHIVE_THEME, 'Theme', 5),
            $this->group(Category::GROUP_CODE_ARCHIVE_PROGRAMME, 'Programme', 6),
            $this->group(Category::GROUP_CODE_YOUTUBE_CHANNEL, 'YouTube Channel', 7),
        ];

        DB::transaction(function () use ($groups): void {
            foreach ($groups as $group) {
                Category::query()->updateOrCreate(
                    ['code' => $group['code']],
                    $group,
                );
            }
        });

        $this->command?->info('탭 관리 0차 메뉴 5개를 동기화했습니다.');
    }

    /** @return array<string, mixed> */
    private function group(string $code, string $name, int $displayOrder): array
    {
        return [
            'parent_id' => null,
            'code' => $code,
            'name' => $name,
            'depth' => 0,
            'display_order' => $displayOrder,
            'is_active' => true,
        ];
    }
}
