<?php

namespace Tests\Feature\Frontend;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublishingPreviewController;
use Illuminate\Http\Request;
use Tests\TestCase;
use ZipArchive;

class PublishingOriginalIsolationTest extends TestCase
{
    public function test_original_and_default_addresses_are_routed_to_different_controllers(): void
    {
        $routes = app('router')->getRoutes();

        $originalRoute = $routes->match(Request::create('/publishing-original', 'GET'));
        $defaultRoute = $routes->match(Request::create('/default', 'GET'));

        $this->assertSame(PublishingPreviewController::class, $originalRoute->getActionName());
        $this->assertSame(HomeController::class.'@index', $defaultRoute->getActionName());
    }

    public function test_original_preview_pages_render_only_the_isolated_snapshot_views(): void
    {
        $pages = [
            '' => 'publishing-original.home.index',
            'about/forum' => 'publishing-original.about.forum',
            'about/committee' => 'publishing-original.about.committee',
            'about/organizers' => 'publishing-original.about.organizers',
            'about/venue' => 'publishing-original.about.venue',
            'programme/theme' => 'publishing-original.programme.theme',
            'programme' => 'publishing-original.programme.list',
            'programme/list' => 'publishing-original.programme.list',
            'programme/speakers' => 'publishing-original.programme.speakers',
            'programme/book' => 'publishing-original.programme.book',
            'archive/theme' => 'publishing-original.archive.theme',
            'archive/programme' => 'publishing-original.archive.programme',
            'archive/speakers' => 'publishing-original.archive.speakers',
            'archive/past' => 'publishing-original.archive.past',
            'media/gallery' => 'publishing-original.media.gallery',
            'media/news' => 'publishing-original.media.news',
            'media/news/view' => 'publishing-original.media.news_view',
            'media/youtube' => 'publishing-original.media.youtube',
            'registration' => 'publishing-original.registration.index',
            'registration/register' => 'publishing-original.registration.register_now',
            'registration/confirm' => 'publishing-original.registration.confirm',
            'announcements' => 'publishing-original.announcements.index',
            'announcements/view' => 'publishing-original.announcements.view',
        ];

        foreach ($pages as $path => $view) {
            $this->get('/publishing-original'.($path === '' ? '' : '/'.$path))
                ->assertOk()
                ->assertViewIs($view)
                ->assertSee('/publishing-original-assets/', false);
        }
    }

    public function test_original_home_keeps_the_published_video_sample(): void
    {
        $this->get('/publishing-original')
            ->assertOk()
            ->assertSee('/publishing-original-assets/images/img_sample_video.avif', false)
            ->assertSee('https://www.youtube.com/shorts/CCpPwCRE-f4', false)
            ->assertSee('/publishing-original/programme', false);
    }

    public function test_original_binary_assets_are_read_from_the_source_archive(): void
    {
        $zip = new ZipArchive;
        $this->assertTrue($zip->open(base_path('docs/aseic.zip')) === true);
        $expected = $zip->getFromName('public/images/img_sample_video.avif');
        $zip->close();

        $response = $this->get('/publishing-original-assets/images/img_sample_video.avif')
            ->assertOk()
            ->assertHeader('Content-Type', 'image/avif');

        $this->assertSame($expected, $response->getContent());

        $this->get('/publishing-original-assets/css/main.css')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/css; charset=UTF-8')
            ->assertSee('/publishing-original-assets/images/', false)
            ->assertDontSee("url('/images/", false);

        $this->get('/publishing-original-assets/index.php')->assertNotFound();
    }
}
