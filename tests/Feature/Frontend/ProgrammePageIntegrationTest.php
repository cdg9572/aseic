<?php

namespace Tests\Feature\Frontend;

use App\Models\MainPage;
use App\Models\MainPageLink;
use App\Models\ProgrammePage;
use App\Models\Speaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProgrammePageIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_unconnected_programme_pages_do_not_render_publishing_samples(): void
    {
        $folder = 'empty-programme-'.Str::lower(Str::random(10));
        MainPage::factory()->create([
            'folder_name' => $folder,
            'is_visible' => true,
        ]);

        $this->get('/'.$folder.'/programme/theme')
            ->assertOk()
            ->assertDontSee('Climate-Smart Innovations for Sustainable Local Economies')
            ->assertDontSee('Explore the forum programme');
        $this->get('/'.$folder.'/programme')
            ->assertOk()
            ->assertDontSee('Opening Ceremony');
        $this->get('/'.$folder.'/programme/speakers')
            ->assertOk()
            ->assertDontSee('Giulia Ajmone');
        $this->get('/'.$folder.'/programme/book')
            ->assertOk()
            ->assertDontSee('Global Eco-Innovation Forum 2026_programme Book');
    }

    public function test_connected_programme_pages_render_their_backoffice_content(): void
    {
        $folder = 'programme-content-'.Str::lower(Str::random(10));
        $mainPage = MainPage::factory()->create([
            'folder_name' => $folder,
            'event_name' => 'Connected Programme Forum',
            'is_visible' => true,
        ]);

        $theme = ProgrammePage::factory()->create([
            'type' => ProgrammePage::TYPE_THEME,
            'page_title' => 'Connected Theme',
            'subtitle' => '<p>Connected theme subtitle</p>',
            'title' => '<p>Connected Climate Theme</p>',
            'location' => 'Connected Convention Center',
            'event_date' => 'September 10, 2027',
            'content' => '<p>Connected theme details</p>',
        ]);

        $programme = ProgrammePage::factory()->create([
            'type' => ProgrammePage::TYPE_PROGRAMME,
            'page_title' => 'Connected Programme',
            'subtitle' => '<p>Connected programme subtitle</p>',
            'title' => null,
            'location' => null,
            'event_date' => null,
            'content' => '<table><tbody><tr><td>Connected programme schedule</td></tr></tbody></table>',
        ]);

        $speakers = $this->speakersPage();
        $book = $this->bookPage();

        foreach ([
            MainPageLink::SLOT_PROGRAMME_THEME => $theme,
            MainPageLink::SLOT_PROGRAMME => $programme,
            MainPageLink::SLOT_PROGRAMME_SPEAKERS => $speakers,
            MainPageLink::SLOT_PROGRAMME_BOOK => $book,
        ] as $slot => $programmePage) {
            $mainPage->links()->create([
                'slot' => $slot,
                'linkable_type' => $programmePage->getMorphClass(),
                'linkable_id' => $programmePage->id,
            ]);
        }

        $this->assertThemePage($folder);
        $this->assertProgrammePage($folder);
        $this->assertSpeakersPage($folder);
        $this->assertBookPage($folder);
    }

    private function speakersPage(): ProgrammePage
    {
        $dayOneSpeaker = Speaker::factory()->create([
            'first_name' => 'Day One',
            'last_name' => 'Speaker',
            'position' => 'Connected Position',
            'affiliation' => 'Connected Organization',
            'profile_image' => 'speakers/day-one.png',
            'role' => Speaker::ROLE_MODERATOR,
            'is_active' => true,
            'is_image_visible' => true,
            'content' => '<p>Connected speaker biography</p>',
            'attachments' => [[
                'path' => 'speakers/connected-profile.pdf',
                'name' => 'Connected Profile.pdf',
                'size' => 1024,
            ]],
        ]);
        $dayTwoSpeaker = Speaker::factory()->create([
            'first_name' => 'Day Two',
            'last_name' => 'Speaker',
            'role' => Speaker::ROLE_PANEL,
            'is_active' => true,
            'is_image_visible' => false,
        ]);
        $inactiveSpeaker = Speaker::factory()->create([
            'first_name' => 'Inactive',
            'last_name' => 'Speaker',
            'is_active' => false,
        ]);

        $page = ProgrammePage::factory()->create([
            'type' => ProgrammePage::TYPE_SPEAKERS,
            'page_title' => 'Connected Speakers',
            'subtitle' => '<p>Connected speakers subtitle</p>',
            'title' => null,
            'location' => null,
            'event_date' => null,
            'content' => null,
        ]);
        $dayOne = $page->sessions()->create([
            'day_number' => 1,
            'session_name' => 'Connected DAY 1 Session',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $dayOne->speakers()->attach([
            $dayOneSpeaker->id => ['sort_order' => 1],
            $inactiveSpeaker->id => ['sort_order' => 2],
        ]);
        $dayTwo = $page->sessions()->create([
            'day_number' => 2,
            'session_name' => 'Connected DAY 2 Session',
            'is_active' => true,
            'sort_order' => 2,
        ]);
        $dayTwo->speakers()->attach($dayTwoSpeaker->id, ['sort_order' => 1]);

        return $page;
    }

    private function bookPage(): ProgrammePage
    {
        $page = ProgrammePage::factory()->create([
            'type' => ProgrammePage::TYPE_BOOK,
            'page_title' => 'Connected Programme Book',
            'subtitle' => '<p>Connected book subtitle</p>',
            'title' => null,
            'location' => null,
            'event_date' => null,
            'content' => null,
        ]);
        $page->books()->create([
            'title' => 'Connected Programme PDF',
            'file_path' => 'programme/books/connected-programme.pdf',
            'file_name' => 'Connected Programme.pdf',
            'file_size' => 2048,
            'link' => null,
            'sort_order' => 1,
        ]);
        $page->books()->create([
            'title' => 'Connected Technology Appendix',
            'file_path' => null,
            'file_name' => null,
            'file_size' => null,
            'link' => 'https://example.com/programme-appendix',
            'sort_order' => 2,
        ]);

        return $page;
    }

    private function assertThemePage(string $folder): void
    {
        $this->get('/'.$folder.'/programme/theme')
            ->assertOk()
            ->assertSeeInOrder([
                '<h1 class="tit_pagename">Theme</h1>',
                '<p>Connected theme subtitle</p>',
            ], false)
            ->assertSee('Connected Theme')
            ->assertSee('Connected theme subtitle')
            ->assertSee('Connected Programme Forum')
            ->assertSee('Connected Climate Theme')
            ->assertSee('Connected Convention Center')
            ->assertSee('September 10, 2027')
            ->assertSee('Connected theme details')
            ->assertDontSee('Climate-Smart Innovations for Sustainable Local Economies');
    }

    private function assertProgrammePage(string $folder): void
    {
        $this->get('/'.$folder.'/programme')
            ->assertOk()
            ->assertSee('<h1 class="tit_pagename">Programme</h1>', false)
            ->assertSee('Connected Programme')
            ->assertSee('Connected programme subtitle')
            ->assertSee('Connected programme schedule')
            ->assertDontSee('Opening Ceremony');
    }

    private function assertSpeakersPage(string $folder): void
    {
        $this->get('/'.$folder.'/programme/speakers')
            ->assertOk()
            ->assertSee('<h1 class="tit_pagename">Speakers</h1>', false)
            ->assertSee('Connected Speakers')
            ->assertSee('Connected speakers subtitle')
            ->assertSee('Connected DAY 1 Session')
            ->assertSee('Connected DAY 2 Session')
            ->assertSee('Day One Speaker')
            ->assertSee('Day Two Speaker')
            ->assertSee('MODERATOR')
            ->assertSee('/storage/speakers/day-one.png', false)
            ->assertSee('/storage/speakers/connected-profile.pdf', false)
            ->assertDontSee('Inactive Speaker')
            ->assertDontSee('Giulia Ajmone');
    }

    private function assertBookPage(string $folder): void
    {
        $this->get('/'.$folder.'/programme/book')
            ->assertOk()
            ->assertSee('<h1 class="tit_pagename">Programme Book</h1>', false)
            ->assertSee('Connected Programme Book')
            ->assertSee('Connected book subtitle')
            ->assertSee('Connected Programme PDF')
            ->assertSee('/storage/programme/books/connected-programme.pdf', false)
            ->assertSee('Connected Technology Appendix')
            ->assertSee('https://example.com/programme-appendix', false)
            ->assertSee('<nav class="paging"', false)
            ->assertSee('aria-current="page"', false)
            ->assertSee('>1</a>', false);

        $this->get('/'.$folder.'/programme/book?search_condition=title&search_keyword=Programme+PDF')
            ->assertOk()
            ->assertSee('Connected Programme PDF')
            ->assertDontSee('Connected Technology Appendix');
    }
}
