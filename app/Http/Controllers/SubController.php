<?php

namespace App\Http\Controllers;

use App\Http\Requests\FrontendRegistrationConfirmationRequest;
use App\Http\Requests\FrontendRegistrationRequest;
use App\Models\AboutPage;
use App\Models\Category;
use App\Models\MainPage;
use App\Models\MainPageLink;
use App\Models\MediaContent;
use App\Models\ProgrammePage;
use App\Models\RegistrationPage;
use App\Services\Frontend\RegistrationService;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\View;

class SubController extends Controller
{
    public function __construct(private readonly RegistrationService $registrationService) {}

    public function aboutForum(MainPage $mainPage)
    {
        $aboutPage = $this->linkedAboutPage($mainPage, MainPageLink::SLOT_ABOUT_FORUM, ['forumDetail']);

        return $this->render($mainPage, 'about.forum', [
            'gNum' => '01', 'sNum' => '01', 'gName' => 'ABOUT',
            'sName' => 'About the Forum',
            'pageTitle' => $aboutPage?->page_title,
            'aboutPage' => $aboutPage,
            'pageSubtitle' => $aboutPage?->subtitle,
        ]);
    }

    public function aboutCommittee(MainPage $mainPage)
    {
        $aboutPage = $this->linkedAboutPage($mainPage, MainPageLink::SLOT_STEERING_COMMITTEE, [
            'steeringOrganizedPartners',
            'steeringPartnershipPartners',
        ]);

        return $this->render($mainPage, 'about.committee', [
            'gNum' => '01', 'sNum' => '02', 'gName' => 'ABOUT',
            'sName' => 'Steering Committee',
            'pageTitle' => $aboutPage?->page_title,
            'aboutPage' => $aboutPage,
            'pageSubtitle' => $aboutPage?->subtitle,
        ]);
    }

    public function aboutOrganizers(MainPage $mainPage)
    {
        $aboutPage = $this->linkedAboutPage($mainPage, MainPageLink::SLOT_CO_ORGANIZERS, ['coOrganizerItems']);

        return $this->render($mainPage, 'about.organizers', [
            'gNum' => '01', 'sNum' => '03', 'gName' => 'ABOUT',
            'sName' => 'Co-organizers',
            'pageTitle' => $aboutPage?->page_title,
            'aboutPage' => $aboutPage,
            'pageSubtitle' => $aboutPage?->subtitle,
        ]);
    }

    public function aboutVenue(MainPage $mainPage)
    {
        $aboutPage = $this->linkedAboutPage($mainPage, MainPageLink::SLOT_VENUE, ['venueDetail']);

        return $this->render($mainPage, 'about.venue', [
            'gNum' => '01', 'sNum' => '04', 'gName' => 'ABOUT',
            'sName' => 'Venue',
            'pageTitle' => $aboutPage?->page_title,
            'aboutPage' => $aboutPage,
            'pageSubtitle' => $aboutPage?->subtitle,
        ]);
    }

    public function programmeTheme(MainPage $mainPage)
    {
        $programmePage = $this->linkedProgrammePage($mainPage, MainPageLink::SLOT_PROGRAMME_THEME);

        return $this->render($mainPage, 'programme.theme', [
            'gNum' => '02', 'sNum' => '01', 'gName' => 'PROGRAMME',
            'sName' => 'Theme',
            'pageTitle' => $programmePage?->page_title,
            'pageSubtitle' => $programmePage?->subtitle,
            'programmePage' => $programmePage,
        ]);
    }

    public function programmeList(MainPage $mainPage)
    {
        $programmePage = $this->linkedProgrammePage($mainPage, MainPageLink::SLOT_PROGRAMME);

        return $this->render($mainPage, 'programme.list', [
            'gNum' => '02', 'sNum' => '02', 'gName' => 'PROGRAMME',
            'sName' => 'Programme',
            'pageTitle' => $programmePage?->page_title,
            'pageSubtitle' => $programmePage?->subtitle,
            'programmePage' => $programmePage,
        ]);
    }

    public function programmeListRedirect(MainPage $mainPage): RedirectResponse
    {
        abort_unless($mainPage->is_visible, 404);

        return redirect()->route('programme.list', ['mainPage' => $mainPage->folder_name]);
    }

