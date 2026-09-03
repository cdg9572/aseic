<?php

namespace Tests\Feature\Frontend;

use App\Models\MainPage;
use App\Models\MainPageLink;
use App\Models\RegistrationApplicant;
use App\Models\RegistrationPage;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegistrationAnnouncementsIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_linked_registration_page_accepts_and_confirms_a_public_application(): void
    {
        [$mainPage, $registrationPage] = $this->linkedRegistrationPage();
        $folder = $mainPage->folder_name;

        $this->get('/'.$folder.'/registration')
            ->assertOk()
            ->assertSeeInOrder([
                '<h1 class="tit_pagename">Registration</h1>',
                '<p>Connected registration subtitle</p>',
            ], false)
            ->assertSee('Connected Registration')
            ->assertSee('August 1 through September 30, 2026')
            ->assertSee('Open the registration form')
            ->assertSee('Register Now')
            ->assertDontSee('June 1 (Mon)');
        $this->get('/'.$folder.'/registration/register')
            ->assertOk()
            ->assertSee('name="first_name"', false)
            ->assertSee('name="privacy_agree"', false)
            ->assertSee('required', false)
            ->assertSee('/js/registration.js', false)
            ->assertSee('Connected Registration Forum')
            ->assertDontSee('Global Korea Forum 2026')
            ->assertDontSee('session attendance')
            ->assertDontSee('captcha_sample.png', false)
            ->assertDontSee('Luncheon Notice');

        $response = $this->post('/'.$folder.'/registration/register', [
            'first_name' => 'Connected',
            'last_name' => 'Applicant',
            'affiliation' => 'ASEIC Test Organization',
            'position' => 'Researcher',
            'mobile' => '+82 10-1234-5678',
            'email' => 'Applicant@Example.com',
            'attendance_mode' => 'online',
            'privacy_agree' => '1',
        ]);

        $response->assertRedirect('/'.$folder.'/registration/confirm');
        $this->assertDatabaseHas('registration_applicants', [
            'registration_page_id' => $registrationPage->id,
            'name' => 'Connected Applicant',
            'email' => 'Applicant@Example.com',
            'phone' => '+82 10-1234-5678',
            'participation_type' => 'online',
            'status' => RegistrationApplicant::STATUS_PENDING,
            'agreed_privacy' => true,
        ]);
        $this->followRedirects($response)
            ->assertOk()
            ->assertSee('Registration Completed');

        $confirmation = $this->post('/'.$folder.'/registration/confirm', [
            'mobile_last_four' => '5678',
            'email' => 'applicant@example.com',
        ]);
        $confirmation->assertRedirect('/'.$folder.'/registration/confirm');
        $this->followRedirects($confirmation)
            ->assertOk()
            ->assertSee('Registration Confirmed');

        $this->post('/'.$folder.'/registration/confirm', [
            'mobile_last_four' => '0000',
            'email' => 'applicant@example.com',
        ])->assertSessionHas('registration_confirmation_error');
    }

    public function test_unlinked_or_closed_registration_does_not_render_publishing_values_or_accept_applications(): void
    {
        $emptyFolder = 'empty-registration-'.Str::lower(Str::random(10));
        MainPage::factory()->create(['folder_name' => $emptyFolder, 'is_visible' => true]);

        $this->get('/'.$emptyFolder.'/registration')
            ->assertOk()
            ->assertDontSee('Pre-registration Period')
            ->assertDontSee('June 1 (Mon)');
        $this->get('/'.$emptyFolder.'/registration/register')
            ->assertOk()
            ->assertDontSee('name="first_name"', false);

        [$closedMainPage, $closedRegistrationPage] = $this->linkedRegistrationPage([
            'participation_mode' => RegistrationPage::MODE_NOT_PARTICIPATING,
            'period_text' => null,
            'closed_notice' => '<p>Registration is currently unavailable.</p>',
        ]);

        $this->get('/'.$closedMainPage->folder_name.'/registration')
            ->assertOk()
            ->assertSee('Registration is currently unavailable.')
            ->assertDontSee('Register Now');
        $this->post('/'.$closedMainPage->folder_name.'/registration/register', [
            'first_name' => 'Closed',
            'last_name' => 'Applicant',
            'affiliation' => 'Organization',
            'position' => 'Position',
            'mobile' => '01012345678',
            'email' => 'closed@example.com',
            'attendance_mode' => 'offline',
            'privacy_agree' => '1',
        ])->assertNotFound();
        $this->assertDatabaseMissing('registration_applicants', [
            'registration_page_id' => $closedRegistrationPage->id,
            'email' => 'closed@example.com',
        ]);
    }

    public function test_global_announcements_support_search_detail_files_and_visibility(): void
    {
        $folder = 'announcement-content-'.Str::lower(Str::random(10));
        MainPage::factory()->create(['folder_name' => $folder, 'is_visible' => true]);

        $visibleId = DB::table('board_notices')->insertGetId([
            'title' => 'Connected Public Announcement',
            'content' => '<p>Unique searchable announcement body</p>',
            'author_name' => 'Administrator',
            'is_notice' => true,
            'is_secret' => false,
            'is_active' => true,
            'category' => null,
            'attachments' => json_encode([['path' => 'uploads/notices/connected.pdf', 'name' => 'Connected File.pdf', 'size' => 1024]]),
            'custom_fields' => json_encode(['subtitle' => '<p>Connected announcement subtitle</p>']),
            'view_count' => 7,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('board_notices')->insert([
            'title' => 'Hidden Announcement',
            'content' => '<p>Hidden body</p>',
            'author_name' => 'Administrator',
            'is_notice' => false,
            'is_secret' => false,
            'is_active' => false,
            'view_count' => 0,
            'sort_order' => 0,
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ]);
        DB::table('board_notices')->insert([
            'title' => 'Secret Announcement',
            'content' => '<p>Secret body</p>',
            'author_name' => 'Administrator',
            'is_notice' => false,
            'is_secret' => true,
            'is_active' => true,
            'view_count' => 0,
            'sort_order' => 0,
            'created_at' => now()->subMinutes(2),
            'updated_at' => now()->subMinutes(2),
        ]);

        $this->get('/'.$folder.'/announcements?search_condition=content&search_keyword=searchable')
            ->assertOk()
            ->assertSee('Connected Public Announcement')
            ->assertSee('<nav class="paging"', false)
            ->assertSee('aria-current="page"', false)
            ->assertSee('>1</a>', false)
            ->assertDontSee('Hidden Announcement')
            ->assertDontSee('Secret Announcement')
            ->assertDontSee('It’s the space where the title goes in.');
        $this->get('/'.$folder.'/announcements/view/'.$visibleId)
            ->assertOk()
            ->assertSeeInOrder([
                '<div class="tit_pagename">Announcements</div>',
                '<p>Connected announcement subtitle</p>',
            ], false)
            ->assertSee('Connected Public Announcement')
            ->assertSee('Connected announcement subtitle')
            ->assertSee('Unique searchable announcement body')
            ->assertSee('Connected File.pdf')
            ->assertSee('/storage/uploads/notices/connected.pdf', false)
            ->assertSee('8');
        $this->assertDatabaseHas('board_notices', ['id' => $visibleId, 'view_count' => 8]);

        $this->get('/'.$folder.'/announcements/view')
            ->assertOk()
            ->assertDontSee('concluded successfully');
        $this->get('/'.$folder.'/announcements/view/999999999')->assertNotFound();
    }

    /** @param array<string, mixed> $overrides */
    private function linkedRegistrationPage(array $overrides = []): array
    {
        $folder = 'registration-content-'.Str::lower(Str::random(10));
        $mainPage = MainPage::factory()->create([
            'folder_name' => $folder,
            'event_name' => 'Connected Registration Forum',
            'is_visible' => true,
        ]);
        $registrationPage = RegistrationPage::query()->create([
            'page_title' => 'Connected Registration',
            'subtitle' => '<p>Connected registration subtitle</p>',
            'participation_mode' => RegistrationPage::MODE_PARTICIPATING,
            'period_text' => 'August 1 through September 30, 2026',
            'guide_step_1' => 'Open the registration form',
            'guide_step_2' => 'Enter your information',
            'guide_step_3' => 'Complete registration',
            'registration_start_date' => '2026-08-01',
            'registration_end_date' => '2026-09-30',
            'use_custom_end_text' => false,
            ...$overrides,
        ]);
        $mainPage->links()->create([
            'slot' => MainPageLink::SLOT_REGISTRATION,
            'linkable_type' => $registrationPage->getMorphClass(),
            'linkable_id' => $registrationPage->id,
        ]);

        return [$mainPage, $registrationPage];
    }
}
