<?php

namespace Tests\Feature\Backoffice;

use App\Models\Banner;
use App\Models\MainPage;
use App\Models\Popup;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class HomepageAssetMainPageLinkTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }

    public function test_popup_and_banner_forms_offer_main_page_selection(): void
    {
        $mainPage = $this->mainPage('asset-form');

        foreach (['/backoffice/popups/create', '/backoffice/banners/create'] as $url) {
            $this->actingAs($this->admin)
                ->get($url)
                ->assertOk()
                ->assertSee('Main Page 연결')
                ->assertSee($mainPage->folder_name)
                ->assertSee($mainPage->event_name);
        }
    }

    public function test_admin_can_link_and_move_a_popup_between_main_pages(): void
    {
        $firstMainPage = $this->mainPage('popup-first');
        $secondMainPage = $this->mainPage('popup-second');

        $this->actingAs($this->admin)
            ->post('/backoffice/popups', $this->popupPayload($firstMainPage->id))
            ->assertRedirect(route('backoffice.popups.index'));

        $popup = Popup::query()->where('title', 'Main Page popup')->firstOrFail();
        $this->assertSame($popup->id, $firstMainPage->fresh()->popup_id);

        $this->actingAs($this->admin)
            ->get('/backoffice/popups/'.$popup->id.'/edit')
            ->assertOk()
            ->assertSee($firstMainPage->folder_name);

        $this->actingAs($this->admin)
            ->put('/backoffice/popups/'.$popup->id, $this->popupPayload($secondMainPage->id))
            ->assertRedirect(route('backoffice.popups.index'));

        $this->assertNull($firstMainPage->fresh()->popup_id);
        $this->assertSame($popup->id, $secondMainPage->fresh()->popup_id);
    }

    public function test_admin_can_link_and_unlink_a_banner_from_a_main_page(): void
    {
        $mainPage = $this->mainPage('banner-link');

        $this->actingAs($this->admin)
            ->post('/backoffice/banners', $this->bannerPayload($mainPage->id))
            ->assertRedirect(route('backoffice.banners.index'));

        $banner = Banner::query()->where('title', 'Main Page banner')->firstOrFail();
        $this->assertSame($banner->id, $mainPage->fresh()->banner_id);

        $this->actingAs($this->admin)
            ->put('/backoffice/banners/'.$banner->id, $this->bannerPayload(null))
            ->assertRedirect(route('backoffice.banners.index'));

        $this->assertNull($mainPage->fresh()->banner_id);
    }

    public function test_main_page_selection_rejects_a_missing_main_page(): void
    {
        $this->actingAs($this->admin)
            ->post('/backoffice/popups', $this->popupPayload(999999999))
            ->assertSessionHasErrors('main_page_id');

        $this->actingAs($this->admin)
            ->post('/backoffice/banners', $this->bannerPayload(999999999))
            ->assertSessionHasErrors('main_page_id');
    }

    private function mainPage(string $prefix): MainPage
    {
        return MainPage::factory()->create([
            'folder_name' => $prefix.'-'.Str::lower(Str::random(10)),
            'event_name' => Str::headline($prefix).' Forum',
        ]);
    }

    /** @return array<string, mixed> */
    private function popupPayload(?int $mainPageId): array
    {
        return [
            'main_page_id' => $mainPageId,
            'title' => 'Main Page popup',
            'use_period' => '0',
            'width' => 400,
            'height' => 300,
            'position_top' => 100,
            'position_left' => 100,
            'url_target' => '_blank',
            'popup_type' => 'html',
            'popup_display_type' => 'normal',
            'popup_content' => '<p>Popup</p>',
            'is_active' => '1',
            'sort_order' => 0,
        ];
    }

    /** @return array<string, mixed> */
    private function bannerPayload(?int $mainPageId): array
    {
        return [
            'main_page_id' => $mainPageId,
            'title' => 'Main Page banner',
            'main_text' => 'Main Page banner',
            'url_target' => '_self',
            'use_period' => '0',
            'is_active' => '1',
            'sort_order' => 0,
        ];
    }
}