    public function programmeSpeakers(MainPage $mainPage)
    {
        $programmePage = $this->linkedProgrammePage($mainPage, MainPageLink::SLOT_PROGRAMME_SPEAKERS, [
            'sessions.speakers',
        ]);

        return $this->render($mainPage, 'programme.speakers', [
            'gNum' => '02', 'sNum' => '03', 'gName' => 'PROGRAMME',
            'sName' => 'Speakers',
            'pageTitle' => $programmePage?->page_title,
            'pageSubtitle' => $programmePage?->subtitle,
            'programmePage' => $programmePage,
        ]);
    }

    public function programmeBook(Request $request, MainPage $mainPage)
    {
        $programmePage = $this->linkedProgrammePage($mainPage, MainPageLink::SLOT_PROGRAMME_BOOK);
        $requestedCondition = $request->query('search_condition');
        $requestedKeyword = $request->query('search_keyword');
        $searchCondition = is_string($requestedCondition)
            && in_array($requestedCondition, ['title', 'file_name'], true)
            ? $requestedCondition
            : 'title';
        $searchKeyword = is_string($requestedKeyword) ? trim($requestedKeyword) : '';
        $books = $programmePage?->books()
            ->when($searchKeyword !== '', function ($query) use ($searchCondition, $searchKeyword): void {
                $query->where($searchCondition, 'like', '%'.$searchKeyword.'%');
            })
            ->paginate(10)
            ->withQueryString();

        return $this->render($mainPage, 'programme.book', [
            'gNum' => '02', 'sNum' => '04', 'gName' => 'PROGRAMME',
            'sName' => 'Programme Book',
            'pageTitle' => $programmePage?->page_title,
            'pageSubtitle' => $programmePage?->subtitle,
            'programmePage' => $programmePage,
            'books' => $books,
            'searchCondition' => $searchCondition,
            'searchKeyword' => $searchKeyword,
        ]);
    }

    public function archiveTheme(Request $request, MainPage $mainPage)
    {
        $categories = $this->mediaCategories(Category::GROUP_CODE_ARCHIVE_THEME);
        $selectedCategoryId = $this->selectedCategoryId($request, $categories);
        $programmePage = $this->archivePageForCategory(
            $mainPage,
            ProgrammePage::TYPE_ARCHIVE_THEME,
            MainPageLink::SLOT_ARCHIVE_THEME,
            $selectedCategoryId,
        );

        return $this->render($mainPage, 'archive.theme', [
            'gNum' => '03', 'sNum' => '01', 'dNum' => '01',
            'gName' => 'ARCHIVE', 'sName' => '2025 Forum', 'dName' => 'Theme',
            'pageTitle' => $programmePage?->page_title,
            'pageSubtitle' => $programmePage?->subtitle,
            'programmePage' => $programmePage,
            'categories' => $categories,
            'selectedCategoryId' => $selectedCategoryId,
            'archiveSourceMainPage' => $programmePage?->mainPageLink?->mainPage,
        ]);
    }

    public function archiveProgramme(Request $request, MainPage $mainPage)
    {
        $categories = $this->mediaCategories(Category::GROUP_CODE_ARCHIVE_PROGRAMME);
        $selectedCategoryId = $this->selectedCategoryId($request, $categories);
        $programmePage = $this->archivePageForCategory(
            $mainPage,
            ProgrammePage::TYPE_ARCHIVE_PROGRAMME,
            MainPageLink::SLOT_ARCHIVE_PROGRAMME,
            $selectedCategoryId,
        );

        return $this->render($mainPage, 'archive.programme', [
            'gNum' => '03', 'sNum' => '01', 'dNum' => '02',
            'gName' => 'ARCHIVE', 'sName' => '2025 Forum', 'dName' => 'Programme',
            'pageTitle' => $programmePage?->page_title,
            'pageSubtitle' => $programmePage?->subtitle,
            'programmePage' => $programmePage,
            'categories' => $categories,
            'selectedCategoryId' => $selectedCategoryId,
        ]);
    }

