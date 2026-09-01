<?php

namespace Tests\Feature\Backoffice;

use App\Models\AboutPage;
use App\Models\MainPage;
use App\Models\MainPageLink;
use App\Models\Speaker;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class MainPageManagementTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;

    /** @var array<int, string> */
    private array $createdTemplateFolders = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        foreach ($this->createdTemplateFolders as $folder) {
            File::deleteDirectory(resource_path('views/forums/'.$folder));
        }

        parent::tearDown();
    }

    public function test_admin_can_create_main_page_with_links_files_and_event_template_folder(): void
    {
        Storage::fake('public');

        $folder = $this->newFolderName();
        $speaker = Speaker::factory()->create();
        $aboutPage = AboutPage::factory()->create(['type' => AboutPage::TYPE_FORUM]);

        $this->actingAs($this->admin)
            ->get('/backoffice/main-pages/create')
            ->assertOk()
            ->assertSee('연도(폴더명)')
            ->assertSee('Programme')
            ->assertSee('About the Forum')
            ->assertSee('Registration');

        $response = $this->actingAs($this->admin)->post('/backoffice/main-pages', $this->payload($folder, [
            'speaker_ids' => [$speaker->id],
            'links' => [MainPageLink::SLOT_ABOUT_FORUM => $aboutPage->id],
            'programme_background' => UploadedFile::fake()->image('programme-background.jpg'),
            'register_background' => UploadedFile::fake()->image('register-background.png'),
            'host_images' => [UploadedFile::fake()->image('host-original.png')],
            'organizer_images' => [UploadedFile::fake()->image('organizer-original.jpg')],
            'co_organizer_images' => [UploadedFile::fake()->image('co-organizer-original.png')],
        ]));

        $response->assertRedirect(route('backoffice.main-pages.index'));
        $mainPage = MainPage::query()->where('folder_name', $folder)->firstOrFail();

        $this->assertSame('ASEIC Global Forum 2026', $mainPage->event_name);
        $this->assertSame('programme-background.jpg', $mainPage->programme_background_name);
        $this->assertSame('register-background.png', $mainPage->register_background_name);
        $this->assertSame('host-original.png', $mainPage->host_image_files[0]['name']);
        $this->assertDatabaseHas('main_page_speaker', [
            'main_page_id' => $mainPage->id,
            'speaker_id' => $speaker->id,
            'sort_order' => 1,
        ]);
        $this->assertDatabaseHas('main_page_links', [
            'main_page_id' => $mainPage->id,
            'slot' => MainPageLink::SLOT_ABOUT_FORUM,
            'linkable_type' => $aboutPage->getMorphClass(),
            'linkable_id' => $aboutPage->id,
        ]);

        Storage::disk('public')->assertExists($mainPage->programme_background_path);
        Storage::disk('public')->assertExists($mainPage->register_background_path);
        Storage::disk('public')->assertExists($mainPage->host_image_files[0]['path']);

        $templatePath = resource_path('views/forums/'.$folder);
        $this->assertDirectoryExists($templatePath);
        $this->assertFileExists($templatePath.'/main.blade.php');
        $this->assertFileExists($templatePath.'/layouts/app.blade.php');
        $this->assertFileExists($templatePath.'/about/forum.blade.php');
        $this->assertFileExists($templatePath.'/programme/index.blade.php');
        $this->assertFileExists($templatePath.'/archive/legacy.blade.php');
        $this->assertFileExists($templatePath.'/registration/index.blade.php');
        $this->assertFileExists($templatePath.'/registration/register.blade.php');
        $this->assertFileExists($templatePath.'/registration/confirm.blade.php');
        $this->assertFileExists($templatePath.'/media/gallery.blade.php');
        $this->assertFileExists($templatePath.'/media/news.blade.php');
        $this->assertFileExists($templatePath.'/media/news-view.blade.php');
        $this->assertFileExists($templatePath.'/media/youtube.blade.php');
        $this->assertFileExists($templatePath.'/announcements/index.blade.php');
        $this->assertFileExists($templatePath.'/announcements/view.blade.php');

        $this->get(route('home', ['mainPage' => $mainPage->folder_name]))
            ->assertOk()
            ->assertViewIs('forums.'.$folder.'.main');

        $this->actingAs($this->admin)
            ->get('/backoffice/main-pages/'.$mainPage->id.'/edit')
            ->assertOk()
            ->assertSee('programme-background.jpg')
            ->assertSee('host-original.png')
            ->assertSee('ASEIC Global Forum 2026');
    }

    public function test_admin_can_update_main_page_but_cannot_change_folder_name(): void
    {
        $folder = $this->newFolderName();
        $mainPage = MainPage::factory()->create(['folder_name' => $folder]);
        File::copyDirectory(resource_path('views/forums/default'), resource_path('views/forums/'.$folder));

        $returnUrl = route('backoffice.main-pages.index').'?keyword=2026';
        $response = $this->actingAs($this->admin)->put('/backoffice/main-pages/'.$mainPage->id, [
            ...$this->payload(null, [
                'event_name' => 'Updated Forum',
                'return_url' => $returnUrl,
            ]),
        ]);

        $response->assertRedirect($returnUrl);
        $mainPage->refresh();
        $this->assertSame($folder, $mainPage->folder_name);
        $this->assertSame('Updated Forum', $mainPage->event_name);

        $invalidResponse = $this->actingAs($this->admin)->put('/backoffice/main-pages/'.$mainPage->id, [
            ...$this->payload(null),
            'folder_name' => 'changed-folder',
        ]);

        $invalidResponse->assertSessionHasErrors('folder_name');
        $this->assertSame($folder, $mainPage->fresh()->folder_name);
    }

    public function test_admin_can_open_visible_main_page_from_its_folder_name_link(): void
    {
        $folder = $this->newFolderName();
        $mainPage = MainPage::factory()->create([
            'folder_name' => $folder,
            'is_visible' => true,
        ]);
        $frontendUrl = route('home', ['mainPage' => $folder]);
        $returnUrl = route('backoffice.main-pages.index').'?keyword=visible';

        $this->actingAs($this->admin)
            ->get('/backoffice/main-pages')
            ->assertOk()
            ->assertSee($frontendUrl, false);

        $this->actingAs($this->admin)
            ->get('/backoffice/main-pages/'.$mainPage->id.'/edit?'.http_build_query(['return_url' => $returnUrl]))
            ->assertOk()
            ->assertDontSee('사용자 페이지 주소')
            ->assertSeeInOrder(['목록으로', '사용자 페이지'])
            ->assertSee('class="board-buttons"', false)
            ->assertSee('href="'.$returnUrl.'"', false)
            ->assertSee('href="'.$frontendUrl.'"', false)
            ->assertSee('target="_blank"', false);
    }

    public function test_admin_can_filter_and_bulk_delete_main_pages_without_removing_template_folder(): void
    {
        $visibleFolder = $this->newFolderName();
        $hiddenFolder = $this->newFolderName();
        $visible = MainPage::factory()->create([
            'folder_name' => $visibleFolder,
            'event_name' => 'ASEIC Visible Forum',
            'is_visible' => true,
            'created_at' => '2026-08-20 10:00:00',
        ]);
        $hidden = MainPage::factory()->create([
            'folder_name' => $hiddenFolder,
            'event_name' => 'ASEIC Hidden Forum',
            'is_visible' => false,
            'created_at' => '2026-08-20 10:00:00',
        ]);
        File::copyDirectory(resource_path('views/forums/default'), resource_path('views/forums/'.$visibleFolder));
        File::copyDirectory(resource_path('views/forums/default'), resource_path('views/forums/'.$hiddenFolder));

        $this->actingAs($this->admin)->get('/backoffice/main-pages?'.http_build_query([
            'is_visible' => '1',
            'created_from' => '2026-08-01',
            'created_to' => '2026-08-31',
            'keyword' => 'Visible',
        ]))->assertOk()
            ->assertSee('ASEIC Visible Forum')
            ->assertDontSee('ASEIC Hidden Forum');

        $this->actingAs($this->admin)->postJson('/backoffice/main-pages/delete-multiple', [
            'ids' => [$visible->id, $hidden->id],
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertSoftDeleted('main_pages', ['id' => $visible->id]);
        $this->assertSoftDeleted('main_pages', ['id' => $hidden->id]);
        $this->assertDirectoryExists(resource_path('views/forums/'.$visibleFolder));
        $this->assertDirectoryExists(resource_path('views/forums/'.$hiddenFolder));
    }

    public function test_create_rejects_invalid_or_existing_template_folder_name(): void
    {
        $invalidResponse = $this->actingAs($this->admin)->post('/backoffice/main-pages', $this->payload('한글 2026'));
        $invalidResponse->assertSessionHasErrors('folder_name');

        foreach (['default', 'publishing-original', 'forums', 'backoffice', 'auth', 'popup', 'css', 'images', 'js', 'storage'] as $reservedFolder) {
            $existingCount = MainPage::query()->where('folder_name', $reservedFolder)->count();
            $reservedResponse = $this->actingAs($this->admin)
                ->post('/backoffice/main-pages', $this->payload($reservedFolder));
            $reservedResponse->assertSessionHasErrors('folder_name');
            $this->assertSame(
                $existingCount,
                MainPage::query()->where('folder_name', $reservedFolder)->count(),
            );
        }

        $folder = $this->newFolderName();
        File::makeDirectory(resource_path('views/forums/'.$folder), 0755, true);

        $collisionResponse = $this->actingAs($this->admin)->post('/backoffice/main-pages', $this->payload($folder));
        $collisionResponse->assertSessionHasErrors('folder_name');
        $this->assertDatabaseMissing('main_pages', ['folder_name' => $folder]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(?string $folder, array $overrides = []): array
    {
        $payload = [
            'is_visible' => '1',
            'event_name' => 'ASEIC Global Forum 2026',
            'event_start_date' => '2026-09-01',
            'event_end_date' => '2026-09-02',
            'use_custom_event_date' => '0',
            'programme_items' => [
                ['time' => '09:00', 'subject' => 'Opening', 'content' => 'Opening Ceremony'],
                ['time' => '10:00', 'subject' => 'Session 1', 'content' => 'Programme content 1'],
                ['time' => '13:00', 'subject' => 'Session 2', 'content' => 'Programme content 2'],
                ['time' => '16:00', 'subject' => 'Closing', 'content' => 'Closing Ceremony'],
            ],
            'past_forum_video_url' => 'https://www.youtube.com/watch?v=example',
            'footer_text' => 'ASEIC Global Forum',
        ];

        if ($folder !== null) {
            $payload['folder_name'] = $folder;
        }

        return array_replace($payload, $overrides);
    }

    private function newFolderName(): string
    {
        $folder = 'test-'.Str::lower(Str::random(12));
        $this->createdTemplateFolders[] = $folder;

        return $folder;
    }
}
