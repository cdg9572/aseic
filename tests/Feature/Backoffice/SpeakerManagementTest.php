<?php

namespace Tests\Feature\Backoffice;

use App\Models\Speaker;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SpeakerManagementTest extends TestCase
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

    public function test_admin_can_filter_speakers_by_visibility_date_and_name(): void
    {
        Speaker::factory()->create([
            'first_name' => 'Alice',
            'last_name' => 'Torres',
            'is_active' => true,
            'created_at' => '2026-08-20 10:00:00',
        ]);
        Speaker::factory()->create([
            'first_name' => 'Brian',
            'last_name' => 'Lee',
            'is_active' => false,
            'created_at' => '2026-08-20 10:00:00',
        ]);

        $response = $this->actingAs($this->admin)->get('/backoffice/speakers?'.http_build_query([
            'is_active' => '1',
            'created_from' => '2026-08-01',
            'created_to' => '2026-08-31',
            'keyword' => 'Alice',
        ]));

        $response->assertOk();
        $response->assertSee('Alice Torres');
        $response->assertDontSee('Brian Lee');
    }

    public function test_admin_can_create_update_and_delete_a_speaker(): void
    {
        Storage::fake('public');

        $createResponse = $this->actingAs($this->admin)->post('/backoffice/speakers', [
            'first_name' => 'Alice',
            'last_name' => 'Torres',
            'position' => 'Director',
            'affiliation' => 'ASEIC',
            'presentation_subject' => 'Green transformation',
            'profile_image' => UploadedFile::fake()->image('profile.jpg'),
            'role' => Speaker::ROLE_MODERATOR,
            'is_active' => '1',
            'is_image_visible' => '0',
            'content' => '<p>Speaker detail</p>',
            'attachments' => [
                UploadedFile::fake()->create('profile.pdf', 100, 'application/pdf'),
                UploadedFile::fake()->create('agenda.docx', 120, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
            ],
        ]);

        $createResponse->assertRedirect(route('backoffice.speakers.index'));
        $speaker = Speaker::query()->where('first_name', 'Alice')->firstOrFail();
        $this->assertSame('profile.jpg', $speaker->profile_image_name);
        $this->assertSame('profile.pdf', $speaker->attachment_name);
        $this->assertSame(['profile.pdf', 'agenda.docx'], array_column($speaker->attachment_files, 'name'));
        Storage::disk('public')->assertExists($speaker->profile_image);
        foreach ($speaker->attachment_files as $attachment) {
            Storage::disk('public')->assertExists($attachment['path']);
        }

        $this->actingAs($this->admin)
            ->get('/backoffice/speakers/'.$speaker->id.'/edit')
            ->assertOk()
            ->assertSee('profile.jpg')
            ->assertSee('profile.pdf')
            ->assertSee('agenda.docx');

        $returnUrl = route('backoffice.speakers.index').'?keyword=Alice';
        $removedAttachmentPath = $speaker->attachment_files[0]['path'];
        $updateResponse = $this->actingAs($this->admin)->put('/backoffice/speakers/'.$speaker->id, [
            'first_name' => 'Alice',
            'last_name' => 'Torres',
            'position' => 'Co-Chairman',
            'role' => Speaker::ROLE_SPEAKER,
            'is_active' => '0',
            'is_image_visible' => '1',
            'return_url' => $returnUrl,
            'remove_attachments' => [0],
            'attachments' => [
                UploadedFile::fake()->create('slides.pptx', 150, 'application/vnd.openxmlformats-officedocument.presentationml.presentation'),
            ],
        ]);

        $updateResponse->assertRedirect($returnUrl);
        $this->assertDatabaseHas('speakers', [
            'id' => $speaker->id,
            'position' => 'Co-Chairman',
            'is_active' => false,
            'is_image_visible' => true,
        ]);
        $speaker->refresh();
        $this->assertSame(['agenda.docx', 'slides.pptx'], array_column($speaker->attachment_files, 'name'));
        Storage::disk('public')->assertMissing($removedAttachmentPath);
        foreach ($speaker->attachment_files as $attachment) {
            Storage::disk('public')->assertExists($attachment['path']);
        }

        $deleteResponse = $this->actingAs($this->admin)
            ->deleteJson('/backoffice/speakers/'.$speaker->id);

        $deleteResponse->assertOk()->assertJson(['success' => true]);
        $this->assertSoftDeleted('speakers', ['id' => $speaker->id]);
    }

    public function test_admin_can_bulk_delete_selected_speakers(): void
    {
        $speakers = Speaker::factory()->count(2)->create();

        $response = $this->actingAs($this->admin)->postJson('/backoffice/speakers/delete-multiple', [
            'ids' => $speakers->pluck('id')->all(),
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $speakers->each(fn (Speaker $speaker) => $this->assertSoftDeleted('speakers', ['id' => $speaker->id]));
    }

    public function test_speaker_attachments_are_limited_to_five_files(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin)->post('/backoffice/speakers', [
            'first_name' => 'Limit',
            'last_name' => 'Test',
            'role' => Speaker::ROLE_SPEAKER,
            'is_active' => '1',
            'is_image_visible' => '0',
            'attachments' => collect(range(1, 6))
                ->map(fn (int $number) => UploadedFile::fake()->create("file{$number}.pdf", 10, 'application/pdf'))
                ->all(),
        ]);

        $response->assertSessionHasErrors('attachments');
        $this->assertDatabaseMissing('speakers', [
            'first_name' => 'Limit',
            'last_name' => 'Test',
        ]);
    }
}
