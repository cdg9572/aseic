<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
class SubController extends Controller
{
    public function aboutForum() {
        return view('about.forum', [
            'gNum' => '01', 'sNum' => '01', 'gName' => 'ABOUT', 'sName' => 'About the Forum'
        ]);
    }
    public function aboutCommittee() {
        return view('about.committee', [
            'gNum' => '01', 'sNum' => '02', 'gName' => 'ABOUT', 'sName' => 'Steering Committee'
        ]);
    }
    public function aboutOrganizers() {
        return view('about.organizers', [
            'gNum' => '01', 'sNum' => '03', 'gName' => 'ABOUT', 'sName' => 'Co-organizers'
        ]);
    }
    public function aboutVenue() {
        return view('about.venue', [
            'gNum' => '01', 'sNum' => '04', 'gName' => 'ABOUT', 'sName' => 'Venue'
        ]);
    }

    public function programmeTheme() {
        return view('programme.theme', [
            'gNum' => '02', 'sNum' => '01', 'gName' => 'PROGRAMME', 'sName' => 'Theme'
        ]);
    }
    public function programmeList() {
        return view('programme.list', [
            'gNum' => '02', 'sNum' => '02', 'gName' => 'PROGRAMME', 'sName' => 'Programme'
        ]);
    }
    public function programmeSpeakers() {
        return view('programme.speakers', [
            'gNum' => '02', 'sNum' => '03', 'gName' => 'PROGRAMME', 'sName' => 'Speakers'
        ]);
    }
    public function programmeBook() {
        return view('programme.book', [
            'gNum' => '02', 'sNum' => '04', 'gName' => 'PROGRAMME', 'sName' => 'Programme Book'
        ]);
    }

    public function archiveTheme() {
		return view('archive.theme', [
			'gNum' => '03', 'sNum' => '01', 'dNum' => '01',
			'gName' => 'ARCHIVE', 'sName' => '2025 Forum', 'dName' => 'Theme'
		]);
	}

	public function archiveProgramme() {
		return view('archive.programme', [
			'gNum' => '03', 'sNum' => '01', 'dNum' => '02',
			'gName' => 'ARCHIVE', 'sName' => '2025 Forum', 'dName' => 'Programme'
		]);
	}

	public function archiveSpeakers() {
		return view('archive.speakers', [
			'gNum' => '03', 'sNum' => '01', 'dNum' => '03',
			'gName' => 'ARCHIVE', 'sName' => '2025 Forum', 'dName' => 'Speakers'
		]);
	}

	public function archivePast() {
		return view('archive.past', [
			'gNum' => '03', 'sNum' => '02', 'dNum' => '',
			'gName' => 'ARCHIVE', 'sName' => 'Past Forums (2015~2024)', 'dName' => 'Past Forums (2015~2024)'
		]);
	}

    public function mediaGallery() {
		return view('media.gallery', [
			'gNum' => '04', 'sNum' => '01', 'gName' => 'MEDIA', 'sName' => 'Photo Gallery'
		]);
	}

	public function mediaNews() {
		return view('media.news', [
			'gNum' => '04', 'sNum' => '02', 'gName' => 'MEDIA', 'sName' => 'News Clippings'
		]);
	}

	public function mediaNewsView() {
		return view('media.news_view', [
			'gNum' => '04', 'sNum' => '02', 'gName' => 'MEDIA', 'sName' => 'News Clippings'
		]);
	}

	public function mediaYoutube() {
		return view('media.youtube', [
			'gNum' => '04', 'sNum' => '03', 'gName' => 'MEDIA', 'sName' => 'Youtube Channel'
		]);
	}

    public function registrationIndex() {
		return view('registration.index', [
			'gNum' => '05', 'sNum' => '01', 'gName' => 'REGISTRATION', 'sName' => 'Registration'
		]);
	}

	public function registrationConfirm() {
		return view('registration.confirm', [
			'gNum' => '05', 'sNum' => '02', 'gName' => 'REGISTRATION', 'sName' => 'Confirm Registration', 'page' => 'view'
		]);
	}

	public function registrationRegister() {
		return view('registration.register_now', [
			'gNum' => '05', 'sNum' => '01', 'gName' => 'REGISTRATION', 'sName' => 'Registration'
		]);
	}

    public function announcementsIndex() {
        return view('announcements.index', [
            'gNum' => '06', 'sNum' => '01', 'gName' => 'ANNOUNCEMENTS', 'sName' => 'Announcements'
        ]);
    }

    public function announcementsView() {
        return view('announcements.view', [
            'gNum' => '06', 'sNum' => '01', 'gName' => 'ANNOUNCEMENTS', 'sName' => 'Announcements', 'page' => 'view'
        ]);
    }
}