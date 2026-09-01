<?php

namespace Tests\Feature;

use App\Models\MainPage;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test example.
     */
    public function test_the_application_root_redirects_to_a_visible_forum(): void
    {
        MainPage::query()->update(['is_visible' => false]);
        $mainPage = MainPage::factory()->create([
            'folder_name' => 'example-'.Str::lower(Str::random(12)),
            'is_visible' => true,
        ]);

        $this->get('/')->assertRedirect(route('home', ['mainPage' => $mainPage->folder_name]));
    }
}
