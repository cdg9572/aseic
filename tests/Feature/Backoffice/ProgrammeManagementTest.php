<?php

namespace Tests\Feature\Backoffice;

use App\Models\MainPage;
use App\Models\MainPageLink;
use App\Models\ProgrammePage;
use App\Models\Speaker;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProgrammeManagementTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;

    private MainPage $mainPage;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        $this->mainPage = MainPage::factory()->create([
            'folder_name' => 'programme-'.strtolower(str()->random(8)),
            'event_name' => 'ASEIC Programme Test',
        ]);
    }

    public function test_all_programme_create_screens_follow_the_planned_fields(): void
    {
        $this->actingAs($this->admin)->get('/backoffice/programme/theme/create')
            ->assertOk()
            ->assertSee('Main Page')
            ->assertSee('name="subtitle"', false)
            ->assertSee('name="title"', false)
            ->assertSee('name="location"', false)
            ->assertSee('name="event_date"', false)
            ->assertSee('name="content"', false);

        $this->actingAs($this->admin)->get('/backoffice/programme/create')
            ->assertOk()
            ->assertSee('Main Page')
            ->assertSee('name="subtitle"', false)
            ->assertSee('name="content"', false)
            ->assertDontSee('name="title"', false)
            ->assertDontSee('name="location"', false)
            ->assertDontSee('name="event_date"', false);

        $this->actingAs($this->admin)->get('/backoffice/programme/speakers/create')
            ->assertOk()
            ->assertSee('Speakers 신규 등록')
            ->assertDontSee('Programme Speakers 관리')
            ->assertSee('DAY 1 SESSION')
            ->assertSee('DAY 2 SESSION')
            ->assertDontSee('data-programme-session-add', false)
            ->assertDontSee('data-programme-session-remove', false)
            ->assertSee('추가하기')
            ->assertSee('about-detail.js?v=', false);

        $this->actingAs($this->admin)->get('/backoffice/programme/book/create')
            ->assertOk()
            ->assertSee('Programme Book 신규 등록')
            ->assertSee('class="bo-repeat-toolbar"', false)
            ->assertSee('class="bo-repeat-list"', false)
            ->assertSee('class="bo-repeat-item"', false)
            ->assertDontSee('class="bo-repeat-header"', false)
            ->assertDontSee('data-repeat-item-label', false)
            ->assertSee('data-programme-book-add', false)
            ->assertSee('data-programme-book-remove', false)
            ->assertSee('추가하기')
            ->assertSee('삭제')
            ->assertSee('파일')
            ->assertSee('Link');
    }

    public function test_programme_list_places_page_title_after_folder_title(): void
    {
        $page = ProgrammePage::factory()->create([
            'type' => ProgrammePage::TYPE_THEME,
            'page_title' => '2026 Programme Theme',
        ]);

        $this->actingAs($this->admin)->get('/backoffice/programme/theme')
            ->assertOk()
            ->assertSeeInOrder(['제목(폴더명)', '제목', 'Main Page 연결'])
            ->assertSee('class="w15">제목(폴더명)</th>', false)
            ->assertSee('class="w30">제목</th>', false)
            ->assertSee('class="w10">Main Page 연결</th>', false)
            ->assertSee('2026 Programme Theme')
            ->assertDontSee('미연결 #'.$page->id);
    }

    public function test_programme_page_title_is_required(): void
    {
        $this->actingAs($this->admin)->post('/backoffice/programme/theme', [
            'page_title' => '',
        ])->assertSessionHasErrors('page_title');
    }

    public function test_theme_and_programme_can_be_created_and_mapped_from_subpage(): void
    {
        $this->actingAs($this->admin)->post('/backoffice/programme/theme', [
            'main_page_id' => $this->mainPage->id,
            'page_title' => '2026 Programme Theme',
            'subtitle' => '<p>Theme subtitle</p>',
            'title' => '<p>Theme title</p>',
            'location' => 'Seoul',
            'event_date' => 'September 1-2, 2026',
            'content' => '<p>Theme content</p>',
        ])->assertRedirect(route('backoffice.programme-theme.index'));

        $theme = ProgrammePage::query()
            ->where('type', ProgrammePage::TYPE_THEME)
            ->where('page_title', '2026 Programme Theme')
            ->latest('id')
            ->firstOrFail();
        $this->assertSame('2026 Programme Theme', $theme->page_title);
        $this->assertDatabaseHas('main_page_links', [
            'main_page_id' => $this->mainPage->id,
            'slot' => MainPageLink::SLOT_PROGRAMME_THEME,
            'linkable_id' => $theme->id,
        ]);

        $this->actingAs($this->admin)->post('/backoffice/programme', [
            'page_title' => '2026 Programme',
            'subtitle' => '<p>Programme subtitle</p>',
            'content' => '<p>Programme content</p>',
        ])->assertRedirect(route('backoffice.programme.index'));

        $this->assertDatabaseHas('programme_pages', [
            'type' => ProgrammePage::TYPE_PROGRAMME,
            'page_title' => '2026 Programme',
            'content' => '<p>Programme content</p>',
        ]);
    }

    public function test_programme_speakers_save_two_days_and_selected_speakers(): void
    {
        $speakerOne = Speaker::factory()->create([
            'is_active' => true,
            'position' => 'Climate Role',
            'affiliation' => 'Climate Organization',
        ]);
        $speakerTwo = Speaker::factory()->create(['is_active' => true]);

        $this->actingAs($this->admin)->get('/backoffice/programme/speakers')
            ->assertOk()
            ->assertSee('<h2>Speakers</h2>', false)
            ->assertDontSee('Programme Speakers 관리');

        $this->actingAs($this->admin)->post('/backoffice/programme/speakers', [
            'main_page_id' => $this->mainPage->id,
            'page_title' => '2026 Programme Speakers',
            'subtitle' => '<p>Speakers subtitle</p>',
            'sessions' => [
                ['is_active' => '1', 'session_name' => 'Climate Session', 'speaker_ids' => [$speakerOne->id]],
                ['is_active' => '1', 'session_name' => 'Technology Session', 'speaker_ids' => [$speakerTwo->id]],
            ],
        ])->assertRedirect(route('backoffice.programme-speakers.index'));

        $page = ProgrammePage::query()
            ->where('type', ProgrammePage::TYPE_SPEAKERS)
            ->where('page_title', '2026 Programme Speakers')
            ->latest('id')
            ->firstOrFail()
            ->load('sessions.speakers');
        $this->assertSame('2026 Programme Speakers', $page->page_title);
        $this->assertCount(2, $page->sessions);
        $this->assertTrue($page->sessions->firstWhere('day_number', 2)->is_active);
        $this->assertSame([$speakerOne->id], $page->sessions->firstWhere('day_number', 1)->speakers->pluck('id')->all());
        $this->assertSame([$speakerTwo->id], $page->sessions->firstWhere('day_number', 2)->speakers->pluck('id')->all());
        $this->assertDatabaseHas('main_page_links', ['slot' => MainPageLink::SLOT_PROGRAMME_SPEAKERS, 'linkable_id' => $page->id]);

        $speakerThree = Speaker::factory()->create(['is_active' => true]);
        $editResponse = $this->actingAs($this->admin)->get('/backoffice/programme/speakers/'.$page->id.'/edit');
        $editResponse->assertOk()
            ->assertSee('about-detail.js?v=', false)
            ->assertSee('data-prevent-cross-picker-duplicates', false)
            ->assertDontSee('Climate Role')
            ->assertDontSee('Climate Organization');
        $this->assertSame(2, substr_count($editResponse->getContent(), 'data-about-picker-open'));

        $this->actingAs($this->admin)->put('/backoffice/programme/speakers/'.$page->id, [
            'page_title' => '2026 Programme Speakers',
            'sessions' => [
                ['is_active' => '1', 'session_name' => 'Climate Session', 'speaker_ids' => [$speakerOne->id]],
                ['is_active' => '1', 'session_name' => 'Technology Session', 'speaker_ids' => [$speakerTwo->id, $speakerThree->id]],
            ],
        ])->assertRedirect(route('backoffice.programme-speakers.index'));

        $page->refresh()->load('sessions.speakers');
        $this->assertCount(2, $page->sessions);
        $this->assertSame(
            [$speakerTwo->id, $speakerThree->id],
            $page->sessions->firstWhere('day_number', 2)->speakers->pluck('id')->all(),
        );
    }

    public function test_programme_speakers_rejects_the_same_speaker_across_days(): void
    {
        $speaker = Speaker::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->admin)
            ->from('/backoffice/programme/speakers/create')
            ->post('/backoffice/programme/speakers', [
                'page_title' => 'Duplicated Speakers',
                'sessions' => [
                    ['is_active' => '1', 'session_name' => 'DAY 1', 'speaker_ids' => [$speaker->id]],
                    ['is_active' => '1', 'session_name' => 'DAY 2', 'speaker_ids' => [$speaker->id]],
                ],
            ]);

        $response->assertRedirect('/backoffice/programme/speakers/create')
            ->assertSessionHasErrors([
                'sessions.0.speaker_ids',
                'sessions.1.speaker_ids',
            ]);
        $this->assertDatabaseMissing('programme_pages', ['page_title' => 'Duplicated Speakers']);
    }

    public function test_programme_book_can_manage_multiple_items_and_preserves_original_file_names(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->post('/backoffice/programme/book', [
            'main_page_id' => $this->mainPage->id,
            'page_title' => '2026 Programme Book',
            'subtitle' => '<p>Book subtitle</p>',
            'books' => [
                [
                    'title' => 'ASEIC Programme Book',
                    'file' => UploadedFile::fake()->create('ASEIC Programme Book 2026.pdf', 1200, 'application/pdf'),
                    'link' => 'https://example.com/book',
                ],
                [
                    'title' => 'ASEIC Programme Summary',
                    'file' => UploadedFile::fake()->create('ASEIC Summary 2026.pdf', 800, 'application/pdf'),
                    'link' => 'https://example.com/summary',
                ],
            ],
        ])->assertRedirect(route('backoffice.programme-book.index'));

        $page = ProgrammePage::query()
            ->where('type', ProgrammePage::TYPE_BOOK)
            ->where('page_title', '2026 Programme Book')
            ->latest('id')
            ->firstOrFail()
            ->load('books');
        $this->assertSame('2026 Programme Book', $page->page_title);
        $this->assertCount(2, $page->books);
        $this->assertSame('ASEIC Programme Book 2026.pdf', $page->books[0]->file_name);
        $this->assertSame('ASEIC Summary 2026.pdf', $page->books[1]->file_name);
        Storage::disk('public')->assertExists($page->books[0]->file_path);
        Storage::disk('public')->assertExists($page->books[1]->file_path);

        $firstBook = $page->books[0];
        $firstPath = $firstBook->file_path;
        $secondPath = $page->books[1]->file_path;

        $this->actingAs($this->admin)->get('/backoffice/programme/book/'.$page->id.'/edit')
            ->assertOk()
            ->assertSee('ASEIC Programme Book 2026.pdf')
            ->assertSee('ASEIC Summary 2026.pdf');

        $this->actingAs($this->admin)->put('/backoffice/programme/book/'.$page->id, [
            'main_page_id' => null,
            'page_title' => '2026 Programme Book Updated',
            'books' => [
                [
                    'id' => $firstBook->id,
                    'title' => 'Updated Book',
                    'link' => 'https://example.com/updated-book',
                    'remove_file' => '1',
                ],
                [
                    'title' => 'New Book',
                    'file' => UploadedFile::fake()->create('New Programme Book.pdf', 900, 'application/pdf'),
                    'link' => 'https://example.com/new-book',
                ],
            ],
        ])->assertRedirect(route('backoffice.programme-book.index'));

        $page->refresh()->load('books');
        $this->assertSame('2026 Programme Book Updated', $page->page_title);
        $this->assertCount(2, $page->books);
        $this->assertSame('Updated Book', $page->books[0]->title);
        $this->assertNull($page->books[0]->file_path);
        $this->assertSame('New Book', $page->books[1]->title);
        $this->assertSame('New Programme Book.pdf', $page->books[1]->file_name);
        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertMissing($secondPath);
        Storage::disk('public')->assertExists($page->books[1]->file_path);
        $this->assertDatabaseMissing('main_page_links', ['linkable_id' => $page->id]);
    }

    public function test_main_page_form_can_select_programme_content_and_reject_wrong_type(): void
    {
        $theme = ProgrammePage::factory()->create(['type' => ProgrammePage::TYPE_THEME, 'page_title' => 'Theme Selection Title', 'subtitle' => '<p>Theme subtitle</p>']);
        $programme = ProgrammePage::factory()->create(['type' => ProgrammePage::TYPE_PROGRAMME, 'page_title' => 'Programme Selection Title', 'subtitle' => '<p>Programme subtitle</p>']);

        $this->actingAs($this->admin)->get('/backoffice/main-pages/'.$this->mainPage->id.'/edit')
            ->assertOk()->assertSee('Theme Selection Title')->assertSee('Programme Selection Title');

        $validPayload = $this->mainPagePayload([
            'links' => [MainPageLink::SLOT_PROGRAMME_THEME => $theme->id],
        ]);
        $this->actingAs($this->admin)->put('/backoffice/main-pages/'.$this->mainPage->id, $validPayload)
            ->assertRedirect(route('backoffice.main-pages.index'));
        $this->assertDatabaseHas('main_page_links', ['slot' => MainPageLink::SLOT_PROGRAMME_THEME, 'linkable_id' => $theme->id]);

        $invalidPayload = $this->mainPagePayload([
            'links' => [MainPageLink::SLOT_PROGRAMME_THEME => $programme->id],
        ]);
        $this->actingAs($this->admin)->put('/backoffice/main-pages/'.$this->mainPage->id, $invalidPayload)
            ->assertSessionHasErrors('links.'.MainPageLink::SLOT_PROGRAMME_THEME);
    }

    /** @param array<string, mixed> $overrides */
    private function mainPagePayload(array $overrides = []): array
    {
        return array_replace([
            'is_visible' => '1',
            'event_name' => $this->mainPage->event_name,
            'event_start_date' => '2026-09-01',
            'event_end_date' => '2026-09-02',
            'use_custom_event_date' => '0',
            'programme_items' => array_fill(0, 4, ['time' => '', 'subject' => '', 'content' => '']),
        ], $overrides);
    }
}
