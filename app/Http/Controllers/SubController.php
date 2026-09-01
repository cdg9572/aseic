<?php

namespace App\Http\Controllers;

use App\Models\MainPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\View;

class SubController extends Controller
{
    public function aboutForum(MainPage $mainPage)
    {
        return $this->render($mainPage, 'about.forum', [
            'gNum' => '01', 'sNum' => '01', 'gName' => 'ABOUT', 'sName' => 'About the Forum',
        ]);
    }

    public function aboutCommittee(MainPage $mainPage)
    {
        return $this->render($mainPage, 'about.committee', [
            'gNum' => '01', 'sNum' => '02', 'gName' => 'ABOUT', 'sName' => 'Steering Committee',
        ]);
    }

    public function aboutOrganizers(MainPage $mainPage)
    {
        return $this->render($mainPage, 'about.organizers', [
            'gNum' => '01', 'sNum' => '03', 'gName' => 'ABOUT', 'sName' => 'Co-organizers',
        ]);
    }

    public function aboutVenue(MainPage $mainPage)
    {
        return $this->render($mainPage, 'about.venue', [
            'gNum' => '01', 'sNum' => '04', 'gName' => 'ABOUT', 'sName' => 'Venue',
        ]);
    }

    public function programmeTheme(MainPage $mainPage)
    {
        return $this->render($mainPage, 'programme.theme', [
            'gNum' => '02', 'sNum' => '01', 'gName' => 'PROGRAMME', 'sName' => 'Theme',
        ]);
    }

    public function programmeList(MainPage $mainPage)
    {
        return $this->render($mainPage, 'programme.list', [
            'gNum' => '02', 'sNum' => '02', 'gName' => 'PROGRAMME', 'sName' => 'Programme',
        ]);
    }

    public function programmeListRedirect(MainPage $mainPage): RedirectResponse
    {
        abort_unless($mainPage->is_visible, 404);

        return redirect()->route('programme.list', ['mainPage' => $mainPage->folder_name]);
    }

    public function programmeSpeakers(MainPage $mainPage)
    {
        return $this->render($mainPage, 'programme.speakers', [
            'gNum' => '02', 'sNum' => '03', 'gName' => 'PROGRAMME', 'sName' => 'Speakers',
        ]);
    }

    public function programmeBook(MainPage $mainPage)
    {
        return $this->render($mainPage, 'programme.book', [
            'gNum' => '02', 'sNum' => '04', 'gName' => 'PROGRAMME', 'sName' => 'Programme Book',
        ]);
    }

    public function archiveTheme(MainPage $mainPage)
    {
        return $this->render($mainPage, 'archive.theme', [
            'gNum' => '03', 'sNum' => '01', 'dNum' => '01',
            'gName' => 'ARCHIVE', 'sName' => '2025 Forum', 'dName' => 'Theme',
        ]);
    }

    public function archiveProgramme(MainPage $mainPage)
    {
        return $this->render($mainPage, 'archive.programme', [
            'gNum' => '03', 'sNum' => '01', 'dNum' => '02',
            'gName' => 'ARCHIVE', 'sName' => '2025 Forum', 'dName' => 'Programme',
        ]);
    }

    public function archiveSpeakers(MainPage $mainPage)
    {
        return $this->render($mainPage, 'archive.speakers', [
            'gNum' => '03', 'sNum' => '01', 'dNum' => '03',
            'gName' => 'ARCHIVE', 'sName' => '2025 Forum', 'dName' => 'Speakers',
        ]);
    }

    public function archivePast(MainPage $mainPage)
    {
        return $this->render($mainPage, 'archive.past', [
            'gNum' => '03', 'sNum' => '02', 'dNum' => '',
            'gName' => 'ARCHIVE', 'sName' => 'Past Forums (2015~2024)', 'dName' => 'Past Forums (2015~2024)',
        ]);
    }

    public function mediaGallery(MainPage $mainPage)
    {
        return $this->render($mainPage, 'media.gallery', [
            'gNum' => '04', 'sNum' => '01', 'gName' => 'MEDIA', 'sName' => 'Photo Gallery',
        ]);
    }

    public function mediaNews(MainPage $mainPage)
    {
        return $this->render($mainPage, 'media.news', [
            'gNum' => '04', 'sNum' => '02', 'gName' => 'MEDIA', 'sName' => 'News Clippings',
        ]);
    }

    public function mediaNewsView(MainPage $mainPage)
    {
        return $this->render($mainPage, 'media.news_view', [
            'gNum' => '04', 'sNum' => '02', 'gName' => 'MEDIA', 'sName' => 'News Clippings',
        ]);
    }

    public function mediaYoutube(MainPage $mainPage)
    {
        return $this->render($mainPage, 'media.youtube', [
            'gNum' => '04', 'sNum' => '03', 'gName' => 'MEDIA', 'sName' => 'Youtube Channel',
        ]);
    }

    public function registrationIndex(MainPage $mainPage)
    {
        return $this->render($mainPage, 'registration.index', [
            'gNum' => '05', 'sNum' => '01', 'gName' => 'REGISTRATION', 'sName' => 'Registration',
        ]);
    }

    public function registrationConfirm(MainPage $mainPage)
    {
        return $this->render($mainPage, 'registration.confirm', [
            'gNum' => '05', 'sNum' => '02', 'gName' => 'REGISTRATION', 'sName' => 'Confirm Registration', 'page' => 'view',
        ]);
    }

    public function registrationRegister(MainPage $mainPage)
    {
        return $this->render($mainPage, 'registration.register_now', [
            'gNum' => '05', 'sNum' => '01', 'gName' => 'REGISTRATION', 'sName' => 'Registration',
        ]);
    }

    public function announcementsIndex(MainPage $mainPage)
    {
        return $this->render($mainPage, 'announcements.index', [
            'gNum' => '06', 'sNum' => '01', 'gName' => 'ANNOUNCEMENTS', 'sName' => 'Announcements',
        ]);
    }

    public function announcementsView(MainPage $mainPage)
    {
        return $this->render($mainPage, 'announcements.view', [
            'gNum' => '06', 'sNum' => '01', 'gName' => 'ANNOUNCEMENTS', 'sName' => 'Announcements', 'page' => 'view',
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function render(MainPage $mainPage, string $view, array $data): View
    {
        abort_unless($mainPage->is_visible, 404);

        URL::defaults(['mainPage' => $mainPage->folder_name]);

        return view($this->forumView($mainPage, $this->forumTemplate($view)), [
            ...$data,
            'mainPage' => $mainPage,
        ]);
    }

    private function forumView(MainPage $mainPage, string $template): string
    {
        $eventView = 'forums.'.$mainPage->folder_name.'.'.$template;

        return ViewFacade::exists($eventView) ? $eventView : 'forums.default.'.$template;
    }

    private function forumTemplate(string $view): string
    {
        return match ($view) {
            'about.committee' => 'about.steering-committee',
            'about.organizers' => 'about.co-organizers',
            'programme.list' => 'programme.index',
            'archive.past' => 'archive.legacy',
            'media.news_view' => 'media.news-view',
            'registration.register_now' => 'registration.register',
            default => $view,
        };
    }
}
