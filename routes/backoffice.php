<?php

use App\Http\Controllers\Backoffice\AboutCoOrganizerController;
use App\Http\Controllers\Backoffice\AboutForumController;
use App\Http\Controllers\Backoffice\AboutVenueController;
use App\Http\Controllers\Backoffice\AccessStatisticsController;
use App\Http\Controllers\Backoffice\AddressBookController;
use App\Http\Controllers\Backoffice\AdminController;
use App\Http\Controllers\Backoffice\AdminGroupController;
use App\Http\Controllers\Backoffice\AdminMenuController;
use App\Http\Controllers\Backoffice\AuthController;
use App\Http\Controllers\Backoffice\BannerController;
use App\Http\Controllers\Backoffice\BoardController;
use App\Http\Controllers\Backoffice\BoardPostController;
use App\Http\Controllers\Backoffice\BoardSkinController;
use App\Http\Controllers\Backoffice\BoardTemplateController;
use App\Http\Controllers\Backoffice\CategoryController;
use App\Http\Controllers\Backoffice\HomepagePartnerController;
use App\Http\Controllers\Backoffice\LogController;
use App\Http\Controllers\Backoffice\MailCampaignController;
use App\Http\Controllers\Backoffice\MainPageController;
use App\Http\Controllers\Backoffice\MediaContentController;
use App\Http\Controllers\Backoffice\MemberController;
use App\Http\Controllers\Backoffice\PopupController;
use App\Http\Controllers\Backoffice\ProgrammePageController;
use App\Http\Controllers\Backoffice\RegistrationApplicantController;
use App\Http\Controllers\Backoffice\RegistrationPageController;
use App\Http\Controllers\Backoffice\SettingController;
use App\Http\Controllers\Backoffice\SpeakerController;
use App\Http\Controllers\Backoffice\SteeringCommitteeController;
use App\Http\Controllers\Backoffice\UserController;
use App\Models\MediaContent;
use App\Models\ProgrammePage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// =============================================================================
// 백오피스 인증 라우트
// =============================================================================
Route::prefix('backoffice')->name('backoffice.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});

// =============================================================================
// 백오피스 라우트 (관리자 전용)
// =============================================================================

Route::prefix('backoffice')->middleware(['backoffice'])->group(function () {

    // 대시보드
    Route::get('/', [App\Http\Controllers\Backoffice\DashboardController::class, 'index'])
        ->name('backoffice.dashboard');

    // 대시보드 API
    Route::get('/api/statistics', [App\Http\Controllers\Backoffice\DashboardController::class, 'statistics'])
        ->name('backoffice.api.statistics');

    $registerProgrammeRoutes = static function (string $uri, string $routeName, string $type): void {
        Route::get($uri, [ProgrammePageController::class, 'index'])->defaults('programme_type', $type)->name($routeName.'.index');
        Route::get($uri.'/create', [ProgrammePageController::class, 'create'])->defaults('programme_type', $type)->name($routeName.'.create');
        Route::post($uri, [ProgrammePageController::class, 'store'])->defaults('programme_type', $type)->name($routeName.'.store');
        Route::get($uri.'/{programmePage}/edit', [ProgrammePageController::class, 'edit'])->defaults('programme_type', $type)->name($routeName.'.edit');
        Route::put($uri.'/{programmePage}', [ProgrammePageController::class, 'update'])->defaults('programme_type', $type)->name($routeName.'.update');
        Route::delete($uri.'/{programmePage}', [ProgrammePageController::class, 'destroy'])->defaults('programme_type', $type)->name($routeName.'.destroy');
        Route::post($uri.'/delete-multiple', [ProgrammePageController::class, 'destroyMultiple'])->defaults('programme_type', $type)->name($routeName.'.delete-multiple');
    };

    // /programme의 동적 경로가 하위 메뉴를 가로채지 않도록 상세 메뉴를 먼저 등록합니다.
    $registerProgrammeRoutes('programme/theme', 'backoffice.programme-theme', ProgrammePage::TYPE_THEME);
    $registerProgrammeRoutes('programme/speakers', 'backoffice.programme-speakers', ProgrammePage::TYPE_SPEAKERS);
    $registerProgrammeRoutes('programme/book', 'backoffice.programme-book', ProgrammePage::TYPE_BOOK);
    $registerProgrammeRoutes('programme', 'backoffice.programme', ProgrammePage::TYPE_PROGRAMME);
    $registerProgrammeRoutes('archives/2025-plus/theme', 'backoffice.archive-theme', ProgrammePage::TYPE_ARCHIVE_THEME);
    $registerProgrammeRoutes('archives/2025-plus/programme', 'backoffice.archive-programme', ProgrammePage::TYPE_ARCHIVE_PROGRAMME);
    $registerProgrammeRoutes('archives/2025-plus/speakers', 'backoffice.archive-speakers', ProgrammePage::TYPE_ARCHIVE_SPEAKERS);
    $registerProgrammeRoutes('archives/2015-2024', 'backoffice.archive-legacy', ProgrammePage::TYPE_ARCHIVE_LEGACY);

    $registerMediaRoutes = static function (string $uri, string $routeName, string $folderType, ?string $itemType = null): void {
        Route::get($uri, [MediaContentController::class, 'index'])->defaults('media_type', $folderType)->name($routeName.'.index');
        Route::get($uri.'/create', [MediaContentController::class, 'create'])->defaults('media_type', $folderType)->name($routeName.'.create');
        Route::post($uri, [MediaContentController::class, 'store'])->defaults('media_type', $folderType)->name($routeName.'.store');
        Route::get($uri.'/{mediaContent}/edit', [MediaContentController::class, 'edit'])->defaults('media_type', $folderType)->name($routeName.'.edit');
        Route::put($uri.'/{mediaContent}', [MediaContentController::class, 'update'])->defaults('media_type', $folderType)->name($routeName.'.update');
        Route::delete($uri.'/{mediaContent}', [MediaContentController::class, 'destroy'])->defaults('media_type', $folderType)->name($routeName.'.destroy');
        Route::post($uri.'/delete-multiple', [MediaContentController::class, 'destroyMultiple'])->defaults('media_type', $folderType)->name($routeName.'.delete-multiple');

        if ($itemType === null) {
            return;
        }

        $itemRouteName = $routeName.'-items';
        Route::get($uri.'/{mediaParent}/items', [MediaContentController::class, 'nestedIndex'])->defaults('media_type', $itemType)->defaults('media_parent_type', $folderType)->name($itemRouteName.'.index');
        Route::get($uri.'/{mediaParent}/items/create', [MediaContentController::class, 'nestedCreate'])->defaults('media_type', $itemType)->defaults('media_parent_type', $folderType)->name($itemRouteName.'.create');
        Route::post($uri.'/{mediaParent}/items', [MediaContentController::class, 'nestedStore'])->defaults('media_type', $itemType)->defaults('media_parent_type', $folderType)->name($itemRouteName.'.store');
        Route::get($uri.'/{mediaParent}/items/{mediaContent}/edit', [MediaContentController::class, 'nestedEdit'])->defaults('media_type', $itemType)->defaults('media_parent_type', $folderType)->name($itemRouteName.'.edit');
        Route::put($uri.'/{mediaParent}/items/{mediaContent}', [MediaContentController::class, 'nestedUpdate'])->defaults('media_type', $itemType)->defaults('media_parent_type', $folderType)->name($itemRouteName.'.update');
        Route::delete($uri.'/{mediaParent}/items/{mediaContent}', [MediaContentController::class, 'nestedDestroy'])->defaults('media_type', $itemType)->defaults('media_parent_type', $folderType)->name($itemRouteName.'.destroy');
        Route::post($uri.'/{mediaParent}/items/delete-multiple', [MediaContentController::class, 'nestedDestroyMultiple'])->defaults('media_type', $itemType)->defaults('media_parent_type', $folderType)->name($itemRouteName.'.delete-multiple');
    };

    $registerMediaRoutes('media/photo-gallery', 'backoffice.media-photo', MediaContent::TYPE_PHOTO_ITEM);
    $registerMediaRoutes('media/news-clippings', 'backoffice.media-news', MediaContent::TYPE_NEWS_ITEM);
    $registerMediaRoutes('media/youtube', 'backoffice.media-youtube', MediaContent::TYPE_YOUTUBE);

    Route::post('registration/delete-multiple', [RegistrationPageController::class, 'destroyMultiple'])->name('backoffice.registration.delete-multiple');
    Route::resource('registration', RegistrationPageController::class, ['names' => 'backoffice.registration', 'parameters' => ['registration' => 'registrationPage']])->except(['show']);

    Route::post('registration/applicants/delete-multiple', [RegistrationApplicantController::class, 'destroyMultiple'])->name('backoffice.registration-applicants.delete-multiple');
    Route::resource('registration/applicants', RegistrationApplicantController::class, ['names' => 'backoffice.registration-applicants', 'parameters' => ['applicants' => 'registrationApplicant']])->except(['show']);

    Route::get('address-books/sample', [AddressBookController::class, 'sample'])->name('backoffice.address-books.sample');
    Route::post('address-books/delete-multiple', [AddressBookController::class, 'destroyMultiple'])->name('backoffice.address-books.delete-multiple');
    Route::post('address-books/{addressBook}/contacts', [AddressBookController::class, 'storeContact'])->name('backoffice.address-books.contacts.store');
    Route::put('address-books/{addressBook}/contacts/{contact}', [AddressBookController::class, 'updateContact'])->scopeBindings()->name('backoffice.address-books.contacts.update');
    Route::delete('address-books/{addressBook}/contacts/{contact}', [AddressBookController::class, 'destroyContact'])->scopeBindings()->name('backoffice.address-books.contacts.destroy');
    Route::resource('address-books', AddressBookController::class, ['names' => 'backoffice.address-books', 'parameters' => ['address-books' => 'addressBook']])->except(['show']);

    Route::post('mail-campaigns/delete-multiple', [MailCampaignController::class, 'destroyMultiple'])->name('backoffice.mail-campaigns.delete-multiple');
    Route::post('mail-campaigns/{mailCampaign}/send', [MailCampaignController::class, 'send'])->name('backoffice.mail-campaigns.send');
    Route::resource('mail-campaigns', MailCampaignController::class, ['names' => 'backoffice.mail-campaigns', 'parameters' => ['mail-campaigns' => 'mailCampaign']])->except(['show']);

    Route::post('about-the-forum/delete-multiple', [AboutForumController::class, 'destroyMultiple'])
        ->name('backoffice.about-the-forum.delete-multiple');
    Route::resource('about-the-forum', AboutForumController::class, [
        'names' => 'backoffice.about-the-forum',
        'parameters' => ['about-the-forum' => 'aboutPage'],
    ])->except(['show']);

    Route::post('steering-committee/delete-multiple', [SteeringCommitteeController::class, 'destroyMultiple'])
        ->name('backoffice.steering-committee.delete-multiple');
    Route::resource('steering-committee', SteeringCommitteeController::class, [
        'names' => 'backoffice.steering-committee',
        'parameters' => ['steering-committee' => 'aboutPage'],
    ])->except(['show']);

    Route::post('co-organizers/delete-multiple', [AboutCoOrganizerController::class, 'destroyMultiple'])
        ->name('backoffice.co-organizers.delete-multiple');
    Route::resource('co-organizers', AboutCoOrganizerController::class, [
        'names' => 'backoffice.co-organizers',
        'parameters' => ['co-organizers' => 'aboutPage'],
    ])->except(['show']);

    Route::post('venue/delete-multiple', [AboutVenueController::class, 'destroyMultiple'])
        ->name('backoffice.venue.delete-multiple');
    Route::resource('venue', AboutVenueController::class, [
        'names' => 'backoffice.venue',
        'parameters' => ['venue' => 'aboutPage'],
    ])->except(['show']);

    Route::post('main-pages/delete-multiple', [MainPageController::class, 'destroyMultiple'])
        ->name('backoffice.main-pages.delete-multiple');
    Route::resource('main-pages', MainPageController::class, [
        'names' => 'backoffice.main-pages',
        'parameters' => ['main-pages' => 'mainPage'],
    ])->except(['show']);

    Route::post('speakers/delete-multiple', [SpeakerController::class, 'destroyMultiple'])
        ->name('backoffice.speakers.delete-multiple');
    Route::resource('speakers', SpeakerController::class, [
        'names' => 'backoffice.speakers',
    ])->except(['show']);

    Route::post('organized/delete-multiple', [HomepagePartnerController::class, 'destroyMultiple'])
        ->name('backoffice.organized.delete-multiple');
    Route::resource('organized', HomepagePartnerController::class, [
        'names' => 'backoffice.organized',
        'parameters' => ['organized' => 'homepagePartner'],
    ])->except(['show']);

    Route::post('partnerships/delete-multiple', [HomepagePartnerController::class, 'destroyMultiple'])
        ->name('backoffice.partnerships.delete-multiple');
    Route::resource('partnerships', HomepagePartnerController::class, [
        'names' => 'backoffice.partnerships',
        'parameters' => ['partnerships' => 'homepagePartner'],
    ])->except(['show']);

    // -------------------------------------------------------------------------
    // 시스템 관리
    // -------------------------------------------------------------------------

    // 관리자 메뉴 관리
    Route::resource('admin-menus', AdminMenuController::class, [
        'names' => 'backoffice.admin-menus',
    ])->except(['show']);

    // 메뉴 순서 업데이트
    Route::post('admin-menus/update-order', [AdminMenuController::class, 'updateOrder'])
        ->name('backoffice.admin-menus.update-order');

    // 메뉴 부모 업데이트 (드래그로 메뉴 이동)
    Route::post('admin-menus/update-parent', [AdminMenuController::class, 'updateParent'])
        ->name('backoffice.admin-menus.update-parent');

    // 카테고리 관리
    // 카테고리 순서 업데이트 (resource 라우트보다 앞에 위치)
    Route::post('categories/update-order', [CategoryController::class, 'updateOrder'])
        ->name('backoffice.categories.update-order');

    // 활성 카테고리 조회 (AJAX - resource 라우트보다 앞에 위치)
    Route::get('categories/active/{group}', [CategoryController::class, 'getActiveCategories'])
        ->name('backoffice.categories.active');

    // 특정 그룹의 1차 카테고리 조회 (AJAX)
    Route::get('categories/get-by-group/{groupId}', [CategoryController::class, 'getByGroup'])
        ->name('backoffice.categories.get-by-group');

    // 카테고리 수정용 데이터 조회 (AJAX)
    Route::get('categories/{category}/edit-data', [CategoryController::class, 'getEditData'])
        ->name('backoffice.categories.edit-data');

    // 인라인 수정 (AJAX)
    Route::post('categories/{category}/update-inline', [CategoryController::class, 'updateInline'])
        ->name('backoffice.categories.update-inline');

    // 모달 등록 (AJAX)
    Route::post('categories/store-modal', [CategoryController::class, 'storeModal'])
        ->name('backoffice.categories.store-modal');

    // 모달 수정 (AJAX)
    Route::put('categories/update-modal', [CategoryController::class, 'updateModal'])
        ->name('backoffice.categories.update-modal');

    // 미리 생성될 코드 조회 (AJAX)
    Route::post('categories/generate-preview-code', [CategoryController::class, 'generatePreviewCode'])
        ->name('backoffice.categories.generate-preview-code');

    Route::resource('categories', CategoryController::class, [
        'names' => 'backoffice.categories',
    ])->except(['show']);

    // 기본설정 관리
    Route::get('setting', [SettingController::class, 'index'])
        ->name('backoffice.setting.index');
    Route::post('setting', [SettingController::class, 'update'])
        ->name('backoffice.setting.update');

    // 접속 로그 관리
    Route::get('logs/access', [LogController::class, 'access'])
        ->name('backoffice.logs.access');
    Route::get('user-access-logs', [LogController::class, 'userAccessLogs'])
        ->name('backoffice.user-access-logs');
    Route::get('admin-access-logs', [LogController::class, 'adminAccessLogs'])
        ->name('backoffice.admin-access-logs');

    // 통계 관리
    Route::get('access-statistics', [AccessStatisticsController::class, 'index'])
        ->name('backoffice.access-statistics');
    Route::get('access-statistics/get-statistics', [AccessStatisticsController::class, 'getStatistics'])
        ->name('backoffice.access-statistics.get-statistics');

    // 관리자 계정 관리
    Route::post('admins/bulk-destroy', [AdminController::class, 'bulkDestroy'])
        ->name('backoffice.admins.bulk-destroy');
    Route::post('admins/check-login-id', [AdminController::class, 'checkLoginId'])
        ->name('backoffice.admins.check-login-id');
    Route::resource('admins', AdminController::class, [
        'names' => 'backoffice.admins',
    ]);

    // 관리자 권한 그룹 관리
    Route::resource('admin-groups', AdminGroupController::class, [
        'names' => 'backoffice.admin-groups',
    ])->except(['show']);

    // 권한 그룹 권한 설정
    Route::get('admin-groups/{admin_group}/permissions', [AdminGroupController::class, 'editPermissions'])
        ->name('backoffice.admin-groups.permissions.edit');
    Route::post('admin-groups/{admin_group}/permissions', [AdminGroupController::class, 'updatePermissions'])
        ->name('backoffice.admin-groups.permissions.update');

    // -------------------------------------------------------------------------
    // 콘텐츠 관리
    // -------------------------------------------------------------------------

    // 이미지 업로드
    Route::post('upload-image', function (Request $request) {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('uploads/editor', 'public');

            return response()->json([
                'uploaded' => true,
                'url' => asset('storage/'.$path),
            ]);
        }

        return response()->json([
            'uploaded' => false,
            'error' => ['message' => '이미지 업로드에 실패했습니다.'],
        ]);
    });

    // 정렬 순서 업데이트
    Route::post('board-posts/update-sort-order', [BoardPostController::class, 'updateSortOrder'])->name('backoffice.board-posts.update-sort-order');

    // 게시글 관리 (특정 게시판)
    Route::prefix('board-posts/{slug}')->name('backoffice.board-posts.')->group(function () {
        Route::get('/', [BoardPostController::class, 'index'])->name('index');
        Route::get('/create', [BoardPostController::class, 'create'])->name('create');
        Route::post('/', [BoardPostController::class, 'store'])->name('store');
        Route::get('/{post}', [BoardPostController::class, 'show'])->name('show');
        Route::get('/{post}/edit', [BoardPostController::class, 'edit'])->name('edit');
        Route::put('/{post}', [BoardPostController::class, 'update'])->name('update');
        Route::delete('/{post}', [BoardPostController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-destroy', [BoardPostController::class, 'bulkDestroy'])->name('bulk_destroy');
    });

    // 게시판 관리
    Route::resource('boards', BoardController::class, [
        'names' => 'backoffice.boards',
    ])->except(['show']); // show는 제외 (게시글 목록과 충돌)

    // 게시판 템플릿 관리
    Route::resource('board-templates', BoardTemplateController::class, [
        'names' => 'backoffice.board-templates',
        'parameters' => ['board-templates' => 'boardTemplate'],
    ]);

    // 게시판 템플릿 추가 기능
    Route::post('board-templates/{boardTemplate}/duplicate', [BoardTemplateController::class, 'duplicate'])
        ->name('backoffice.board-templates.duplicate');
    Route::get('board-templates/{boardTemplate}/data', [BoardTemplateController::class, 'getTemplateData'])
        ->name('backoffice.board-templates.data');

    // 게시판 스킨 관리
    Route::resource('board-skins', BoardSkinController::class, [
        'names' => 'backoffice.board-skins',
        'parameters' => ['board-skins' => 'boardSkin'],
    ]);

    // 게시판 스킨 템플릿 편집
    Route::prefix('board-skins/{boardSkin}')->name('backoffice.board-skins.')->group(function () {
        Route::get('template', [BoardSkinController::class, 'editTemplate'])
            ->name('edit_template');
        Route::post('template', [BoardSkinController::class, 'updateTemplate'])
            ->name('update_template');
    });

    // 게시글 관리
    Route::resource('posts', BoardPostController::class, [
        'names' => 'backoffice.posts',
    ]);

    // 회원 관리
    Route::resource('users', UserController::class, [
        'names' => 'backoffice.users',
    ]);
    Route::get('withdrawn', [MemberController::class, 'withdrawn'])->name('backoffice.withdrawn');
    Route::post('withdrawn/{id}/restore', [MemberController::class, 'restore'])->name('backoffice.withdrawn.restore');
    Route::post('withdrawn/{id}/force-delete', [MemberController::class, 'forceDelete'])->name('backoffice.withdrawn.force-delete');
    Route::post('withdrawn/force-delete-multiple', [MemberController::class, 'forceDeleteMultiple'])->name('backoffice.withdrawn.force-delete-multiple');

    Route::resource('members', MemberController::class, [
        'names' => 'backoffice.members',
        'parameters' => ['members' => 'user'],
    ]);
    Route::post('members/check-email', [MemberController::class, 'checkDuplicateEmail'])->name('backoffice.members.check-email');
    Route::post('members/check-phone', [MemberController::class, 'checkDuplicatePhone'])->name('backoffice.members.check-phone');
    Route::get('members/search-school', [MemberController::class, 'searchSchool'])->name('backoffice.members.search-school');
    Route::post('members/delete-multiple', [MemberController::class, 'destroyMultiple'])->name('backoffice.members.delete-multiple');
    Route::get('members/export', [MemberController::class, 'export'])->name('backoffice.members.export');

    // 배너 관리
    Route::resource('banners', BannerController::class, [
        'names' => 'backoffice.banners',
    ]);
    Route::post('banners/update-order', [BannerController::class, 'updateOrder'])->name('backoffice.banners.update-order');

    // 팝업 관리
    Route::resource('popups', PopupController::class, [
        'names' => 'backoffice.popups',
    ]);
    Route::post('popups/update-order', [PopupController::class, 'updateOrder'])->name('backoffice.popups.update-order');

    // 세션 연장
    Route::post('session/extend', [App\Http\Controllers\Backoffice\SessionController::class, 'extend'])
        ->name('backoffice.session.extend');
});
