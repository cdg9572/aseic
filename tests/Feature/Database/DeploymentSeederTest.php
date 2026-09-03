<?php

namespace Tests\Feature\Database;

use App\Models\AdminMenu;
use App\Models\Category;
use Database\Seeders\AdminMenuSeeder;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DeploymentSeederTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_menu_seeder_matches_the_current_backoffice_menu(): void
    {
        $this->seed(AdminMenuSeeder::class);

        $this->assertSame(41, AdminMenu::query()->count());
        $this->assertDatabaseHas('admin_menus', [
            'id' => 42,
            'parent_id' => 31,
            'name' => '탭 관리',
            'url' => '/backoffice/categories',
            'order' => 5,
            'is_active' => true,
            'permission_key' => 'settings.categories',
        ]);
        $this->assertSame(
            array_values(array_diff(range(1, 42), [27])),
            AdminMenu::query()->orderBy('id')->pluck('id')->all(),
        );
    }

    public function test_category_seeder_updates_only_the_five_tab_groups_and_preserves_child_tabs(): void
    {
        $childIdsBefore = Category::query()
            ->where('depth', '>', 0)
            ->orderBy('id')
            ->pluck('id')
            ->all();

        $this->seed(CategorySeeder::class);

        $expectedGroups = [
            Category::GROUP_CODE_PHOTO_GALLERY => ['Photo Gallery', 3],
            Category::GROUP_CODE_NEWS_CLIPPINGS => ['News Clippings', 4],
            Category::GROUP_CODE_ARCHIVE_THEME => ['Theme', 5],
            Category::GROUP_CODE_ARCHIVE_PROGRAMME => ['Programme', 6],
            Category::GROUP_CODE_YOUTUBE_CHANNEL => ['YouTube Channel', 7],
        ];

        foreach ($expectedGroups as $code => [$name, $displayOrder]) {
            $this->assertDatabaseHas('categories', [
                'parent_id' => null,
                'code' => $code,
                'name' => $name,
                'depth' => 0,
                'display_order' => $displayOrder,
                'is_active' => true,
            ]);
        }

        $this->assertSame(
            $childIdsBefore,
            Category::query()->where('depth', '>', 0)->orderBy('id')->pluck('id')->all(),
        );
    }
}