    public function archiveSpeakers(MainPage $mainPage)
    {
        $programmePage = $this->linkedProgrammePage($mainPage, MainPageLink::SLOT_ARCHIVE_SPEAKERS, [
            'sessions.speakers',
        ]);

        return $this->render($mainPage, 'archive.speakers', [
            'gNum' => '03', 'sNum' => '01', 'dNum' => '03',
            'gName' => 'ARCHIVE', 'sName' => '2025 Forum', 'dName' => 'Speakers',
            'pageTitle' => $programmePage?->page_title,
            'pageSubtitle' => $programmePage?->subtitle,
            'programmePage' => $programmePage,
        ]);
    }

    public function archivePast(MainPage $mainPage)
    {
        $programmePage = $this->linkedProgrammePage($mainPage, MainPageLink::SLOT_ARCHIVE_LEGACY);

        return $this->render($mainPage, 'archive.past', [
            'gNum' => '03', 'sNum' => '02', 'dNum' => '',
            'gName' => 'ARCHIVE', 'sName' => 'Past Forums (2015~2024)', 'dName' => 'Past Forums (2015~2024)',
            'pageTitle' => $programmePage?->page_title,
            'pageSubtitle' => $programmePage?->subtitle,
            'programmePage' => $programmePage,
        ]);
    }

    public function mediaGallery(Request $request, MainPage $mainPage)
    {
        $categories = $this->mediaCategories(Category::GROUP_CODE_PHOTO_GALLERY);
        $selectedCategoryId = $this->selectedCategoryId($request, $categories);
        $photos = MediaContent::query()
            ->where('type', MediaContent::TYPE_PHOTO_ITEM)
            ->where('is_visible', true)
            ->when($selectedCategoryId !== null, fn ($query) => $query->where('category_id', $selectedCategoryId))
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(16)
            ->withQueryString();

        return $this->render($mainPage, 'media.gallery', [
            'gNum' => '04', 'sNum' => '01', 'gName' => 'MEDIA', 'sName' => 'Photo Gallery',
            'categories' => $categories,
            'selectedCategoryId' => $selectedCategoryId,
            'photos' => $photos,
        ]);
    }

    public function mediaNews(Request $request, MainPage $mainPage)
    {
        $categories = $this->mediaCategories(Category::GROUP_CODE_NEWS_CLIPPINGS);
        $selectedCategoryId = $this->selectedCategoryId($request, $categories);
        $requestedCondition = $request->query('search_condition');
        $requestedKeyword = $request->query('search_keyword');
        $searchCondition = is_string($requestedCondition)
            && in_array($requestedCondition, ['title', 'content'], true)
            ? $requestedCondition
            : 'title';
        $searchKeyword = is_string($requestedKeyword) ? trim($requestedKeyword) : '';
        $newsItems = MediaContent::query()
            ->where('type', MediaContent::TYPE_NEWS_ITEM)
            ->where('is_visible', true)
            ->when($selectedCategoryId !== null, fn ($query) => $query->where('category_id', $selectedCategoryId))
            ->when($searchKeyword !== '', fn ($query) => $query->where($searchCondition, 'like', '%'.$searchKeyword.'%'))
            ->orderBy('sort_order')
            ->orderByDesc('published_date')
            ->orderByDesc('id')
            ->paginate(6)
            ->withQueryString();

        return $this->render($mainPage, 'media.news', [
            'gNum' => '04', 'sNum' => '02', 'gName' => 'MEDIA', 'sName' => 'News Clippings',
            'categories' => $categories,
            'selectedCategoryId' => $selectedCategoryId,
            'newsItems' => $newsItems,
            'searchCondition' => $searchCondition,
            'searchKeyword' => $searchKeyword,
        ]);
    }

    public function mediaNewsView(MainPage $mainPage, ?string $mediaContent = null)
    {
        $newsItem = $mediaContent === null
            ? null
            : MediaContent::query()
                ->whereKey($mediaContent)
                ->where('type', MediaContent::TYPE_NEWS_ITEM)
                ->where('is_visible', true)
                ->firstOrFail();

        return $this->render($mainPage, 'media.news_view', [
            'gNum' => '04', 'sNum' => '02', 'gName' => 'MEDIA', 'sName' => 'News Clippings',
            'pageTitle' => $newsItem?->title,
            'newsItem' => $newsItem,
        ]);
    }

