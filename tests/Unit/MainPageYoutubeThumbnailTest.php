<?php

namespace Tests\Unit;

use App\Models\MainPage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MainPageYoutubeThumbnailTest extends TestCase
{
    #[DataProvider('youtubeUrls')]
    public function test_it_builds_the_thumbnail_from_supported_youtube_urls(string $url): void
    {
        $mainPage = new MainPage(['past_forum_video_url' => $url]);

        $this->assertSame(
            'https://i.ytimg.com/vi/CCpPwCRE-f4/maxresdefault.jpg',
            $mainPage->past_forum_video_thumbnail_url,
        );
    }

    public static function youtubeUrls(): array
    {
        return [
            'watch' => ['https://www.youtube.com/watch?v=CCpPwCRE-f4'],
            'shorts' => ['https://www.youtube.com/shorts/CCpPwCRE-f4'],
            'short link' => ['https://youtu.be/CCpPwCRE-f4'],
            'embed' => ['https://www.youtube-nocookie.com/embed/CCpPwCRE-f4'],
            'mobile live' => ['https://m.youtube.com/live/CCpPwCRE-f4'],
        ];
    }

    public function test_it_returns_null_for_a_non_youtube_or_invalid_video_url(): void
    {
        $this->assertNull((new MainPage(['past_forum_video_url' => 'https://example.com/video']))->past_forum_video_thumbnail_url);
        $this->assertNull((new MainPage(['past_forum_video_url' => 'https://www.youtube.com/watch?v=invalid']))->past_forum_video_thumbnail_url);
    }
}
