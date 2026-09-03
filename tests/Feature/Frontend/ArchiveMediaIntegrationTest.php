<?php

namespace Tests\Feature\Frontend;

use App\Models\Category;
use App\Models\MainPage;
use App\Models\MainPageLink;
use App\Models\MediaContent;
use App\Models\ProgrammePage;
use App\Models\Speaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class ArchiveMediaIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_connected_archive_pages_render_only_their_backoffice_content(): void
    {
        $folder = 'archive-content-'.Str::lower(Str::random(10));
        $mainPage = MainPage::factory()->create([
            'folder_name' => $folder,
            'event_name' => 'Connected Archive Forum',
            'is_visible' => true,
        ]);
        $themeCategory = $this->mediaCategory(Category::GROUP_CODE_ARCHIVE_THEME, '2099');
        $programmeCategory = $this->mediaCategory(Category::GROUP_CODE_ARCHIVE_PROGRAMME, '2099');

        $theme = ProgrammePage::factory()->create([
            'type' => ProgrammePage::TYPE_ARCHIVE_THEME,
            'category_id' => $themeCategory->id,
            'page_title' => 'Connected Archive Theme',
            'title' => '<p>Archive Climate Theme</p>',
            'location' => 'Archive Convention Center',
            'event_date' => 'September 1, 2025',
            'content' => '<p>Archive theme details</p>',
        ]);
        $programme = ProgrammePage::factory()->create([
            'type' => ProgrammePage::TYPE_ARCHIVE_PROGRAMME,
            'category_id' => $programmeCategory->id,
            'page_title' => 'Connected Archive Programme',
            'title' => '<p>Archive Programme Title</p>',
            'content' => '<table><tr><td>Archive programme schedule</td></tr></table>',
        ]);
        $speakers = ProgrammePage::factory()->create([
            'type' => ProgrammePage::TYPE_ARCHIVE_SPEAKERS,
            'page_title' => 'Connected Archive Speakers',
        ]);
        $dayTwo = $speakers->sessions()->create([
            'day_number' => 2,
            'session_name' => 'Archive DAY 2 Session',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $speaker = Speaker::factory()->create([
            'first_name' => 'Archive',
            'last_name' => 'Speaker',
            'is_active' => true,
        ]);
        $dayTwo->speakers()->attach($speaker->id, ['sort_order' => 1]);
        $legacy = ProgrammePage::factory()->create([
            'type' => ProgrammePage::TYPE_ARCHIVE_LEGACY,
            'page_title' => 'Connected Legacy Archive',
            'subtitle' => '<p>Connected legacy subtitle</p>',
            'content' => '<p>Connected forum history 2015 through 2024</p>',
        ]);

        foreach ([
            MainPageLink::SLOT_ARCHIVE_THEME => $theme,
            MainPageLink::SLOT_ARCHIVE_PROGRAMME => $programme,
            MainPageLink::SLOT_ARCHIVE_SPEAKERS => $speakers,
            MainPageLink::SLOT_ARCHIVE_LEGACY => $legacy,
        ] as $slot => $page) {
            $mainPage->links()->create([
                'slot' => $slot,
                'linkable_type' => $page->getMorphClass(),
                'linkable_id' => $page->id,
            ]);
        }

        $this->get('/'.$folder.'/archive/theme')
            ->assertOk()
            ->assertSee('Archive Climate Theme')
            ->assertSee('Archive theme details')
            ->assertDontSee('Climate-Smart Innovations for Sustainable Local Economies');
        $this->get('/'.$folder.'/archive/programme')
            ->assertOk()
            ->assertSee('Archive Programme Title')
            ->assertSee('Archive programme schedule')
            ->assertDontSee('Opening Ceremony');
        $this->get('/'.$folder.'/archive/speakers')
            ->assertOk()
            ->assertSee('DAY 2')
            ->assertSee('Archive DAY 2 Session')
            ->assertSee('Archive Speaker')
            ->assertDontSee('Giulia Ajmone');
        $this->get('/'.$folder.'/archive/past')
            ->assertOk()
            ->assertSeeInOrder([
                '<h1 class="tit_pagename">Past Forums (2015~2024)</h1>',
                '<p>Connected legacy subtitle</p>',
            ], false)
            ->assertSee('Connected forum history 2015 through 2024')
            ->assertDontSee('November 14–15, 2024');
    }

    public function test_unconnected_archive_pages_do_not_render_publishing_samples(): void
    {
        $folder = 'empty-archive-'.Str::lower(Str::random(10));
        MainPage::factory()->create(['folder_name' => $folder, 'is_visible' => true]);

        $this->get('/'.$folder.'/archive/theme')->assertOk()->assertDontSee('Climate-Smart Innovations');
        $this->get('/'.$folder.'/archive/programme')->assertOk()->assertDontSee('Opening Ceremony');
        $this->get('/'.$folder.'/archive/speakers')->assertOk()->assertDontSee('Giulia Ajmone');
        $this->get('/'.$folder.'/archive/past')->assertOk()->assertDontSee('November 14–15, 2024');
    }

    public function test_archive_theme_and_programme_year_tabs_switch_linked_main_page_content(): void
    {
        $mainPage2025 = MainPage::factory()->create([
            'folder_name' => '2025-archive-'.Str::lower(Str::random(6)),
            'event_name' => '2025 Archive Forum',
            'event_start_date' => '2025-09-01',
            'is_visible' => true,
        ]);
        $mainPage2026 = MainPage::factory()->create([
            'folder_name' => '2026-archive-'.Str::lower(Str::random(6)),
            'event_name' => '2026 Archive Forum',
            'event_start_date' => '2026-09-01',
            'is_visible' => true,
        ]);

        foreach ([
            [ProgrammePage::TYPE_ARCHIVE_THEME, MainPageLink::SLOT_ARCHIVE_THEME, Category::GROUP_CODE_ARCHIVE_THEME, 'theme'],
            [ProgrammePage::TYPE_ARCHIVE_PROGRAMME, MainPageLink::SLOT_ARCHIVE_PROGRAMME, Category::GROUP_CODE_ARCHIVE_PROGRAMME, 'programme'],
        ] as [$type, $slot, $groupCode, $routeSegment]) {
            $category2025 = $this->mediaCategory($groupCode, '2025', 100);
            $category2026 = $this->mediaCategory($groupCode, '2026', 200);
            $olderPage = ProgrammePage::factory()->create([
                'type' => $type,
                'category_id' => $category2025->id,
                'page_title' => '2025 Archive '.$routeSegment,
                'content' => '<p>Selected 2025 '.$routeSegment.' content</p>',
            ]);
            $newerPage = ProgrammePage::factory()->create([
                'type' => $type,
                'category_id' => $category2026->id,
                'page_title' => '2026 Archive '.$routeSegment,
                'content' => '<p>Selected 2026 '.$routeSegment.' content</p>',
            ]);
            $mainPage2025->links()->create([
                'slot' => $slot,
                'linkable_type' => $olderPage->getMorphClass(),
                'linkable_id' => $olderPage->id,
            ]);
            $mainPage2026->links()->create([
                'slot' => $slot,
                'linkable_type' => $newerPage->getMorphClass(),
                'linkable_id' => $newerPage->id,
            ]);

            $url = '/'.$mainPage2026->folder_name.'/archive/'.$routeSegment;
            $this->get($url)
                ->assertOk()
                ->assertSeeInOrder(['>2026</button>', '>2025</button>'], false)
                ->assertSee('Selected 2026 '.$routeSegment.' content')
                ->assertDontSee('Selected 2025 '.$routeSegment.' content');

            $this->get($url.'?category_id='.$category2025->id)
                ->assertOk()
                ->assertSee('aria-selected="true">2025</button>', false)
                ->assertSee('Selected 2025 '.$routeSegment.' content')
                ->assertDontSee('Selected 2026 '.$routeSegment.' content');
        }
    }

    public function test_global_media_content_is_visible_on_forum_routes_with_filters_and_detail(): void
    {
        $folder = 'media-content-'.Str::lower(Str::random(10));
        MainPage::factory()->create(['folder_name' => $folder, 'is_visible' => true]);

        $photoCategory = $this->mediaCategory(Category::GROUP_CODE_PHOTO_GALLERY, '2026 Photos');
        $newsCategory = $this->mediaCategory(Category::GROUP_CODE_NEWS_CLIPPINGS, '2026 News');
        $youtubeCategory = $this->mediaCategory(Category::GROUP_CODE_YOUTUBE_CHANNEL, '2099');

        MediaContent::query()->create([
            'type' => MediaContent::TYPE_PHOTO_ITEM,
            'category_id' => $photoCategory->id,
            'page_title' => 'Photo Gallery',
            'title' => 'Connected Gallery Photo',
            'image_path' => 'media/photos/connected.jpg',
            'is_visible' => true,
        ]);
        MediaContent::query()->create([
            'type' => MediaContent::TYPE_PHOTO_ITEM,
            'category_id' => $photoCategory->id,
            'page_title' => 'Photo Gallery',
            'title' => 'Hidden Gallery Photo',
            'image_path' => 'media/photos/hidden.jpg',
            'is_visible' => false,
        ]);
        $news = MediaContent::query()->create([
            'type' => MediaContent::TYPE_NEWS_ITEM,
            'category_id' => $newsCategory->id,
            'page_title' => 'News Clippings',
            'title' => 'Connected Global News',
            'content' => '<p>Unique searchable archive media content</p>',
            'published_date' => '2026-08-31',
            'view_count' => 17,
            'image_path' => 'media/photos/news.jpg',
            'is_visible' => true,
        ]);
        MediaContent::query()->create([
            'type' => MediaContent::TYPE_YOUTUBE,
            'category_id' => $youtubeCategory->id,
            'page_title' => '2026',
            'subtitle' => 'Connected video subtitle',
            'title' => 'Connected Forum Video',
            'link' => 'https://www.youtube.com/watch?v=Connected_2026',
            'is_visible' => true,
        ]);

        $this->get('/'.$folder.'/media/gallery')
            ->assertOk()
            ->assertSee('<h1 class="tit_pagename">Photo Gallery</h1>', false)
            ->assertSee('2026 Photos')
            ->assertSee('Connected Gallery Photo')
            ->assertSee('<nav class="paging"', false)
            ->assertSee('aria-current="page"', false)
            ->assertSee('>1</a>', false)
            ->assertSee('/storage/media/photos/connected.jpg', false)
            ->assertDontSee('Hidden Gallery Photo')
            ->assertDontSee('img_sample_gallery');
        $this->get('/'.$folder.'/media/news?search_condition=content&search_keyword=searchable')
            ->assertOk()
            ->assertSee('<h1 class="tit_pagename">News Clippings</h1>', false)
            ->assertSee('Connected Global News')
            ->assertSee('Unique searchable archive media content')
            ->assertSee('<nav class="paging"', false)
            ->assertSee('aria-current="page"', false)
            ->assertSee('>1</a>', false)
            ->assertDontSee('Global Forum Highlights Innovation');
        $this->get('/'.$folder.'/media/news/view/'.$news->id)
            ->assertOk()
            ->assertSee('Connected Global News')
            ->assertSee('2026.08.31')
            ->assertSee('17')
            ->assertSee('Unique searchable archive media content')
            ->assertSee('/storage/media/photos/news.jpg', false);
        $this->get('/'.$folder.'/media/youtube')
            ->assertOk()
            ->assertSee('<h1 class="tit_pagename">Youtube Channel</h1>', false)
            ->assertSee('Connected Forum Video')
            ->assertDontSee('Connected video subtitle')
            ->assertSee('https://www.youtube.com/embed/Connected_2026', false)
            ->assertDontSee('iYpl9ExsFjg');
    }

    public function test_media_year_tabs_are_ordered_from_newest_to_oldest(): void
    {
        $folder = 'media-tab-order-'.Str::lower(Str::random(10));
        MainPage::factory()->create(['folder_name' => $folder, 'is_visible' => true]);

        $photo2025 = $this->mediaCategory(Category::GROUP_CODE_PHOTO_GALLERY, '2025', 200);
        $photo2026 = $this->mediaCategory(Category::GROUP_CODE_PHOTO_GALLERY, '2026', 100);
        $news2025 = $this->mediaCategory(Category::GROUP_CODE_NEWS_CLIPPINGS, '2025 News', 200);
        $news2026 = $this->mediaCategory(Category::GROUP_CODE_NEWS_CLIPPINGS, '2026 News', 100);

        foreach ([
            [$photo2025, MediaContent::TYPE_PHOTO_ITEM, '2025 Gallery Item'],
            [$photo2026, MediaContent::TYPE_PHOTO_ITEM, '2026 Gallery Item'],
            [$news2025, MediaContent::TYPE_NEWS_ITEM, '2025 News Item'],
            [$news2026, MediaContent::TYPE_NEWS_ITEM, '2026 News Item'],
        ] as [$category, $type, $title]) {
            MediaContent::query()->create([
                'type' => $type,
                'category_id' => $category->id,
                'page_title' => $category->name,
                'title' => $title,
                'image_path' => $type === MediaContent::TYPE_PHOTO_ITEM ? 'media/photos/'.$category->id.'.jpg' : null,
                'is_visible' => true,
            ]);
        }

        $youtubeCategory2025 = $this->mediaCategory(Category::GROUP_CODE_YOUTUBE_CHANNEL, '2025', 200);
        $youtubeCategory2026 = $this->mediaCategory(Category::GROUP_CODE_YOUTUBE_CHANNEL, '2026', 100);
        MediaContent::query()->create([
            'type' => MediaContent::TYPE_YOUTUBE,
            'category_id' => $youtubeCategory2025->id,
            'page_title' => '2025 YouTube',
            'title' => 'Older Video',
            'link' => 'https://youtu.be/OlderVideo',
            'sort_order' => 1,
            'is_visible' => true,
        ]);
        MediaContent::query()->create([
            'type' => MediaContent::TYPE_YOUTUBE,
            'category_id' => $youtubeCategory2026->id,
            'page_title' => '2026 YouTube',
            'title' => 'Newer Video',
            'link' => 'https://youtu.be/NewerVideo',
            'sort_order' => 999,
            'is_visible' => true,
        ]);

        $this->get('/'.$folder.'/media/gallery')
            ->assertOk()
            ->assertSeeInOrder([
                'name="category_id" value="'.$photo2026->id.'"',
                'name="category_id" value="'.$photo2025->id.'"',
            ], false)
            ->assertSee('2026 Gallery Item')
            ->assertDontSee('2025 Gallery Item');

        $this->get('/'.$folder.'/media/news')
            ->assertOk()
            ->assertSeeInOrder([
                'name="category_id" value="'.$news2026->id.'"',
                'name="category_id" value="'.$news2025->id.'"',
            ], false)
            ->assertSee('2026 News Item')
            ->assertDontSee('2025 News Item');

        $this->get('/'.$folder.'/media/youtube')
            ->assertOk()
            ->assertSee('class="years_select_tab flex"', false)
            ->assertSeeInOrder([
                'name="category_id" value="'.$youtubeCategory2026->id.'"',
                'name="category_id" value="'.$youtubeCategory2025->id.'"',
            ], false)
            ->assertSee('Newer Video')
            ->assertDontSee('Older Video');
    }

    private function mediaCategory(string $groupCode, string $name, int $displayOrder = 99999): Category
    {
        $group = Category::query()->where('code', $groupCode)->firstOrFail();

        return Category::query()->create([
            'parent_id' => $group->id,
            'code' => Str::upper(Str::random(10)),
            'name' => $name,
            'depth' => 1,
            'display_order' => $displayOrder,
            'is_active' => true,
        ]);
    }
}
