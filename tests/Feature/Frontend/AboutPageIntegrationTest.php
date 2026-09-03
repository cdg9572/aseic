<?php

namespace Tests\Feature\Frontend;

use App\Models\AboutPage;
use App\Models\AboutVenueDetail;
use App\Models\HomepagePartner;
use App\Models\MainPage;
use App\Models\MainPageLink;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class AboutPageIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_unconnected_about_pages_do_not_render_publishing_samples(): void
    {
        $folder = 'empty-about-'.Str::lower(Str::random(10));
        MainPage::factory()->create([
            'folder_name' => $folder,
            'is_visible' => true,
        ]);

        $this->get('/'.$folder.'/about/forum')
            ->assertOk()
            ->assertDontSee('Global Challenges')
            ->assertDontSee('Learn about the Forum');
        $this->get('/'.$folder.'/about/committee')
            ->assertOk()
            ->assertDontSee('Giulia Ajmone');
        $this->get('/'.$folder.'/about/organizers')
            ->assertOk()
            ->assertDontSee('Asia-Europe Foundation');
        $this->get('/'.$folder.'/about/venue')
            ->assertOk()
            ->assertDontSee('International Convention Center Jeju');
    }

    public function test_connected_about_pages_render_their_backoffice_content(): void
    {
        $folder = 'about-content-'.Str::lower(Str::random(10));
        $mainPage = MainPage::factory()->create([
            'folder_name' => $folder,
            'event_name' => 'Connected About Forum',
            'is_visible' => true,
        ]);

        $forum = AboutPage::factory()->create([
            'type' => AboutPage::TYPE_FORUM,
            'page_title' => 'Connected About the Forum',
            'subtitle' => '<p>Connected forum subtitle</p>',
        ]);
        $forum->forumDetail()->create([
            'overview' => '<p>Connected overview content</p>',
            'forums_since_2015' => '12',
            'participants' => '1800+',
            'countries' => '45',
            'organizations' => '350+',
            'backgrounds' => [
                ['title' => 'Connected Challenge', 'content' => '<p>Connected background content</p>'],
                ['title' => '', 'content' => ''],
                ['title' => '', 'content' => ''],
                ['title' => '', 'content' => ''],
            ],
            'objectives' => [
                ['title' => 'Connected Objective', 'content' => '<p>Connected objective content</p>'],
                ['title' => '', 'content' => ''],
                ['title' => '', 'content' => ''],
            ],
        ]);

        $steering = $this->steeringPage();

        $coOrganizers = AboutPage::factory()->create([
            'type' => AboutPage::TYPE_CO_ORGANIZERS,
            'page_title' => 'Connected Co-Organizers',
            'subtitle' => '<p>Connected co-organizer subtitle</p>',
        ]);
        $coOrganizers->coOrganizerItems()->create([
            'logo_path' => 'about/co-organizers/connected-logo.png',
            'logo_name' => 'connected-logo.png',
            'name' => 'Connected Organization',
            'description' => '<p>Connected organization description</p>',
            'url' => 'https://example.com/connected-organization',
            'sort_order' => 1,
        ]);

        $venue = AboutPage::factory()->create([
            'type' => AboutPage::TYPE_VENUE,
            'page_title' => 'Connected Venue',
            'subtitle' => '<p>Connected venue subtitle</p>',
        ]);
        $venue->venueDetail()->create([
            'postal_code' => '04524',
            'address' => 'Seoul, Korea',
            'address_detail' => 'Room 101',
            'venue_name' => 'Connected Convention Hall',
            'venue_description' => '<p>Connected venue description</p>',
            'event_date' => 'September 1-2, 2027',
            'format' => AboutVenueDetail::FORMAT_ONLINE_OFFLINE,
            'bus_content' => '<p>Connected bus information</p>',
            'subway_content' => '<p>Connected subway information</p>',
            'taxi_content' => '<p>Connected taxi information</p>',
        ]);

        foreach ([
            MainPageLink::SLOT_ABOUT_FORUM => $forum,
            MainPageLink::SLOT_STEERING_COMMITTEE => $steering,
            MainPageLink::SLOT_CO_ORGANIZERS => $coOrganizers,
            MainPageLink::SLOT_VENUE => $venue,
        ] as $slot => $aboutPage) {
            $mainPage->links()->create([
                'slot' => $slot,
                'linkable_type' => $aboutPage->getMorphClass(),
                'linkable_id' => $aboutPage->id,
            ]);
        }

        $this->assertForumPage($folder);
        $this->assertSteeringPage($folder);
        $this->assertCoOrganizerPage($folder);
        $this->assertVenuePage($folder);
    }

    private function steeringPage(): AboutPage
    {
        $organized = HomepagePartner::factory()->create([
            'type' => HomepagePartner::TYPE_ORGANIZED,
            'first_name' => 'Organized',
            'last_name' => 'Member',
            'profile_image' => 'about/organized-member.jpg',
            'is_image_visible' => true,
            'is_active' => true,
            'content' => '<p>Organized member biography</p>',
            'linkedin_url' => 'https://example.com/organized-member',
        ]);
        $partnership = HomepagePartner::factory()->create([
            'type' => HomepagePartner::TYPE_PARTNERSHIP,
            'first_name' => 'Partnership',
            'last_name' => 'Member',
            'is_active' => true,
        ]);
        $inactive = HomepagePartner::factory()->create([
            'type' => HomepagePartner::TYPE_ORGANIZED,
            'first_name' => 'Inactive',
            'last_name' => 'Member',
            'is_active' => false,
        ]);
        $steering = AboutPage::factory()->create([
            'type' => AboutPage::TYPE_STEERING_COMMITTEE,
            'page_title' => 'Connected Steering Committee',
            'subtitle' => '<p>Connected steering subtitle</p>',
        ]);
        $steering->steeringOrganizedPartners()->attach([
            $organized->id => ['group_type' => HomepagePartner::TYPE_ORGANIZED, 'sort_order' => 1],
            $inactive->id => ['group_type' => HomepagePartner::TYPE_ORGANIZED, 'sort_order' => 2],
        ]);
        $steering->steeringPartnershipPartners()->attach($partnership->id, [
            'group_type' => HomepagePartner::TYPE_PARTNERSHIP,
            'sort_order' => 1,
        ]);

        return $steering;
    }

    private function assertForumPage(string $folder): void
    {
        $this->get('/'.$folder.'/about/forum')
            ->assertOk()
            ->assertSeeInOrder([
                '<h1 class="tit_pagename">About the Forum</h1>',
                '<p>Connected forum subtitle</p>',
            ], false)
            ->assertSee('Connected About the Forum')
            ->assertSee('Connected forum subtitle')
            ->assertSee('Connected overview content')
            ->assertSee('1800+')
            ->assertSee('Connected Challenge')
            ->assertSee('Connected Objective')
            ->assertDontSee('Global Challenges');
    }

    private function assertSteeringPage(string $folder): void
    {
        $this->get('/'.$folder.'/about/committee')
            ->assertOk()
            ->assertSee('<h1 class="tit_pagename">Steering Committee</h1>', false)
            ->assertSee('Connected Steering Committee')
            ->assertSee('Connected steering subtitle')
            ->assertSee('Organized Member')
            ->assertSee('Partnership Member')
            ->assertSee('/storage/about/organized-member.jpg', false)
            ->assertSee('https://example.com/organized-member', false)
            ->assertDontSee('Inactive Member');
    }

    private function assertCoOrganizerPage(string $folder): void
    {
        $this->get('/'.$folder.'/about/organizers')
            ->assertOk()
            ->assertSee('<h1 class="tit_pagename">Co-organizers</h1>', false)
            ->assertSee('Connected Co-Organizers')
            ->assertSee('Connected co-organizer subtitle')
            ->assertSee('Connected Organization')
            ->assertSee('Connected organization description')
            ->assertSee('/storage/about/co-organizers/connected-logo.png', false)
            ->assertSee('https://example.com/connected-organization', false)
            ->assertDontSee('Asia-Europe Foundation');
    }

    private function assertVenuePage(string $folder): void
    {
        $this->get('/'.$folder.'/about/venue')
            ->assertOk()
            ->assertSee('<h1 class="tit_pagename">Venue</h1>', false)
            ->assertSee('Connected Venue')
            ->assertSee('Connected venue subtitle')
            ->assertSee('Connected About Forum')
            ->assertSee('Connected Convention Hall')
            ->assertSee('Seoul, Korea Room 101')
            ->assertSee('Connected venue description')
            ->assertSee('September 1-2, 2027')
            ->assertSee('Online &amp; Offline', false)
            ->assertSee('Connected bus information')
            ->assertSee('Seoul%2C%20Korea%20Room%20101', false);
    }
}
