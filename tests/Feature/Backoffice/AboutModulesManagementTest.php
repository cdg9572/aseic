<?php

namespace Tests\Feature\Backoffice;

use App\Models\AboutPage;
use App\Models\AboutVenueDetail;
use App\Models\HomepagePartner;
use App\Models\MainPage;
use App\Models\MainPageLink;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AboutModulesManagementTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;

    private MainPage $mainPage;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        $this->mainPage = MainPage::factory()->create(['folder_name' => 'about-modules-'.strtolower(str()->random(8)), 'event_name' => 'ASEIC Test Forum']);
    }

    public function test_steering_committee_uses_registered_partner_modules_and_main_page_mapping(): void
    {
        $organized = HomepagePartner::factory()->create(['type' => HomepagePartner::TYPE_ORGANIZED]);
        $partnership = HomepagePartner::factory()->create(['type' => HomepagePartner::TYPE_PARTNERSHIP]);

        $this->actingAs($this->admin)->get('/backoffice/steering-committee/create')
            ->assertOk()->assertSee('Main Page')->assertSee('Organized By')->assertSee('Partnership with');

        $response = $this->actingAs($this->admin)->post('/backoffice/steering-committee', [
            'main_page_id' => $this->mainPage->id,
            'page_title' => '2026 Steering Committee',
            'subtitle' => '<p>Steering subtitle</p>',
            'organized_ids' => [$organized->id],
            'partnership_ids' => [$partnership->id],
        ]);

        $response->assertRedirect(route('backoffice.steering-committee.index'));
        $page = AboutPage::query()
            ->where('type', AboutPage::TYPE_STEERING_COMMITTEE)
            ->where('subtitle', '<p>Steering subtitle</p>')
            ->latest('id')
            ->firstOrFail();
        $this->assertSame('2026 Steering Committee', $page->page_title);
        $this->assertDatabaseHas('about_steering_partners', ['about_page_id' => $page->id, 'homepage_partner_id' => $organized->id, 'group_type' => HomepagePartner::TYPE_ORGANIZED]);
        $this->assertDatabaseHas('about_steering_partners', ['about_page_id' => $page->id, 'homepage_partner_id' => $partnership->id, 'group_type' => HomepagePartner::TYPE_PARTNERSHIP]);
        $this->assertDatabaseHas('main_page_links', ['main_page_id' => $this->mainPage->id, 'slot' => MainPageLink::SLOT_STEERING_COMMITTEE, 'linkable_id' => $page->id]);

        $this->actingAs($this->admin)->get('/backoffice/steering-committee')
            ->assertOk()
            ->assertSee($this->mainPage->folder_name.' Steering Committee')
            ->assertSee('2026 Steering Committee');
    }

    public function test_co_organizers_preserve_original_logo_names_and_repeat_items(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->get('/backoffice/co-organizers/create')
            ->assertOk()
            ->assertSee('class="bo-repeat-toolbar"', false)
            ->assertSee('class="bo-repeat-list"', false)
            ->assertSee('class="bo-repeat-item"', false)
            ->assertDontSee('class="bo-repeat-header"', false)
            ->assertDontSee('data-repeat-item-label', false)
            ->assertSee('data-co-organizer-add', false)
            ->assertSee('data-co-organizer-remove', false)
            ->assertSee('name="items[0][description]" rows="8" data-backoffice-ckeditor', false)
            ->assertSee('about-detail.js?v=', false)
            ->assertSee('추가하기');

        $response = $this->actingAs($this->admin)->post('/backoffice/co-organizers', [
            'main_page_id' => $this->mainPage->id,
            'page_title' => '2026 Co-Organizers',
            'subtitle' => '<p>Co-Organizers subtitle</p>',
            'items' => [
                ['name' => 'ASEIC', 'description' => 'First organizer', 'url' => 'https://example.com/aseic', 'logo' => UploadedFile::fake()->image('aseic-original.png')],
                ['name' => 'Partner Org', 'description' => 'Second organizer', 'url' => 'https://example.com/partner', 'logo' => UploadedFile::fake()->image('partner-original.jpg')],
            ],
        ]);

        $response->assertRedirect(route('backoffice.co-organizers.index'));
        $page = AboutPage::query()
            ->where('type', AboutPage::TYPE_CO_ORGANIZERS)
            ->where('page_title', '2026 Co-Organizers')
            ->latest('id')
            ->firstOrFail()
            ->load('coOrganizerItems');
        $this->assertSame('2026 Co-Organizers', $page->page_title);
        $this->assertSame(['aseic-original.png', 'partner-original.jpg'], $page->coOrganizerItems->pluck('logo_name')->all());
        $page->coOrganizerItems->each(fn ($item) => Storage::disk('public')->assertExists($item->logo_path));
        $this->assertDatabaseHas('main_page_links', ['main_page_id' => $this->mainPage->id, 'slot' => MainPageLink::SLOT_CO_ORGANIZERS, 'linkable_id' => $page->id]);

        $this->actingAs($this->admin)->get('/backoffice/co-organizers/'.$page->id.'/edit')
            ->assertOk()->assertSee('aseic-original.png')->assertSee('partner-original.jpg');

        $firstItem = $page->coOrganizerItems[0];
        $secondItem = $page->coOrganizerItems[1];
        $this->actingAs($this->admin)->put('/backoffice/co-organizers/'.$page->id, [
            'main_page_id' => $this->mainPage->id,
            'page_title' => '2026 Co-Organizers Updated',
            'items' => [
                0 => ['id' => $firstItem->id, 'name' => 'ASEIC'],
                2 => ['id' => $secondItem->id, 'name' => 'Partner Org Updated', 'logo' => UploadedFile::fake()->image('partner-replaced.png')],
            ],
        ])->assertRedirect(route('backoffice.co-organizers.index'));

        $this->assertSame('partner-replaced.png', $secondItem->fresh()->logo_name);
        $this->assertSame('2026 Co-Organizers Updated', $page->fresh()->page_title);
        Storage::disk('public')->assertExists($secondItem->fresh()->logo_path);
    }

    public function test_venue_can_be_created_updated_and_linked_to_main_page(): void
    {
        $this->actingAs($this->admin)->get('/backoffice/venue/create')
            ->assertOk()
            ->assertSee('name="postal_code"', false)
            ->assertSee('name="address"', false)
            ->assertSee('name="address_detail"', false)
            ->assertDontSee('name="forum_location"', false);

        $response = $this->actingAs($this->admin)->post('/backoffice/venue', [
            'main_page_id' => $this->mainPage->id,
            'page_title' => '2026 Venue',
            'subtitle' => '<p>Venue subtitle</p>',
            'postal_code' => '04524',
            'address' => 'Seoul, Korea',
            'address_detail' => 'Grand Hall, 3F',
            'venue_name' => 'Grand Hall',
            'venue_description' => '<p>Venue details</p>',
            'event_date' => 'September 1-2, 2026',
            'format' => AboutVenueDetail::FORMAT_ONLINE_OFFLINE,
            'bus_content' => '<p>Bus information</p>',
            'subway_content' => '<p>Subway information</p>',
            'taxi_content' => '<p>Taxi information</p>',
        ]);

        $response->assertRedirect(route('backoffice.venue.index'));
        $page = AboutPage::query()
            ->where('type', AboutPage::TYPE_VENUE)
            ->where('page_title', '2026 Venue')
            ->latest('id')
            ->firstOrFail()
            ->load('venueDetail');
        $this->assertSame('2026 Venue', $page->page_title);
        $this->assertSame('04524', $page->venueDetail?->postal_code);
        $this->assertSame('Seoul, Korea', $page->venueDetail?->address);
        $this->assertSame('Grand Hall, 3F', $page->venueDetail?->address_detail);
        $this->assertSame('Grand Hall', $page->venueDetail?->venue_name);
        $this->assertSame(AboutVenueDetail::FORMAT_ONLINE_OFFLINE, $page->venueDetail?->format);
        $this->assertDatabaseHas('main_page_links', ['main_page_id' => $this->mainPage->id, 'slot' => MainPageLink::SLOT_VENUE, 'linkable_id' => $page->id]);

        $this->actingAs($this->admin)->put('/backoffice/venue/'.$page->id, [
            'main_page_id' => null,
            'page_title' => '2026 Venue Updated',
            'postal_code' => '04637',
            'address' => 'Updated Seoul Address',
            'address_detail' => 'Room 101',
            'venue_name' => 'Updated Hall',
            'format' => AboutVenueDetail::FORMAT_OFFLINE,
        ])->assertRedirect(route('backoffice.venue.index'));
        $this->assertSame('2026 Venue Updated', $page->fresh()->page_title);
        $this->assertSame('04637', $page->fresh()->venueDetail?->postal_code);
        $this->assertSame('Updated Seoul Address', $page->fresh()->venueDetail?->address);
        $this->assertSame('Room 101', $page->fresh()->venueDetail?->address_detail);
        $this->assertSame('Updated Hall', $page->fresh()->venueDetail?->venue_name);
        $this->assertDatabaseMissing('main_page_links', ['linkable_id' => $page->id]);
    }

    public function test_main_page_form_can_change_about_mapping_created_from_subpage(): void
    {
        $steering = AboutPage::factory()->create([
            'type' => AboutPage::TYPE_STEERING_COMMITTEE,
            'page_title' => 'Main Page Steering Choice',
        ]);

        $this->actingAs($this->admin)->get('/backoffice/main-pages/'.$this->mainPage->id.'/edit')
            ->assertOk()
            ->assertSee('Main Page Steering Choice');

        $response = $this->actingAs($this->admin)->put('/backoffice/main-pages/'.$this->mainPage->id, [
            'is_visible' => '1',
            'event_name' => $this->mainPage->event_name,
            'event_start_date' => '2026-09-01',
            'event_end_date' => '2026-09-02',
            'use_custom_event_date' => '0',
            'programme_items' => array_fill(0, 4, ['time' => '', 'subject' => '', 'content' => '']),
            'links' => [MainPageLink::SLOT_STEERING_COMMITTEE => $steering->id],
        ]);

        $response->assertRedirect(route('backoffice.main-pages.index'));
        $this->assertDatabaseHas('main_page_links', ['main_page_id' => $this->mainPage->id, 'slot' => MainPageLink::SLOT_STEERING_COMMITTEE, 'linkable_id' => $steering->id]);
    }
}
