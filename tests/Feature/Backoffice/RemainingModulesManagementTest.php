<?php

namespace Tests\Feature\Backoffice;

use App\Jobs\SendMailCampaign;
use App\Models\Category;
use App\Models\AddressBook;
use App\Models\AddressBookContact;
use App\Models\MailCampaign;
use App\Models\MainPage;
use App\Models\MainPageLink;
use App\Models\MediaContent;
use App\Models\ProgrammePage;
use App\Models\RegistrationApplicant;
use App\Models\RegistrationPage;
use App\Models\Speaker;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RemainingModulesManagementTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;

    private MainPage $mainPage;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        $this->mainPage = MainPage::factory()->create(['folder_name' => 'remaining-'.strtolower(str()->random(8)), 'event_name' => 'Remaining Modules Test']);
    }

    public function test_all_remaining_menu_list_and_create_screens_render(): void
    {
        foreach ([
            '/backoffice/archives/2025-plus/theme',
            '/backoffice/archives/2025-plus/programme',
            '/backoffice/archives/2025-plus/speakers',
            '/backoffice/archives/2015-2024',
            '/backoffice/media/photo-gallery',
            '/backoffice/media/news-clippings',
            '/backoffice/media/youtube',
            '/backoffice/registration',
            '/backoffice/registration/applicants',
            '/backoffice/address-books',
            '/backoffice/mail-campaigns',
            '/backoffice/board-posts/notices',
        ] as $url) {
            $this->actingAs($this->admin)->get($url)->assertOk();
        }

        foreach ([
            '/backoffice/archives/2025-plus/theme/create',
            '/backoffice/archives/2025-plus/programme/create',
            '/backoffice/archives/2025-plus/speakers/create',
            '/backoffice/archives/2015-2024/create',
            '/backoffice/media/photo-gallery/create',
            '/backoffice/media/news-clippings/create',
            '/backoffice/media/youtube/create',
            '/backoffice/registration/create',
            '/backoffice/registration/applicants/create',
            '/backoffice/address-books/create',
            '/backoffice/mail-campaigns/create',
            '/backoffice/board-posts/notices/create',
        ] as $url) {
            $this->actingAs($this->admin)->get($url)->assertOk();
        }

    }

    public function test_archive_types_are_separate_and_can_map_to_main_page(): void
    {
        $this->actingAs($this->admin)->get('/backoffice/archives/2025-plus/theme/create')
            ->assertOk()
            ->assertSee('Past Forums (2025~) - Theme 신규 등록')
            ->assertSee('Main Page 연결')
            ->assertSee('name="main_page_id"', false)
            ->assertSee('연결하지 않음')
            ->assertSee('name="subtitle"', false)
            ->assertSee('name="title"', false)
            ->assertSee('name="location"', false)
            ->assertSee('name="event_date"', false)
            ->assertSee('name="content"', false)
            ->assertSee('>Theme</label>', false);

        $this->actingAs($this->admin)->get('/backoffice/archives/2025-plus/programme/create')
            ->assertOk()
            ->assertSeeInOrder([
                'Main Page 연결',
                'name="page_title"',
                'name="subtitle"',
                'name="title"',
                'name="location"',
                'name="event_date"',
                '>Theme</label>',
                'name="content"',
            ], false)
            ->assertSee('name="subtitle"', false)
            ->assertSee('name="title"', false)
            ->assertSee('name="location"', false)
            ->assertSee('name="event_date"', false)
            ->assertSee('name="content"', false)
            ->assertSee('>Theme</label>', false);

        $archiveSpeakersCreateResponse = $this->actingAs($this->admin)->get('/backoffice/archives/2025-plus/speakers/create');
        $archiveSpeakersCreateResponse->assertOk()
            ->assertSeeInOrder(['Main Page 연결', 'name="page_title"', 'name="subtitle"', 'DAY 1 SESSION', 'DAY 2 SESSION'], false)
            ->assertDontSee('data-programme-session-add', false)
            ->assertDontSee('data-programme-session-remove', false);
        $this->assertSame(2, substr_count($archiveSpeakersCreateResponse->getContent(), 'data-about-picker-open'));

        $this->actingAs($this->admin)->get('/backoffice/archives/2015-2024/create')
            ->assertOk()
            ->assertSeeInOrder(['Sub Title', '>내용</label>'], false)
            ->assertSee('name="subtitle"', false)
            ->assertSee('name="content"', false)
            ->assertSee('>내용</label>', false)
            ->assertDontSee('>Past Forums (2015~2024)</label>', false)
            ->assertDontSee('name="location"', false)
            ->assertDontSee('name="event_date"', false);

        $this->actingAs($this->admin)->post('/backoffice/archives/2025-plus/theme', [
            'main_page_id' => $this->mainPage->id,
            'page_title' => '2026 Archive Theme',
            'subtitle' => '<p>Archive subtitle</p>',
            'title' => '<p>Archive title</p>',
            'location' => 'Seoul',
            'event_date' => '2026.08.29',
            'content' => '<p>Theme content</p>',
        ])->assertRedirect(route('backoffice.archive-theme.index'));

        $theme = ProgrammePage::query()->where('type', ProgrammePage::TYPE_ARCHIVE_THEME)->where('page_title', '2026 Archive Theme')->firstOrFail();
        $this->assertSame('Seoul', $theme->location);
        $this->assertSame('2026.08.29', $theme->event_date);
        $this->assertDatabaseHas('main_page_links', ['main_page_id' => $this->mainPage->id, 'slot' => MainPageLink::SLOT_ARCHIVE_THEME, 'linkable_id' => $theme->id]);

        $this->actingAs($this->admin)->post('/backoffice/archives/2025-plus/programme', [
            'main_page_id' => $this->mainPage->id,
            'page_title' => '2026 Archive Programme',
            'subtitle' => '<p>Programme subtitle</p>',
            'title' => '<p>Programme title</p>',
            'location' => 'Busan',
            'event_date' => '2026.09.01',
            'content' => '<p>Programme theme</p>',
        ])->assertRedirect(route('backoffice.archive-programme.index'));

        $archiveProgramme = ProgrammePage::query()
            ->where('type', ProgrammePage::TYPE_ARCHIVE_PROGRAMME)
            ->where('page_title', '2026 Archive Programme')
            ->firstOrFail();
        $this->assertSame('<p>Programme title</p>', $archiveProgramme->title);
        $this->assertSame('Busan', $archiveProgramme->location);
        $this->assertSame('2026.09.01', $archiveProgramme->event_date);
        $this->assertSame('<p>Programme theme</p>', $archiveProgramme->content);
        $this->assertDatabaseHas('main_page_links', ['main_page_id' => $this->mainPage->id, 'slot' => MainPageLink::SLOT_ARCHIVE_PROGRAMME, 'linkable_id' => $archiveProgramme->id]);

        $speaker = Speaker::factory()->create(['is_active' => true]);
        $this->actingAs($this->admin)->post('/backoffice/archives/2025-plus/speakers', [
            'page_title' => '2026 Archive Speakers',
            'sessions' => [
                ['is_active' => '1', 'session_name' => 'DAY 1', 'speaker_ids' => [$speaker->id]],
                ['is_active' => '0', 'session_name' => null, 'speaker_ids' => []],
            ],
        ])->assertRedirect(route('backoffice.archive-speakers.index'));

        $archiveSpeakers = ProgrammePage::query()->where('type', ProgrammePage::TYPE_ARCHIVE_SPEAKERS)->where('page_title', '2026 Archive Speakers')->firstOrFail()->load('sessions.speakers');
        $this->assertCount(2, $archiveSpeakers->sessions);
        $this->assertSame([$speaker->id], $archiveSpeakers->sessions->firstWhere('day_number', 1)->speakers->pluck('id')->all());

        $this->actingAs($this->admin)->get('/backoffice/main-pages/'.$this->mainPage->id.'/edit')
            ->assertOk()
            ->assertSee('2026 Archive Theme');
    }

    public function test_remaining_create_forms_include_the_planned_fields(): void
    {
        $this->actingAs($this->admin)->get('/backoffice/media/photo-gallery/create')
            ->assertOk()
            ->assertSee('>제목 <span class="required">*</span></label>', false)
            ->assertDontSee('제목(폴더명)')
            ->assertSee('name="category_id"', false)
            ->assertSee('name="title"', false)
            ->assertSee('>Photo ', false)
            ->assertSee('name="image"', false)
            ->assertSee('name="is_visible"', false);

        $this->actingAs($this->admin)->get('/backoffice/media/news-clippings/create')
            ->assertOk()
            ->assertSeeInOrder([
                'name="category_id"',
                '뉴스 제목',
                'name="published_date"',
                'name="view_count"',
                'name="content"',
                '사진 첨부',
                'name="image"',
                'name="is_visible"',
            ], false)
            ->assertSee('data-max-files="1"', false)
            ->assertDontSee('name="image[]"', false)
            ->assertDontSee(' multiple', false)
            ->assertDontSee('name="link"', false)
            ->assertDontSee('name="page_title"', false)
            ->assertDontSee('name="subtitle"', false);

        $this->actingAs($this->admin)->get('/backoffice/media/youtube/create')
            ->assertOk()
            ->assertSee('name="page_title"', false)
            ->assertSee('name="subtitle"', false)
            ->assertSee('name="title"', false)
            ->assertSee('name="link"', false)
            ->assertSee('name="is_visible"', false);

        $this->actingAs($this->admin)->get('/backoffice/registration/create')
            ->assertOk()
            ->assertSee('name="main_page_id"', false)
            ->assertSee('name="page_title"', false)
            ->assertSee('name="subtitle"', false)
            ->assertSee('name="participation_mode"', false)
            ->assertSee('class="board-radio-group"', false)
            ->assertSee('class="board-radio-item"', false)
            ->assertSee('name="period_text"', false)
            ->assertSee('name="guide_step_1"', false)
            ->assertSee('name="guide_step_2"', false)
            ->assertSee('name="guide_step_3"', false)
            ->assertSee('name="registration_start_date"', false)
            ->assertSee('name="registration_end_date"', false)
            ->assertSee('name="closed_notice"', false);

        $this->actingAs($this->admin)->get('/backoffice/board-posts/notices/create')
            ->assertOk()
            ->assertSee('name="custom_field_subtitle"', false)
            ->assertSee('name="is_notice"', false)
            ->assertSee('name="title"', false)
            ->assertSee('name="content"', false)
            ->assertSee('name="view_count"', false)
            ->assertSee('name="attachments[]"', false);

        $this->actingAs($this->admin)->get('/backoffice/address-books/create')
            ->assertOk()
            ->assertSee('name="name"', false)
            ->assertSee('name="import_file"', false)
            ->assertSee('name="contacts[0][name]"', false)
            ->assertSee('name="contacts[0][email]"', false);

        $this->actingAs($this->admin)->get('/backoffice/mail-campaigns/create')
            ->assertOk()
            ->assertSee('name="sender_name"', false)
            ->assertSee('name="sender_email"', false)
            ->assertDontSee('name="reply_name"', false)
            ->assertDontSee('name="reply_email"', false)
            ->assertSee('name="subject"', false)
            ->assertSee('name="target_type"', false)
            ->assertSee('name="subscription_status"', false)
            ->assertSee('name="content"', false)
            ->assertSee('name="attachments[]"', false);
    }

    public function test_media_categories_and_youtube_crud_follow_planned_fields(): void
    {
        Storage::fake('public');

        $photoGroup = Category::query()->where('code', Category::GROUP_CODE_PHOTO_GALLERY)->firstOrFail();
        $photoCategory = Category::query()->create([
            'parent_id' => $photoGroup->id,
            'code' => 'PHOTO_2026',
            'name' => '2026',
            'depth' => 1,
            'display_order' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)->post('/backoffice/media/photo-gallery', [
            'category_id' => $photoCategory->id,
            'title' => 'Forum Opening',
            'image' => UploadedFile::fake()->image('opening-photo.jpg'),
            'is_visible' => '1',
        ])->assertRedirect(route('backoffice.media-photo.index'));
        $photo = MediaContent::query()->where('type', MediaContent::TYPE_PHOTO_ITEM)->where('title', 'Forum Opening')->firstOrFail();
        $this->assertSame($photoCategory->id, $photo->category_id);
        $this->assertSame('opening-photo.jpg', $photo->image_name);
        Storage::disk('public')->assertExists($photo->image_path);

        $photoImagePath = $photo->image_path;
        $this->actingAs($this->admin)->get('/backoffice/media/photo-gallery/'.$photo->id.'/edit')
            ->assertOk()
            ->assertSee('opening-photo.jpg')
            ->assertSee('2026');

        $this->actingAs($this->admin)->put('/backoffice/media/photo-gallery/'.$photo->id, [
            'category_id' => $photoCategory->id,
            'title' => 'Forum Opening Updated',
            'remove_image' => '1',
            'is_visible' => '1',
        ])->assertRedirect(route('backoffice.media-photo.index'));
        $photo->refresh();
        $this->assertSame('Forum Opening Updated', $photo->title);
        $this->assertNull($photo->image_path);
        Storage::disk('public')->assertMissing($photoImagePath);

        $this->actingAs($this->admin)->get('/backoffice/media/photo-gallery?category_id='.$photoCategory->id)
            ->assertOk()
            ->assertSee('2026')
            ->assertSee('Forum Opening Updated');

        $newsGroup = Category::query()->where('code', Category::GROUP_CODE_NEWS_CLIPPINGS)->firstOrFail();
        $newsCategory = Category::query()->create([
            'parent_id' => $newsGroup->id,
            'code' => 'NEWS_2026',
            'name' => '2026 News',
            'depth' => 1,
            'display_order' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)->post('/backoffice/media/news-clippings', [
            'category_id' => $photoCategory->id,
            'title' => 'Wrong News Category',
            'is_visible' => '1',
        ])->assertSessionHasErrors('category_id');

        $this->actingAs($this->admin)->post('/backoffice/media/news-clippings', [
            'category_id' => $newsCategory->id,
            'title' => 'ASEIC News',
            'content' => '<p>News content</p>',
            'published_date' => '2026-08-29',
            'view_count' => 12,
            'image' => UploadedFile::fake()->image('aseic-news.jpg'),
            'is_visible' => '1',
        ])->assertRedirect(route('backoffice.media-news.index'));
        $news = MediaContent::query()->where('type', MediaContent::TYPE_NEWS_ITEM)->where('title', 'ASEIC News')->firstOrFail();
        $this->assertSame($newsCategory->id, $news->category_id);
        $this->assertSame(12, $news->view_count);
        $this->assertSame('aseic-news.jpg', $news->image_name);
        Storage::disk('public')->assertExists($news->image_path);
        $newsImagePath = $news->image_path;

        $this->actingAs($this->admin)->get('/backoffice/media/news-clippings/'.$news->id.'/edit')
            ->assertOk()
            ->assertSee('2026 News')
            ->assertSee('ASEIC News')
            ->assertSee('aseic-news.jpg');

        $this->actingAs($this->admin)->put('/backoffice/media/news-clippings/'.$news->id, [
            'category_id' => $newsCategory->id,
            'title' => 'ASEIC News Updated',
            'content' => '<p>Updated news content</p>',
            'published_date' => '2026-08-30',
            'view_count' => 24,
            'remove_image' => '1',
            'is_visible' => '1',
        ])->assertRedirect(route('backoffice.media-news.index'));
        $news->refresh();
        $this->assertSame(24, $news->view_count);
        $this->assertNull($news->image_path);
        Storage::disk('public')->assertMissing($newsImagePath);

        $this->actingAs($this->admin)->get('/backoffice/media/news-clippings?category_id='.$newsCategory->id)
            ->assertOk()
            ->assertSee('2026 News')
            ->assertSee('ASEIC News Updated');

        $this->actingAs($this->admin)->post('/backoffice/media/youtube', [
            'page_title' => '2026 YouTube',
            'subtitle' => '<p>YouTube subtitle</p>',
            'title' => 'ASEIC Forum Video',
            'link' => 'https://www.youtube.com/watch?v=example',
            'is_visible' => '1',
        ])->assertRedirect(route('backoffice.media-youtube.index'));

        $this->assertDatabaseHas('media_contents', ['type' => MediaContent::TYPE_NEWS_ITEM, 'title' => 'ASEIC News Updated', 'category_id' => $newsCategory->id, 'view_count' => 24]);
        $this->assertDatabaseHas('media_contents', ['type' => MediaContent::TYPE_YOUTUBE, 'title' => 'ASEIC Forum Video']);
    }

    public function test_announcements_preserve_subtitle_in_existing_notice_module(): void
    {
        $this->actingAs($this->admin)->get('/backoffice/board-posts/notices')
            ->assertOk()
            ->assertSee('Announcements')
            ->assertDontSee('<h1>공지사항</h1>', false);
        $this->actingAs($this->admin)->get('/backoffice/board-posts/notices/create')
            ->assertOk()
            ->assertSee('Announcements');

        $this->actingAs($this->admin)->post('/backoffice/board-posts/notices', [
            'custom_field_subtitle' => '<p>Announcement subtitle</p>',
            'is_notice' => '1',
            'title' => 'ASEIC Announcement',
            'content' => '<p>Announcement content</p>',
            'author_name' => 'Administrator',
            'view_count' => 12,
        ])->assertRedirect(route('backoffice.board-posts.index', 'notices'));

        $post = DB::table('board_notices')->where('title', 'ASEIC Announcement')->first();
        $this->assertNotNull($post);
        $this->actingAs($this->admin)->get('/backoffice/board-posts/notices/'.$post->id.'/edit')
            ->assertOk()
            ->assertSee('Announcements');
        $this->assertTrue((bool) $post->is_notice);
        $this->assertSame(12, $post->view_count);
        $this->assertSame(
            '<p>Announcement subtitle</p>',
            json_decode($post->custom_fields, true)['subtitle'] ?? null
        );
    }

    public function test_registration_settings_and_applicants_can_be_managed(): void
    {
        $this->actingAs($this->admin)->post('/backoffice/registration', [
            'main_page_id' => $this->mainPage->id,
            'page_title' => '2026 Registration',
            'subtitle' => '<p>Registration subtitle</p>',
            'participation_mode' => RegistrationPage::MODE_PARTICIPATING,
            'period_text' => 'August 1 - August 31',
            'guide_step_1' => 'Personal Information',
            'guide_step_2' => 'Programme Selection',
            'guide_step_3' => 'Complete',
            'registration_start_date' => '2026-08-01',
            'registration_end_date' => '2026-08-31',
            'use_custom_end_text' => '0',
        ])->assertRedirect(route('backoffice.registration.index'));

        $page = RegistrationPage::query()->where('page_title', '2026 Registration')->firstOrFail();
        $this->assertDatabaseHas('main_page_links', ['slot' => MainPageLink::SLOT_REGISTRATION, 'linkable_id' => $page->id]);

        $this->actingAs($this->admin)->post('/backoffice/registration/applicants', [
            'registration_page_id' => $page->id,
            'name' => 'Applicant One',
            'email' => 'applicant@example.com',
            'affiliation' => 'ASEIC',
            'participation_type' => 'offline',
            'status' => RegistrationApplicant::STATUS_APPROVED,
            'agreed_privacy' => '1',
        ])->assertRedirect(route('backoffice.registration-applicants.index'));

        $this->assertDatabaseHas('registration_applicants', ['registration_page_id' => $page->id, 'email' => 'applicant@example.com', 'status' => 'approved']);
    }

    public function test_registration_required_errors_are_rendered_below_the_fields(): void
    {
        $createUrl = route('backoffice.registration.create');

        $this->actingAs($this->admin)->from($createUrl)->post(route('backoffice.registration.store'), [
            'page_title' => '2026 Registration',
            'participation_mode' => RegistrationPage::MODE_PARTICIPATING,
        ])->assertRedirect($createUrl)
            ->assertSessionHasErrors(['period_text']);

        $this->actingAs($this->admin)->get($createUrl)
            ->assertOk()
            ->assertSee('id="period_text"', false)
            ->assertSee('Period를 입력해주세요.')
            ->assertDontSee('data-backoffice-validation-summary', false);
    }

    public function test_registration_applicant_required_errors_are_rendered_below_the_fields(): void
    {
        $createUrl = route('backoffice.registration-applicants.create');

        $this->actingAs($this->admin)->from($createUrl)->post(route('backoffice.registration-applicants.store'), [])
            ->assertRedirect($createUrl)
            ->assertSessionHasErrors(['registration_page_id', 'name', 'email', 'status']);

        $this->actingAs($this->admin)->get($createUrl)
            ->assertOk()
            ->assertSee('Registration을 선택해주세요.')
            ->assertSee('이름을 입력해주세요.')
            ->assertSee('이메일을 입력해주세요.')
            ->assertSee('상태를 선택해주세요.')
            ->assertDontSee('data-backoffice-validation-summary', false);
    }

    public function test_address_book_supports_manual_and_spreadsheet_contacts(): void
    {
        $csv = UploadedFile::fake()->createWithContent('contacts.csv', "이름,이메일,등록일\nImported User,imported@example.com,2026-07-05\n");
        $this->actingAs($this->admin)->post('/backoffice/address-books', [
            'name' => '2026 Participants',
            'contacts' => [
                ['name' => 'Manual User', 'email' => 'manual@example.com'],
            ],
            'import_file' => $csv,
        ])->assertRedirect(route('backoffice.address-books.index'));

        $book = AddressBook::query()->where('name', '2026 Participants')->firstOrFail()->load('contacts');
        $this->assertCount(2, $book->contacts);
        $this->assertSame(['imported@example.com', 'manual@example.com'], $book->contacts->pluck('email')->sort()->values()->all());
        $this->assertSame('2026-07-05', $book->contacts->firstWhere('email', 'imported@example.com')->created_at->format('Y-m-d'));

        $sample = $this->actingAs($this->admin)->get('/backoffice/address-books/sample');
        $sample->assertOk()->assertHeader('content-disposition');
        $this->assertStringContainsString('이름,이메일,등록일', $sample->streamedContent());
    }

    public function test_address_book_contact_management_list_supports_manual_crud(): void
    {
        $book = AddressBook::query()->create(['name' => '2026 참가자 명단', 'created_by' => $this->admin->id]);
        $existing = $book->contacts()->create([
            'name' => '기존 참가자',
            'email' => 'existing@example.com',
            'is_subscribed' => true,
        ]);

        $this->actingAs($this->admin)->get(route('backoffice.address-books.edit', $book))
            ->assertOk()
            ->assertSeeInOrder(['엑셀 등록', '엑셀 샘플', '주소록 관리 리스트'])
            ->assertSeeInOrder(['주소록 관리 리스트', '번호', '이름', '이메일', '등록일', '관리'])
            ->assertSeeInOrder(['data-address-contact-management', 'form="address-book-form"'], false)
            ->assertSee('form="address-book-form" data-skip-button', false)
            ->assertSee('form="address-contact-create-form" data-skip-button', false)
            ->assertSee('data-address-contact-add', false)
            ->assertSee('기존 참가자')
            ->assertDontSee('이메일 수신');

        $this->actingAs($this->admin)->put(route('backoffice.address-books.update', $book), [
            'name' => '수정된 참가자 명단',
        ])->assertRedirect(route('backoffice.address-books.index'));
        $this->assertDatabaseHas('address_books', [
            'id' => $book->id,
            'name' => '수정된 참가자 명단',
        ]);

        $this->actingAs($this->admin)->post(route('backoffice.address-books.contacts.store', $book), [
            'contact_name' => '수기 참가자',
            'contact_email' => 'manual@example.com',
        ])->assertRedirect(route('backoffice.address-books.edit', $book));

        $manual = AddressBookContact::query()->where('address_book_id', $book->id)->where('email', 'manual@example.com')->firstOrFail();
        $this->assertTrue($manual->is_subscribed);

        $this->actingAs($this->admin)->put(route('backoffice.address-books.contacts.update', [$book, $manual]), [
            'contact_name' => '수정 참가자',
            'contact_email' => 'updated@example.com',
            'editing_contact_id' => $manual->id,
        ])->assertRedirect(route('backoffice.address-books.edit', $book));

        $this->assertDatabaseHas('address_book_contacts', [
            'id' => $manual->id,
            'name' => '수정 참가자',
            'email' => 'updated@example.com',
        ]);

        $this->actingAs($this->admin)->post(route('backoffice.address-books.contacts.store', $book), [
            'contact_name' => '중복 참가자',
            'contact_email' => 'updated@example.com',
        ])->assertSessionHasErrors('contact_email');

        $originalCreatedAt = $manual->fresh()->created_at->toDateTimeString();
        $import = UploadedFile::fake()->createWithContent('additional.csv', "이름,이메일,등록일\n추가 참가자,additional@example.com,2026-07-06\n");
        $this->actingAs($this->admin)->put(route('backoffice.address-books.update', $book), [
            'name' => '수정된 참가자 명단',
            'import_file' => $import,
        ])->assertRedirect(route('backoffice.address-books.index'));

        $this->assertSame($originalCreatedAt, $manual->fresh()->created_at->toDateTimeString());
        $this->assertDatabaseHas('address_book_contacts', [
            'address_book_id' => $book->id,
            'email' => 'additional@example.com',
        ]);

        $this->actingAs($this->admin)->delete(route('backoffice.address-books.contacts.destroy', [$book, $existing]))
            ->assertRedirect(route('backoffice.address-books.edit', $book));
        $this->assertDatabaseMissing('address_book_contacts', ['id' => $existing->id]);
    }

    public function test_address_book_create_add_row_can_save_and_continue_to_contact_list(): void
    {
        $this->actingAs($this->admin)->get(route('backoffice.address-books.create'))
            ->assertOk()
            ->assertSee('주소록 관리 리스트')
            ->assertSeeInOrder(['번호', '이름', '이메일', '등록일', '관리'])
            ->assertSee('data-address-contact-new', false);

        $response = $this->actingAs($this->admin)->post(route('backoffice.address-books.store'), [
            'name' => '수기 주소록',
            'contacts' => [
                ['name' => '첫 연락처', 'email' => 'first@example.com', 'is_subscribed' => '1'],
            ],
            'continue_contacts' => '1',
        ]);

        $book = AddressBook::query()->where('name', '수기 주소록')->firstOrFail();
        $response->assertRedirect(route('backoffice.address-books.edit', $book));
        $this->assertDatabaseHas('address_book_contacts', [
            'address_book_id' => $book->id,
            'name' => '첫 연락처',
            'email' => 'first@example.com',
        ]);
    }

    public function test_mail_campaign_is_saved_and_queued_with_address_book_recipients(): void
    {
        Queue::fake();
        Storage::fake('public');
        $book = AddressBook::query()->create(['name' => 'Mail Targets', 'created_by' => $this->admin->id]);
        $book->contacts()->createMany([
            ['name' => 'Receiver', 'email' => 'receiver@example.com', 'is_subscribed' => true],
            ['name' => 'Opted Out', 'email' => 'out@example.com', 'is_subscribed' => false],
        ]);

        $this->actingAs($this->admin)->post('/backoffice/mail-campaigns', [
            'sender_name' => 'ASEIC',
            'sender_email' => 'forum@example.com',
            'subject' => 'ASEIC Forum Notice',
            'target_type' => MailCampaign::TARGET_ADDRESS_BOOK,
            'address_book_ids' => [$book->id],
            'subscription_status' => 'subscribed',
            'content' => '<p>Forum mail content</p>',
            'attachments' => [UploadedFile::fake()->create('forum-guide.pdf', 100, 'application/pdf')],
        ])->assertRedirect(route('backoffice.mail-campaigns.index'));

        $campaign = MailCampaign::query()->where('subject', 'ASEIC Forum Notice')->firstOrFail();
        $this->assertSame('forum-guide.pdf', $campaign->attachments[0]['name']);
        Storage::disk('public')->assertExists($campaign->attachments[0]['path']);

        $this->actingAs($this->admin)->post('/backoffice/mail-campaigns/'.$campaign->id.'/send')
            ->assertRedirect(route('backoffice.mail-campaigns.index'));

        $campaign->refresh();
        $this->assertSame(MailCampaign::STATUS_QUEUED, $campaign->status);
        $this->assertDatabaseHas('mail_campaign_recipients', ['mail_campaign_id' => $campaign->id, 'email' => 'receiver@example.com']);
        $this->assertDatabaseMissing('mail_campaign_recipients', ['mail_campaign_id' => $campaign->id, 'email' => 'out@example.com']);
        Queue::assertPushed(SendMailCampaign::class, fn (SendMailCampaign $job) => $job->campaignId === $campaign->id);

        Mail::fake();
        (new SendMailCampaign($campaign->id))->handle();
        $this->assertSame(MailCampaign::STATUS_SENT, $campaign->fresh()->status);
        Mail::assertSent(\App\Mail\CampaignMail::class, function (\App\Mail\CampaignMail $mail) use ($campaign): bool {
            $mail->build();

            return $mail->hasAttachmentFromStorageDisk(
                'public',
                $campaign->attachments[0]['path'],
                'forum-guide.pdf',
            );
        });
        Mail::assertSent(\App\Mail\CampaignMail::class, 1);
    }

    public function test_mail_campaign_shows_recipient_validation_message(): void
    {
        $response = $this->actingAs($this->admin)->from(route('backoffice.mail-campaigns.create'))->post(route('backoffice.mail-campaigns.store'), [
            'sender_name' => 'ASEIC',
            'sender_email' => 'forum@example.com',
            'subject' => 'No recipients',
            'target_type' => MailCampaign::TARGET_ADDRESS_BOOK,
            'subscription_status' => 'subscribed',
            'content' => '<p>Test content</p>',
        ]);

        $response->assertRedirect(route('backoffice.mail-campaigns.create'))
            ->assertSessionHasErrors(['address_book_ids']);

        $this->actingAs($this->admin)->get(route('backoffice.mail-campaigns.create'))
            ->assertOk()
            ->assertDontSee('data-backoffice-validation-summary', false)
            ->assertSee('주소록을 하나 이상 선택해주세요.');
    }

    public function test_mail_campaign_required_content_error_is_rendered_below_the_field(): void
    {
        $createUrl = route('backoffice.mail-campaigns.create');

        $this->actingAs($this->admin)->from($createUrl)->post(route('backoffice.mail-campaigns.store'), [
            'sender_name' => 'ASEIC',
            'sender_email' => 'forum@example.com',
            'subject' => 'Missing content',
            'target_type' => MailCampaign::TARGET_DIRECT,
            'direct_recipients' => 'recipient@example.com',
            'subscription_status' => 'subscribed',
        ])->assertRedirect($createUrl)
            ->assertSessionHasErrors(['content']);

        $this->actingAs($this->admin)->get($createUrl)
            ->assertOk()
            ->assertSee('내용을 입력해주세요.')
            ->assertDontSee('data-backoffice-validation-summary', false);
    }
}
