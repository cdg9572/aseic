<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\MainPage;
use App\Models\Speaker;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class PublishingPreviewController extends Controller
{
    /**
     * @var array<string, array{view: string, template: string, data: array<string, string>}>
     */
    private const PAGES = [
        '' => ['view' => 'home.index', 'template' => 'main', 'data' => ['gNum' => 'main', 'gName' => '', 'sName' => '']],
        'about/forum' => ['view' => 'about.forum', 'template' => 'about.forum', 'data' => ['gNum' => '01', 'sNum' => '01', 'gName' => 'ABOUT', 'sName' => 'About the Forum']],
        'about/committee' => ['view' => 'about.committee', 'template' => 'about.steering-committee', 'data' => ['gNum' => '01', 'sNum' => '02', 'gName' => 'ABOUT', 'sName' => 'Steering Committee']],
        'about/organizers' => ['view' => 'about.organizers', 'template' => 'about.co-organizers', 'data' => ['gNum' => '01', 'sNum' => '03', 'gName' => 'ABOUT', 'sName' => 'Co-organizers']],
        'about/venue' => ['view' => 'about.venue', 'template' => 'about.venue', 'data' => ['gNum' => '01', 'sNum' => '04', 'gName' => 'ABOUT', 'sName' => 'Venue']],
        'programme/theme' => ['view' => 'programme.theme', 'template' => 'programme.theme', 'data' => ['gNum' => '02', 'sNum' => '01', 'gName' => 'PROGRAMME', 'sName' => 'Theme']],
        'programme' => ['view' => 'programme.list', 'template' => 'programme.index', 'data' => ['gNum' => '02', 'sNum' => '02', 'gName' => 'PROGRAMME', 'sName' => 'Programme']],
        'programme/list' => ['view' => 'programme.list', 'template' => 'programme.index', 'data' => ['gNum' => '02', 'sNum' => '02', 'gName' => 'PROGRAMME', 'sName' => 'Programme']],
        'programme/speakers' => ['view' => 'programme.speakers', 'template' => 'programme.speakers', 'data' => ['gNum' => '02', 'sNum' => '03', 'gName' => 'PROGRAMME', 'sName' => 'Speakers']],
        'programme/book' => ['view' => 'programme.book', 'template' => 'programme.book', 'data' => ['gNum' => '02', 'sNum' => '04', 'gName' => 'PROGRAMME', 'sName' => 'Programme Book']],
        'archive/theme' => ['view' => 'archive.theme', 'template' => 'archive.theme', 'data' => ['gNum' => '03', 'sNum' => '01', 'dNum' => '01', 'gName' => 'ARCHIVE', 'sName' => '2025 Forum', 'dName' => 'Theme']],
        'archive/programme' => ['view' => 'archive.programme', 'template' => 'archive.programme', 'data' => ['gNum' => '03', 'sNum' => '01', 'dNum' => '02', 'gName' => 'ARCHIVE', 'sName' => '2025 Forum', 'dName' => 'Programme']],
        'archive/speakers' => ['view' => 'archive.speakers', 'template' => 'archive.speakers', 'data' => ['gNum' => '03', 'sNum' => '01', 'dNum' => '03', 'gName' => 'ARCHIVE', 'sName' => '2025 Forum', 'dName' => 'Speakers']],
        'archive/past' => ['view' => 'archive.past', 'template' => 'archive.legacy', 'data' => ['gNum' => '03', 'sNum' => '02', 'dNum' => '', 'gName' => 'ARCHIVE', 'sName' => 'Past Forums (2015~2024)', 'dName' => 'Past Forums (2015~2024)']],
        'media/gallery' => ['view' => 'media.gallery', 'template' => 'media.gallery', 'data' => ['gNum' => '04', 'sNum' => '01', 'gName' => 'MEDIA', 'sName' => 'Photo Gallery']],
        'media/news' => ['view' => 'media.news', 'template' => 'media.news', 'data' => ['gNum' => '04', 'sNum' => '02', 'gName' => 'MEDIA', 'sName' => 'News Clippings']],
        'media/news/view' => ['view' => 'media.news_view', 'template' => 'media.news-view', 'data' => ['gNum' => '04', 'sNum' => '02', 'gName' => 'MEDIA', 'sName' => 'News Clippings']],
        'media/youtube' => ['view' => 'media.youtube', 'template' => 'media.youtube', 'data' => ['gNum' => '04', 'sNum' => '03', 'gName' => 'MEDIA', 'sName' => 'Youtube Channel']],
        'registration' => ['view' => 'registration.index', 'template' => 'registration.index', 'data' => ['gNum' => '05', 'sNum' => '01', 'gName' => 'REGISTRATION', 'sName' => 'Registration']],
        'registration/register' => ['view' => 'registration.register_now', 'template' => 'registration.register', 'data' => ['gNum' => '05', 'sNum' => '01', 'gName' => 'REGISTRATION', 'sName' => 'Registration']],
        'registration/confirm' => ['view' => 'registration.confirm', 'template' => 'registration.confirm', 'data' => ['gNum' => '05', 'sNum' => '02', 'gName' => 'REGISTRATION', 'sName' => 'Confirm Registration', 'page' => 'view']],
        'announcements' => ['view' => 'announcements.index', 'template' => 'announcements.index', 'data' => ['gNum' => '06', 'sNum' => '01', 'gName' => 'ANNOUNCEMENTS', 'sName' => 'Announcements']],
        'announcements/view' => ['view' => 'announcements.view', 'template' => 'announcements.view', 'data' => ['gNum' => '06', 'sNum' => '01', 'gName' => 'ANNOUNCEMENTS', 'sName' => 'Announcements', 'page' => 'view']],
    ];

    public function __invoke(string $previewMode, ?string $previewPath = null): View
    {
        $previewPath = trim((string) $previewPath, '/');
        $page = self::PAGES[$previewPath] ?? null;
        abort_if($page === null, 404);

        URL::defaults(['mainPage' => $previewMode]);

        $mainPage = new MainPage([
            'folder_name' => $previewMode,
            'event_name' => '2026 Global Eco-Innovation Forum',
            'event_start_date' => '2026-09-03',
            'event_end_date' => '2026-09-07',
            'programme_items' => [
                ['time' => '09:00 AM ~ 09:30 AM', 'subject' => 'Registration', 'content' => ''],
                ['time' => '09:30 AM ~ 09:50 AM', 'subject' => 'Opening Ceremony', 'content' => 'Opening Remarks, Welcome Remarks, Congratulatory Remarks'],
                ['time' => '09:50 AM ~ 10:30 AM', 'subject' => 'Keynote Speeches', 'content' => 'Global Policy Directions for Green & Inclusive SME Growth'],
                ['time' => '10:30 AM ~ 10:50 AM', 'subject' => 'Networking Break', 'content' => ''],
            ],
            'past_forum_video_url' => 'https://www.youtube.com/shorts/CCpPwCRE-f4',
            'footer_text' => '2026 Global Eco-Innovation Forum',
            'is_visible' => true,
        ]);

        return view($page['view'], [
            ...$page['data'],
            'mainPage' => $mainPage,
            'galleryPosts' => collect(),
            'noticePosts' => $this->previewNotices(),
            'popups' => collect(),
            'banners' => $previewPath === '' ? $this->previewBanners() : collect(),
            'speakers' => $previewPath === '' ? $this->previewSpeakers() : collect(),
        ]);
    }

    /** @return \Illuminate\Support\Collection<int, Banner> */
    private function previewBanners(): \Illuminate\Support\Collection
    {
        return collect([
            new Banner([
                'title' => '2026 Global Eco-Innovation Forum',
                'main_text' => "2026 Global\nEco-Innovation Forum",
                'sub_text' => 'Climate-Smart Innovations for Sustainable Local Economies',
                'desktop_image' => '/images/mvisual01.avif',
                'mobile_image' => '/images/mvisual01.avif',
                'url_target' => '_self',
            ]),
        ]);
    }

    /** @return \Illuminate\Support\Collection<int, Speaker> */
    private function previewSpeakers(): \Illuminate\Support\Collection
    {
        return collect([
            ['first_name' => 'Sia', 'last_name' => 'LEE', 'position' => 'GREENGRIM inc', 'affiliation' => 'Republic of Korea', 'role' => Speaker::ROLE_STARTUP],
            ['first_name' => 'Nur', 'last_name' => 'AISYAH', 'position' => 'Universiti Malaya', 'affiliation' => 'Malaysia', 'role' => Speaker::ROLE_SPEAKER],
            ['first_name' => 'Daniel', 'last_name' => 'Wong', 'position' => 'ASEIC', 'affiliation' => 'Singapore', 'role' => Speaker::ROLE_PANEL],
            ['first_name' => 'Elena', 'last_name' => 'Müller', 'position' => 'GreenTech Europe', 'affiliation' => 'Germany', 'role' => Speaker::ROLE_MODERATOR],
        ])->map(static fn (array $speaker): Speaker => new Speaker([
            ...$speaker,
            'profile_image' => '/images/img_speaker01.avif',
            'is_active' => true,
            'is_image_visible' => true,
            'presentation_subject' => 'SESSION',
        ]));
    }

    /** @return \Illuminate\Support\Collection<int, object> */
    private function previewNotices(): \Illuminate\Support\Collection
    {
        return collect([
            (object) ['title' => 'Event Information', 'created_at' => '2026-07-13'],
            (object) ['title' => 'Frequently Asked Questions (FAQ)', 'created_at' => '2026-07-13'],
            (object) ['title' => 'Pre-registration Information', 'created_at' => '2026-07-13'],
        ]);
    }
}
