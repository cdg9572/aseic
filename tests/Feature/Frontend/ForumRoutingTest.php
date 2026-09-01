<?php

namespace Tests\Feature\Frontend;

use App\Models\Banner;
use App\Models\Board;
use App\Models\MainPage;
use App\Models\Popup;
use App\Models\Speaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class ForumRoutingTest extends TestCase
{
    use DatabaseTransactions;

    public function test_visible_main_page_is_served_by_its_folder_name_and_keeps_subpage_links_scoped(): void
    {
        $folder = 'routing-'.Str::lower(Str::random(12));
        $mainPage = MainPage::factory()->create([
            'folder_name' => $folder,
            'is_visible' => true,
        ]);

        $response = $this->get('/'.$folder);

        $response->assertOk()
            ->assertViewIs('forums.default.main')
            ->assertViewHas('mainPage', fn (MainPage $viewMainPage): bool => $viewMainPage->is($mainPage))
            ->assertSee('/'.$folder.'/programme', false)
            ->assertSee('/'.$folder.'/registration', false)
            ->assertSee('/'.$folder.'/announcements', false);

        $this->get('/'.$folder.'/about/forum')
            ->assertOk()
            ->assertViewIs('forums.default.about.forum')
            ->assertViewHas('mainPage', fn (MainPage $viewMainPage): bool => $viewMainPage->is($mainPage));
    }

    public function test_hidden_or_unknown_main_page_cannot_be_opened(): void
    {
        $folder = 'hidden-'.Str::lower(Str::random(12));
        MainPage::factory()->create([
            'folder_name' => $folder,
            'is_visible' => false,
        ]);

        $this->get('/'.$folder)->assertNotFound();
        $this->get('/'.$folder.'/programme')->assertNotFound();
        $this->get('/not-registered-'.Str::lower(Str::random(12)))->assertNotFound();
    }

    public function test_root_redirects_to_the_latest_visible_main_page(): void
    {
        MainPage::query()->update(['is_visible' => false]);

        MainPage::factory()->create([
            'folder_name' => 'older-'.Str::lower(Str::random(12)),
            'event_start_date' => '2098-01-01',
            'event_end_date' => '2098-01-02',
            'is_visible' => true,
        ]);
        $latest = MainPage::factory()->create([
            'folder_name' => 'latest-'.Str::lower(Str::random(12)),
            'event_start_date' => '2099-01-01',
            'event_end_date' => '2099-01-02',
            'is_visible' => true,
        ]);
        MainPage::factory()->create([
            'folder_name' => 'hidden-latest-'.Str::lower(Str::random(12)),
            'event_start_date' => '2100-01-01',
            'event_end_date' => '2100-01-02',
            'is_visible' => false,
        ]);

        $this->get('/')->assertRedirect(route('home', ['mainPage' => $latest->folder_name]));
    }

    public function test_popup_hidden_cookie_prevents_normal_popup_from_opening_again(): void
    {
        $folder = 'popup-'.Str::lower(Str::random(12));
        $mainPage = MainPage::factory()->create([
            'folder_name' => $folder,
            'is_visible' => true,
        ]);
        $popup = Popup::query()->create([
            'title' => 'Cookie test popup',
            'use_period' => false,
            'width' => 400,
            'height' => 300,
            'position_top' => 100,
            'position_left' => 100,
            'popup_type' => 'html',
            'popup_display_type' => 'normal',
            'popup_content' => '<p>Popup content</p>',
            'is_active' => true,
            'sort_order' => 999,
        ]);
        $mainPage->update(['popup_id' => $popup->id]);
        $popupUrl = route('popup.show', $popup);
        $cookieName = 'popup_hide_'.$popup->id;
        $hadCookie = array_key_exists($cookieName, $_COOKIE);
        $originalCookie = $_COOKIE[$cookieName] ?? null;

        try {
            unset($_COOKIE[$cookieName]);

            $this->get('/'.$folder)
                ->assertOk()
                ->assertSee($popupUrl, false)
                ->assertSee('/js/popup.js', false);

            foreach (['1', 'true'] as $cookieValue) {
                $_COOKIE[$cookieName] = $cookieValue;

                $this->get('/'.$folder)
                    ->assertOk()
                    ->assertDontSee($popupUrl, false);
            }

            $this->get('/popup/'.$popup->id)
                ->assertOk()
                ->assertSee('data-popup-id="'.$popup->id.'"', false)
                ->assertSee('/js/popup-window.js', false)
                ->assertDontSee('onclick=', false);
        } finally {
            if ($hadCookie) {
                $_COOKIE[$cookieName] = $originalCookie;
            } else {
                unset($_COOKIE[$cookieName]);
            }
        }
    }

    public function test_each_forum_only_renders_its_selected_banner_and_popup(): void
    {
        $firstFolder = 'first-assets-'.Str::lower(Str::random(10));
        $secondFolder = 'second-assets-'.Str::lower(Str::random(10));
        $firstMainPage = MainPage::factory()->create([
            'folder_name' => $firstFolder,
            'is_visible' => true,
        ]);
        $secondMainPage = MainPage::factory()->create([
            'folder_name' => $secondFolder,
            'is_visible' => true,
        ]);

        $firstBanner = Banner::query()->create($this->bannerData('First forum banner'));
        $secondBanner = Banner::query()->create($this->bannerData('Second forum banner'));
        $firstPopup = Popup::query()->create($this->popupData('First forum popup'));
        $secondPopup = Popup::query()->create($this->popupData('Second forum popup'));

        $firstMainPage->update([
            'banner_id' => $firstBanner->id,
            'popup_id' => $firstPopup->id,
        ]);
        $secondMainPage->update([
            'banner_id' => $secondBanner->id,
            'popup_id' => $secondPopup->id,
        ]);

        $this->get('/'.$firstFolder)
            ->assertOk()
            ->assertSee('First forum banner')
            ->assertDontSee('Second forum banner')
            ->assertSee(route('popup.show', $firstPopup), false)
            ->assertDontSee(route('popup.show', $secondPopup), false);

        $this->get('/'.$secondFolder)
            ->assertOk()
            ->assertSee('Second forum banner')
            ->assertDontSee('First forum banner')
            ->assertSee(route('popup.show', $secondPopup), false)
            ->assertDontSee(route('popup.show', $firstPopup), false);
    }

    public function test_main_page_admin_content_is_rendered_on_the_selected_forum_home(): void
    {
        $folder = 'main-content-'.Str::lower(Str::random(10));
        $banner = Banner::query()->create($this->bannerData('Connected forum banner'));
        $mainPage = MainPage::factory()->create([
            'folder_name' => $folder,
            'event_name' => 'Connected Forum 2027',
            'event_start_date' => '2027-09-03',
            'event_end_date' => '2027-09-05',
            'banner_id' => $banner->id,
            'programme_background_path' => 'main-pages/programme/programme.jpg',
            'programme_items' => [
                ['time' => '09:00', 'subject' => 'Connected Opening', 'content' => 'Connected opening content'],
                ['time' => '10:00', 'subject' => 'Connected Session', 'content' => 'Connected session content'],
                ['time' => '', 'subject' => '', 'content' => ''],
                ['time' => '', 'subject' => '', 'content' => ''],
            ],
            'register_background_path' => 'main-pages/register/register.jpg',
            'past_forum_video_url' => 'https://www.youtube.com/watch?v=connected-forum',
            'host_images' => [['path' => 'main-pages/host/host.png', 'name' => 'Host Organization.png', 'size' => 100]],
            'organizer_images' => [['path' => 'main-pages/organizers/organizer.png', 'name' => 'Organizer.png', 'size' => 100]],
            'co_organizer_images' => [['path' => 'main-pages/co-organizers/co-organizer.png', 'name' => 'Co Organizer.png', 'size' => 100]],
            'footer_text' => 'Connected footer text',
            'is_visible' => true,
        ]);
        $visibleSpeaker = Speaker::factory()->create([
            'first_name' => 'Connected',
            'last_name' => 'Speaker',
            'role' => Speaker::ROLE_STARTUP,
            'profile_image' => 'speakers/connected.jpg',
            'is_image_visible' => true,
            'is_active' => true,
        ]);
        $hiddenSpeaker = Speaker::factory()->create([
            'first_name' => 'Hidden',
            'last_name' => 'Speaker',
            'is_active' => false,
        ]);
        $mainPage->speakers()->attach([
            $visibleSpeaker->id => ['sort_order' => 1],
            $hiddenSpeaker->id => ['sort_order' => 2],
        ]);

        $this->get('/'.$folder)
            ->assertOk()
            ->assertSee('Connected Forum 2027')
            ->assertSee('2027. 09. 03 – 2027. 09. 05')
            ->assertSee('Connected Speaker')
            ->assertSee('START UP')
            ->assertSee('/storage/speakers/connected.jpg', false)
            ->assertDontSee('Hidden Speaker')
            ->assertSee('Connected Opening')
            ->assertSee('Connected opening content')
            ->assertSee('/storage/main-pages/programme/programme.jpg', false)
            ->assertSee('/storage/main-pages/register/register.jpg', false)
            ->assertSee('https://www.youtube.com/watch?v=connected-forum', false)
            ->assertDontSee('/storage/main-pages/host/host.png', false)
            ->assertDontSee('/storage/main-pages/organizers/organizer.png', false)
            ->assertDontSee('/storage/main-pages/co-organizers/co-organizer.png', false)
            ->assertSee('Connected footer text');
    }

    public function test_global_announcement_without_category_is_rendered_on_forum_home(): void
    {
        Board::query()->where('slug', 'notices')->firstOrFail();

        $folder = 'announcements-'.Str::lower(Str::random(10));
        MainPage::factory()->create([
            'folder_name' => $folder,
            'is_visible' => true,
        ]);

        DB::table('board_notices')->insert([
            [
                'title' => 'Visible global announcement',
                'content' => '<p>Visible content</p>',
                'author_name' => 'Administrator',
                'category' => null,
                'is_notice' => false,
                'is_secret' => false,
                'is_active' => true,
                'view_count' => 0,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Inactive global announcement',
                'content' => '<p>Inactive content</p>',
                'author_name' => 'Administrator',
                'category' => null,
                'is_notice' => false,
                'is_secret' => false,
                'is_active' => false,
                'view_count' => 0,
                'sort_order' => 0,
                'created_at' => now()->subMinute(),
                'updated_at' => now()->subMinute(),
            ],
        ]);

        $this->get('/'.$folder)
            ->assertOk()
            ->assertSee('Visible global announcement')
            ->assertDontSee('Inactive global announcement');
    }

    public function test_publishing_original_and_default_template_have_fixed_preview_urls(): void
    {
        $this->ensureDefaultMainPageIsVisible();

        $this->get('/publishing-original')
            ->assertOk()
            ->assertViewIs('home.index')
            ->assertSee('/publishing-original/programme', false);

        $this->get('/default')
            ->assertOk()
            ->assertViewIs('forums.default.main')
            ->assertSee('/default/programme', false);

        $this->get('/default/media/youtube')
            ->assertOk()
            ->assertViewIs('forums.default.media.youtube');
    }

    public function test_every_publishing_original_and_default_subpage_is_previewable(): void
    {
        $this->ensureDefaultMainPageIsVisible();

        $paths = [
            '',
            'about/forum',
            'about/committee',
            'about/organizers',
            'about/venue',
            'programme/theme',
            'programme',
            'programme/list',
            'programme/speakers',
            'programme/book',
            'archive/theme',
            'archive/programme',
            'archive/speakers',
            'archive/past',
            'media/gallery',
            'media/news',
            'media/news/view',
            'media/youtube',
            'registration',
            'registration/register',
            'registration/confirm',
            'announcements',
            'announcements/view',
        ];

        foreach (['publishing-original', 'default'] as $previewMode) {
            foreach ($paths as $path) {
                $url = '/'.$previewMode.($path === '' ? '' : '/'.$path);

                $response = $this->get($url);

                if ($previewMode === 'default' && $path === 'programme/list') {
                    $response->assertRedirect('/default/programme');
                } else {
                    $response->assertOk();
                }
            }
        }
    }

    private function ensureDefaultMainPageIsVisible(): MainPage
    {
        $mainPage = MainPage::query()->where('folder_name', 'default')->first();

        if ($mainPage instanceof MainPage) {
            $mainPage->update(['is_visible' => true]);

            return $mainPage;
        }

        return MainPage::factory()->create([
            'folder_name' => 'default',
            'is_visible' => true,
        ]);
    }

    /** @return array<string, mixed> */
    private function bannerData(string $text): array
    {
        return [
            'title' => $text,
            'main_text' => $text,
            'url_target' => '_self',
            'use_period' => false,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    /** @return array<string, mixed> */
    private function popupData(string $title): array
    {
        return [
            'title' => $title,
            'use_period' => false,
            'width' => 400,
            'height' => 300,
            'position_top' => 100,
            'position_left' => 100,
            'popup_type' => 'html',
            'popup_display_type' => 'normal',
            'popup_content' => '<p>'.$title.'</p>',
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