    public function mediaYoutube(Request $request, MainPage $mainPage)
    {
        $categories = $this->mediaCategories(Category::GROUP_CODE_YOUTUBE_CHANNEL);
        $selectedCategoryId = $this->selectedCategoryId($request, $categories);
        $youtubeItems = MediaContent::query()
            ->where('type', MediaContent::TYPE_YOUTUBE)
            ->where('is_visible', true)
            ->when($selectedCategoryId !== null, fn ($query) => $query->where('category_id', $selectedCategoryId))
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        $youtubeItems->each(function (MediaContent $item): void {
            $item->setAttribute('embed_url', $this->youtubeEmbedUrl($item->link));
        });

        return $this->render($mainPage, 'media.youtube', [
            'gNum' => '04', 'sNum' => '03', 'gName' => 'MEDIA', 'sName' => 'Youtube Channel',
            'categories' => $categories,
            'selectedCategoryId' => $selectedCategoryId,
            'youtubeItems' => $youtubeItems,
        ]);
    }

    public function registrationIndex(MainPage $mainPage)
    {
        $registrationPage = $this->linkedRegistrationPage($mainPage);

        return $this->render($mainPage, 'registration.index', [
            'gNum' => '05', 'sNum' => '01', 'gName' => 'REGISTRATION',
            'sName' => 'Registration',
            'pageTitle' => $registrationPage?->page_title,
            'pageSubtitle' => $registrationPage?->subtitle,
            'registrationPage' => $registrationPage,
            'registrationOpen' => $registrationPage ? $this->registrationService->isOpen($registrationPage) : false,
        ]);
    }

    public function registrationConfirm(MainPage $mainPage)
    {
        $registrationPage = $this->linkedRegistrationPage($mainPage);

        return $this->render($mainPage, 'registration.confirm', [
            'gNum' => '05', 'sNum' => '02', 'gName' => 'REGISTRATION', 'sName' => 'Confirm Registration', 'page' => 'view',
            'pageTitle' => $registrationPage?->page_title,
            'pageSubtitle' => $registrationPage?->subtitle,
            'registrationPage' => $registrationPage,
        ]);
    }

    public function registrationRegister(MainPage $mainPage)
    {
        $registrationPage = $this->linkedRegistrationPage($mainPage);

        return $this->render($mainPage, 'registration.register_now', [
            'gNum' => '05', 'sNum' => '01', 'gName' => 'REGISTRATION',
            'sName' => 'Registration',
            'pageTitle' => $registrationPage?->page_title,
            'pageSubtitle' => $registrationPage?->subtitle,
            'registrationPage' => $registrationPage,
            'registrationOpen' => $registrationPage ? $this->registrationService->isOpen($registrationPage) : false,
        ]);
    }

    public function registrationSubmit(FrontendRegistrationRequest $request, MainPage $mainPage): RedirectResponse
    {
        $registrationPage = $this->linkedRegistrationPage($mainPage);
        abort_unless($registrationPage && $this->registrationService->isOpen($registrationPage), 404);

        $this->registrationService->createApplicant($registrationPage, $request->validated());

        return redirect()->route('registration.confirm', ['mainPage' => $mainPage->folder_name])
            ->with('registration_submitted', true);
    }

    public function registrationConfirmLookup(FrontendRegistrationConfirmationRequest $request, MainPage $mainPage): RedirectResponse
    {
        $registrationPage = $this->linkedRegistrationPage($mainPage);
        abort_unless($registrationPage, 404);

        $validated = $request->validated();
        $applicant = $this->registrationService->findApplicant(
            $registrationPage,
            $validated['email'],
            $validated['mobile_last_four'],
        );

        if (! $applicant) {
            return redirect()->route('registration.confirm', ['mainPage' => $mainPage->folder_name])
                ->withInput(['email' => $validated['email']])
                ->with('registration_confirmation_error', 'No registration was found with the information provided.');
        }

        return redirect()->route('registration.confirm', ['mainPage' => $mainPage->folder_name])
            ->with('registration_confirmed', true);
    }

