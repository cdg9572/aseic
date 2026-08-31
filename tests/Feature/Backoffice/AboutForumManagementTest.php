<?php

namespace Tests\Feature\Backoffice;

use App\Models\AboutPage;
use App\Models\MainPage;
use App\Models\MainPageLink;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AboutForumManagementTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;

    private MainPage $mainPage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);
        $this->mainPage = MainPage::factory()->create([
            'folder_name' => 'about-test-'.strtolower(str()->random(8)),
            'event_name' => 'ASEIC 2026',
        ]);
    }

    public function test_admin_can_filter_about_forum_pages_by_visibility_date_and_folder_name(): void
    {
        $visible = AboutPage::factory()->create([
            'page_title' => 'Visible About Title',
            'subtitle' => 'Visible Forum',
            'created_at' => '2026-08-20 10:00:00',
        ]);
        MainPageLink::query()->create(['main_page_id' => $this->mainPage->id, 'slot' => MainPageLink::SLOT_ABOUT_FORUM, 'linkable_type' => $visible->getMorphClass(), 'linkable_id' => $visible->id]);
        AboutPage::factory()->create([
            'subtitle' => 'Hidden Forum',
            'created_at' => '2026-08-20 10:00:00',
        ]);
        AboutPage::factory()->create([
            'type' => 'venue',
            'folder_name' => 'ASEIC 2026 Venue',
            'created_at' => '2026-08-20 10:00:00',
        ]);

        $response = $this->actingAs($this->admin)->get('/backoffice/about-the-forum?'.http_build_query([
            'is_linked' => '1',
            'created_from' => '2026-08-01',
            'created_to' => '2026-08-31',
            'keyword' => 'Visible About Title',
        ]));

        $response->assertOk();
        $response->assertSeeInOrder(['제목(폴더명)', '제목', 'Main Page 연결']);
        $response->assertSee('class="w15">제목(폴더명)</th>', false);
        $response->assertSee('class="w30">제목</th>', false);
        $response->assertSee('class="w10">Main Page 연결</th>', false);
        $response->assertSee('about-test-');
        $response->assertSee('Visible About Title');
        $response->assertDontSee('Hidden Forum');
    }

    public function test_page_title_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post('/backoffice/about-the-forum', $this->payload([
            'page_title' => '',
        ]));

        $response->assertSessionHasErrors('page_title');
    }

    public function test_admin_can_create_update_and_delete_an_about_forum_page(): void
    {
        $this->actingAs($this->admin)->get('/backoffice/about-the-forum/create')
            ->assertOk()
            ->assertSee('Main Page')
            ->assertSee('제목')
            ->assertSee($this->mainPage->folder_name);

        $createResponse = $this->actingAs($this->admin)->post('/backoffice/about-the-forum', $this->payload());

        $createResponse->assertRedirect(route('backoffice.about-the-forum.index'));
        $page = AboutPage::query()->where('type', AboutPage::TYPE_FORUM)->latest('id')->firstOrFail();
        $this->assertSame(AboutPage::TYPE_FORUM, $page->type);
        $this->assertNull($page->folder_name);
        $this->assertSame('2026 About the Forum', $page->page_title);
        $this->assertSame('<p>ASEIC Global Forum</p>', $page->subtitle);
        $this->assertTrue($page->is_main_page_visible);
        $this->assertDatabaseHas('main_page_links', ['main_page_id' => $this->mainPage->id, 'slot' => MainPageLink::SLOT_ABOUT_FORUM, 'linkable_id' => $page->id]);
        $this->assertSame('<p>Overview text</p>', $page->forumDetail?->overview);
        $this->assertSame('Background 1', $page->forumDetail?->backgrounds[0]['title']);
        $this->assertSame('Objective content 3', $page->forumDetail?->objectives[2]['content']);

        $this->actingAs($this->admin)
            ->get('/backoffice/about-the-forum/'.$page->id.'/edit')
            ->assertOk()
            ->assertSee('2026 About the Forum')
            ->assertSee('ASEIC Global Forum')
            ->assertSee('Background content 1');

        $returnUrl = route('backoffice.about-the-forum.index').'?keyword=ASEIC';
        $updatePayload = $this->payload([
            'subtitle' => '<p>Updated subtitle</p>',
            'overview' => '<p>Updated overview</p>',
            'main_page_id' => null,
            'return_url' => $returnUrl,
        ]);
        $updateResponse = $this->actingAs($this->admin)
            ->put('/backoffice/about-the-forum/'.$page->id, $updatePayload);

        $updateResponse->assertRedirect($returnUrl);
        $page->refresh();
        $this->assertSame('<p>Updated subtitle</p>', $page->subtitle);
        $this->assertFalse($page->is_main_page_visible);
        $this->assertDatabaseMissing('main_page_links', ['linkable_id' => $page->id]);
        $this->assertSame('<p>Updated overview</p>', $page->forumDetail?->overview);

        $deleteResponse = $this->actingAs($this->admin)
            ->deleteJson('/backoffice/about-the-forum/'.$page->id);

        $deleteResponse->assertOk()->assertJson(['success' => true]);
        $this->assertSoftDeleted('about_pages', ['id' => $page->id]);
    }

    public function test_bulk_delete_only_removes_about_forum_pages(): void
    {
        $forumPages = AboutPage::factory()->count(2)->create();
        $venuePage = AboutPage::factory()->create(['type' => 'venue']);

        $response = $this->actingAs($this->admin)->postJson('/backoffice/about-the-forum/delete-multiple', [
            'ids' => [...$forumPages->pluck('id')->all(), $venuePage->id],
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $forumPages->each(fn (AboutPage $page) => $this->assertSoftDeleted('about_pages', ['id' => $page->id]));
        $this->assertDatabaseHas('about_pages', ['id' => $venuePage->id, 'deleted_at' => null]);
    }

    public function test_background_and_objective_counts_are_validated(): void
    {
        $payload = $this->payload([
            'backgrounds' => array_slice($this->contentItems('Background', 4), 0, 3),
            'objectives' => array_slice($this->contentItems('Objective', 3), 0, 2),
        ]);

        $response = $this->actingAs($this->admin)->post('/backoffice/about-the-forum', $payload);

        $response->assertSessionHasErrors(['backgrounds', 'objectives']);
        $this->assertDatabaseMissing('about_pages', ['subtitle' => '<p>ASEIC Global Forum</p>']);
    }

    public function test_statistics_only_accept_numbers_and_plus_signs(): void
    {
        $response = $this->actingAs($this->admin)->post('/backoffice/about-the-forum', $this->payload([
            'forums_since_2015' => 'Since 2015',
            'participants' => '1,000+',
        ]));

        $response->assertSessionHasErrors(['forums_since_2015', 'participants']);
        $this->assertDatabaseMissing('about_pages', ['subtitle' => '<p>ASEIC Global Forum</p>']);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_replace([
            'page_title' => '2026 About the Forum',
            'subtitle' => '<p>ASEIC Global Forum</p>',
            'overview' => '<p>Overview text</p>',
            'forums_since_2015' => '11',
            'participants' => '1000+',
            'countries' => '30',
            'organizations' => '120',
            'backgrounds' => $this->contentItems('Background', 4),
            'objectives' => $this->contentItems('Objective', 3),
            'main_page_id' => $this->mainPage->id,
        ], $overrides);
    }

    /**
     * @return array<int, array{title: string, content: string}>
     */
    private function contentItems(string $prefix, int $count): array
    {
        return collect(range(1, $count))
            ->map(fn (int $number): array => [
                'title' => $prefix.' '.$number,
                'content' => $prefix.' content '.$number,
            ])
            ->all();
    }
}
