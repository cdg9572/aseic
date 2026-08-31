<?php

namespace Tests\Feature\Backoffice;

use App\Models\HomepagePartner;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomepagePartnerManagementTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_create_and_update_an_organized_profile(): void
    {
        Storage::fake('public');

        $createResponse = $this->actingAs($this->admin)->post('/backoffice/organized', [
            'first_name' => 'Alice',
            'last_name' => 'Kim',
            'position' => 'Director',
            'affiliation' => 'ASEIC',
            'linkedin_url' => 'https://www.linkedin.com/in/alice-kim',
            'profile_image' => UploadedFile::fake()->image('organized-profile.png'),
            'is_active' => '1',
            'is_image_visible' => '0',
            'content' => '<p>Organized detail</p>',
        ]);

        $createResponse->assertRedirect(route('backoffice.organized.index'));
        $partner = HomepagePartner::query()->where('first_name', 'Alice')->firstOrFail();
        $this->assertSame(HomepagePartner::TYPE_ORGANIZED, $partner->type);
        $this->assertSame('https://www.linkedin.com/in/alice-kim', $partner->linkedin_url);
        $this->assertSame('organized-profile.png', $partner->profile_image_name);
        Storage::disk('public')->assertExists($partner->profile_image);

        $this->actingAs($this->admin)
            ->get('/backoffice/organized/'.$partner->id.'/edit')
            ->assertOk()
            ->assertSee('organized-profile.png');

        $returnUrl = route('backoffice.organized.index').'?keyword=Alice';
        $updateResponse = $this->actingAs($this->admin)->put('/backoffice/organized/'.$partner->id, [
            'first_name' => 'Alice',
            'last_name' => 'Kim',
            'position' => 'Chair',
            'affiliation' => 'ASEIC',
            'linkedin_url' => 'https://www.linkedin.com/in/alice-kim-updated',
            'is_active' => '0',
            'is_image_visible' => '1',
            'return_url' => $returnUrl,
        ]);

        $updateResponse->assertRedirect($returnUrl);
        $this->assertDatabaseHas('homepage_partners', [
            'id' => $partner->id,
            'type' => HomepagePartner::TYPE_ORGANIZED,
            'position' => 'Chair',
            'linkedin_url' => 'https://www.linkedin.com/in/alice-kim-updated',
            'is_active' => false,
            'is_image_visible' => true,
        ]);
    }

    public function test_organized_and_partnership_lists_are_isolated(): void
    {
        $organized = HomepagePartner::factory()->create([
            'type' => HomepagePartner::TYPE_ORGANIZED,
            'first_name' => 'OrganizedOnly',
            'last_name' => 'Person',
        ]);
        $partnership = HomepagePartner::factory()->create([
            'type' => HomepagePartner::TYPE_PARTNERSHIP,
            'first_name' => 'PartnershipOnly',
            'last_name' => 'Person',
        ]);

        $this->actingAs($this->admin)
            ->get('/backoffice/organized')
            ->assertOk()
            ->assertSee('OrganizedOnly Person')
            ->assertDontSee('PartnershipOnly Person');

        $this->actingAs($this->admin)
            ->get('/backoffice/partnerships')
            ->assertOk()
            ->assertSee('PartnershipOnly Person')
            ->assertDontSee('OrganizedOnly Person');

        $deleteResponse = $this->actingAs($this->admin)->postJson('/backoffice/organized/delete-multiple', [
            'ids' => [$organized->id, $partnership->id],
        ]);

        $deleteResponse->assertOk()->assertJson(['success' => true]);
        $this->assertSoftDeleted('homepage_partners', ['id' => $organized->id]);
        $this->assertDatabaseHas('homepage_partners', [
            'id' => $partnership->id,
            'deleted_at' => null,
        ]);
    }

    public function test_admin_can_create_and_delete_a_partnership_profile(): void
    {
        $createResponse = $this->actingAs($this->admin)->post('/backoffice/partnerships', [
            'first_name' => 'Brian',
            'last_name' => 'Lee',
            'position' => 'Partner',
            'affiliation' => 'Global Forum',
            'is_active' => '1',
            'is_image_visible' => '1',
        ]);

        $createResponse->assertRedirect(route('backoffice.partnerships.index'));
        $partner = HomepagePartner::query()->where('first_name', 'Brian')->firstOrFail();
        $this->assertSame(HomepagePartner::TYPE_PARTNERSHIP, $partner->type);

        $deleteResponse = $this->actingAs($this->admin)
            ->deleteJson('/backoffice/partnerships/'.$partner->id);

        $deleteResponse->assertOk()->assertJson(['success' => true]);
        $this->assertSoftDeleted('homepage_partners', ['id' => $partner->id]);
    }
}