    public function announcementsIndex(Request $request, MainPage $mainPage)
    {
        $requestedCondition = $request->query('search_condition');
        $requestedKeyword = $request->query('search_keyword');
        $searchCondition = is_string($requestedCondition)
            && in_array($requestedCondition, ['title', 'content'], true)
            ? $requestedCondition
            : 'title';
        $searchKeyword = is_string($requestedKeyword) ? trim($requestedKeyword) : '';
        $announcements = $this->announcementQuery()
            ->when($searchKeyword !== '', fn (Builder $query) => $query->where($searchCondition, 'like', '%'.$searchKeyword.'%'))
            ->orderByDesc('is_notice')
            ->orderByDesc('sort_order')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();
        $announcements->getCollection()->transform(function (object $announcement): object {
            $announcement->created_at = Carbon::parse($announcement->created_at);

            return $announcement;
        });

        return $this->render($mainPage, 'announcements.index', [
            'gNum' => '06', 'sNum' => '01', 'gName' => 'ANNOUNCEMENTS', 'sName' => 'Announcements',
            'announcements' => $announcements,
            'searchCondition' => $searchCondition,
            'searchKeyword' => $searchKeyword,
        ]);
    }

    public function announcementsView(MainPage $mainPage, ?string $announcement = null)
    {
        $announcementPost = $announcement === null
            ? null
            : $this->announcementQuery()->where('id', $announcement)->firstOrFail();
        $attachments = [];
        $previousAnnouncement = null;
        $nextAnnouncement = null;
        $pageSubtitle = null;

        if ($announcementPost) {
            DB::table('board_notices')->where('id', $announcementPost->id)->increment('view_count');
            $announcementPost->view_count++;
            $announcementPost->created_at = Carbon::parse($announcementPost->created_at);
            $attachments = $this->decodeJsonArray($announcementPost->attachments);
            $customFields = $this->decodeJsonArray($announcementPost->custom_fields);
            $pageSubtitle = $customFields['subtitle'] ?? null;
            $previousAnnouncement = $this->announcementQuery()
                ->where('id', '>', $announcementPost->id)
                ->orderBy('id')
                ->first();
            $nextAnnouncement = $this->announcementQuery()
                ->where('id', '<', $announcementPost->id)
                ->orderByDesc('id')
                ->first();
        }

        return $this->render($mainPage, 'announcements.view', [
            'gNum' => '06', 'sNum' => '01', 'gName' => 'ANNOUNCEMENTS', 'sName' => 'Announcements', 'page' => 'view',
            'pageTitle' => $announcementPost?->title,
            'pageSubtitle' => $pageSubtitle,
            'announcementPost' => $announcementPost,
            'attachments' => $attachments,
            'previousAnnouncement' => $previousAnnouncement,
            'nextAnnouncement' => $nextAnnouncement,
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

    /**
     * @param  array<int, string>  $relations
     */
    private function linkedAboutPage(MainPage $mainPage, string $slot, array $relations = []): ?AboutPage
    {
        $type = MainPageLink::aboutPageTypeMap()[$slot] ?? null;
        if ($type === null) {
            return null;
        }

        return AboutPage::query()
            ->where('type', $type)
            ->whereHas('mainPageLink', static function ($query) use ($mainPage, $slot): void {
                $query->where('main_page_id', $mainPage->id)
                    ->where('slot', $slot);
            })
            ->with($relations)
            ->first();
    }

    /**
     * @param  array<int, string>  $relations
     */
    private function linkedProgrammePage(MainPage $mainPage, string $slot, array $relations = []): ?ProgrammePage
    {
        $type = MainPageLink::programmePageTypeMap()[$slot] ?? null;
        if ($type === null) {
            return null;
        }

        return ProgrammePage::query()
            ->where('type', $type)
            ->whereHas('mainPageLink', static function ($query) use ($mainPage, $slot): void {
                $query->where('main_page_id', $mainPage->id)
                    ->where('slot', $slot);
            })
            ->with($relations)
            ->first();
    }

    private function linkedRegistrationPage(MainPage $mainPage): ?RegistrationPage
    {
        return RegistrationPage::query()
            ->whereHas('mainPageLink', static function ($query) use ($mainPage): void {
                $query->where('main_page_id', $mainPage->id)
                    ->where('slot', MainPageLink::SLOT_REGISTRATION);
            })
            ->first();
    }

    private function archivePageForCategory(MainPage $currentMainPage, string $type, string $slot, ?int $categoryId): ?ProgrammePage
    {
        if ($categoryId === null) {
            return $this->linkedProgrammePage($currentMainPage, $slot);
        }

        $pages = ProgrammePage::query()
            ->where('type', $type)
            ->where('category_id', $categoryId)
            ->whereHas('mainPageLink', static function ($query) use ($slot): void {
                $query->where('slot', $slot)
                    ->whereHas('mainPage', static fn ($mainPageQuery) => $mainPageQuery->where('is_visible', true));
            })
            ->with(['mainPageLink' => static function ($query) use ($slot): void {
                $query->where('slot', $slot)->with('mainPage');
            }])
            ->get();

        return $pages->first(
            static fn (ProgrammePage $page): bool => (int) $page->mainPageLink?->main_page_id === $currentMainPage->id
        ) ?? $pages->first();
    }

    private function announcementQuery(): Builder
    {
        return DB::table('board_notices')
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->where('is_secret', false);
    }

    /** @return array<int|string, mixed> */
    private function decodeJsonArray(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (! is_string($value) || $value === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }

    /** @return Collection<int, Category> */
    private function mediaCategories(string $groupCode): Collection
    {
        $groupId = Category::query()
            ->active()
            ->whereNull('parent_id')
            ->where('code', $groupCode)
            ->value('id');

        $categories = Category::query()
            ->active()
            ->where('parent_id', $groupId ?? 0)
            ->orderByDesc('display_order')
            ->orderByDesc('id')
            ->get();

        return $this->sortYearTabs(
            $categories,
            static fn (Category $category): string => (string) $category->name
        );
    }

    /**
     * 숫자 연도가 포함된 탭은 최신 연도순으로 정렬하고,
     * 연도가 없는 항목끼리는 관리자의 기존 표시 순서를 보존한다.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param  Collection<TKey, TValue>  $items
     * @param  callable(TValue): string  $labelResolver
     * @return Collection<int, TValue>
     */
    private function sortYearTabs(Collection $items, callable $labelResolver): Collection
    {
        return $items
            ->sort(function (mixed $left, mixed $right) use ($labelResolver): int {
                $leftYear = $this->yearFromTabLabel($labelResolver($left));
                $rightYear = $this->yearFromTabLabel($labelResolver($right));

                if ($leftYear !== null && $rightYear !== null && $leftYear !== $rightYear) {
                    return $rightYear <=> $leftYear;
                }

                if ($leftYear !== null && $rightYear === null) {
                    return -1;
                }

                if ($leftYear === null && $rightYear !== null) {
                    return 1;
                }

                return 0;
            })
            ->values();
    }

    private function yearFromTabLabel(string $label): ?int
    {
        if (preg_match('/(?<!\d)((?:19|20)\d{2})(?!\d)/', $label, $matches) !== 1) {
            return null;
        }

        return (int) $matches[1];
    }

    /** @param Collection<int, Category> $categories */
    private function selectedCategoryId(Request $request, Collection $categories): ?int
    {
        $requestedCategoryId = $request->query('category_id');
        if (! is_scalar($requestedCategoryId) || ! ctype_digit((string) $requestedCategoryId)) {
            return $categories->first()?->id;
        }

        $categoryId = (int) $requestedCategoryId;

        return $categories->contains('id', $categoryId) ? $categoryId : $categories->first()?->id;
    }

    private function youtubeEmbedUrl(?string $link): ?string
    {
        if (! is_string($link) || trim($link) === '') {
            return null;
        }

        $parts = parse_url(trim($link));
        $host = strtolower($parts['host'] ?? '');
        $path = trim($parts['path'] ?? '', '/');
        $videoId = null;

        if (in_array($host, ['youtu.be', 'www.youtu.be'], true)) {
            $videoId = explode('/', $path)[0] ?? null;
        } elseif (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com'], true)) {
            if ($path === 'watch') {
                parse_str($parts['query'] ?? '', $query);
                $videoId = $query['v'] ?? null;
            } elseif (preg_match('#^(?:embed|shorts)/([^/]+)#', $path, $matches) === 1) {
                $videoId = $matches[1];
            }
        }

        return is_string($videoId) && preg_match('/^[A-Za-z0-9_-]+$/', $videoId) === 1
            ? 'https://www.youtube.com/embed/'.$videoId
            : null;
    }
}
