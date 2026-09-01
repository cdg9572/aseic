<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Board;
use App\Models\MainPage;
use App\Models\Popup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function redirectToVisibleForum(): RedirectResponse
    {
        $mainPage = MainPage::query()
            ->where('is_visible', true)
            ->orderByDesc('event_start_date')
            ->orderByDesc('id')
            ->firstOrFail();

        return redirect()->route('home', ['mainPage' => $mainPage->folder_name]);
    }

    public function index(MainPage $mainPage): View
    {
        abort_unless($mainPage->is_visible, 404);

        URL::defaults(['mainPage' => $mainPage->folder_name]);
        $mainPage->load([
            'speakers' => static fn ($query) => $query->where('is_active', true),
        ]);

        $gNum = 'main';
        $gName = '';
        $sName = '';

        // gallerys 게시판 최신글 4개
        $galleryPosts = $this->getLatestPosts('gallerys', 4);

        // notices 게시판 최신글 4개
        $noticePosts = $this->getLatestPosts('notices', 4);

        // 활성화된 팝업 조회 (쿠키 확인하여 숨겨진 팝업 제외)
        $popups = Popup::select('id', 'title', 'popup_type', 'popup_display_type', 'popup_image', 'popup_content', 'url', 'url_target', 'width', 'height', 'position_top', 'position_left')
            ->whereKey($mainPage->popup_id)
            ->active()
            ->inPeriod()
            ->ordered()
            ->get()
            ->filter(function ($popup) {
                return ! $this->isPopupHidden($popup->id);
            });

        // 활성화된 배너 조회
        $banners = Banner::query()
            ->whereKey($mainPage->banner_id)
            ->active()
            ->inPeriod()
            ->ordered()
            ->get();

        $speakers = $mainPage->speakers;

        return view($this->forumView($mainPage, 'main'), compact('mainPage', 'gNum', 'gName', 'sName', 'galleryPosts', 'noticePosts', 'popups', 'banners', 'speakers'));
    }

    /**
     * 특정 게시판의 최신글을 가져옵니다.
     */
    private function getLatestPosts($boardSlug, $limit = 4)
    {
        try {
            $board = Board::where('slug', $boardSlug)->first();
            if (! $board) {
                return collect();
            }

            $tableName = "board_{$boardSlug}";

            // 테이블 존재 여부 확인
            if (! DB::getSchemaBuilder()->hasTable($tableName)) {
                return collect();
            }

            $query = DB::table($tableName)
                ->select('id', 'title', 'created_at', 'thumbnail', 'category')
                ->whereNull('deleted_at')
                ->where('is_active', true);

            if ($boardSlug !== 'notices') {
                $query->where('category', '국문');
            }

            return $query
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($post) use ($boardSlug) {
                    return (object) [
                        'id' => $post->id,
                        'title' => $post->title,
                        'created_at' => $post->created_at,
                        'thumbnail' => $post->thumbnail,
                        'url' => route('backoffice.board-posts.show', [$boardSlug, $post->id]),
                    ];
                });

        } catch (\Exception $e) {
            Log::error('게시판 데이터 조회 오류: '.$e->getMessage());

            return collect();
        }
    }

    private function isPopupHidden(int $popupId): bool
    {
        $cookieValue = $_COOKIE['popup_hide_'.$popupId] ?? null;

        return in_array(strtolower((string) $cookieValue), ['1', 'true'], true);
    }

    private function forumView(MainPage $mainPage, string $template): string
    {
        $eventView = 'forums.'.$mainPage->folder_name.'.'.$template;

        return ViewFacade::exists($eventView) ? $eventView : 'forums.default.'.$template;
    }
}
