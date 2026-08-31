<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SubController;
use App\Http\Controllers\Backoffice\PopupController;

// =============================================================================
// 기본 라우트 파일
// =============================================================================

// 메인 페이지
Route::get('/', [HomeController::class, 'index'])->name('home');

// 팝업 표시 (일반 팝업용)
Route::get('/popup/{popup}', [PopupController::class, 'showPopup'])->name('popup.show');

// 사용자 퍼블리싱 페이지
Route::prefix('about')->name('about.')->group(function () {
    Route::get('/forum', [SubController::class, 'aboutForum'])->name('forum');
    Route::get('/committee', [SubController::class, 'aboutCommittee'])->name('committee');
    Route::get('/organizers', [SubController::class, 'aboutOrganizers'])->name('organizers');
    Route::get('/venue', [SubController::class, 'aboutVenue'])->name('venue');
});

Route::prefix('programme')->name('programme.')->group(function () {
    Route::get('/theme', [SubController::class, 'programmeTheme'])->name('theme');
    Route::get('/', [SubController::class, 'programmeList'])->name('list');
    Route::redirect('/list', '/programme');
    Route::get('/speakers', [SubController::class, 'programmeSpeakers'])->name('speakers');
    Route::get('/book', [SubController::class, 'programmeBook'])->name('book');
});

Route::prefix('archive')->name('archive.')->group(function () {
    Route::get('/theme', [SubController::class, 'archiveTheme'])->name('theme');
    Route::get('/programme', [SubController::class, 'archiveProgramme'])->name('programme');
    Route::get('/speakers', [SubController::class, 'archiveSpeakers'])->name('speakers');
    Route::get('/past', [SubController::class, 'archivePast'])->name('past');
});

Route::prefix('media')->name('media.')->group(function () {
    Route::get('/gallery', [SubController::class, 'mediaGallery'])->name('gallery');
    Route::get('/news', [SubController::class, 'mediaNews'])->name('news');
    Route::get('/news/view', [SubController::class, 'mediaNewsView'])->name('news.view');
    Route::get('/youtube', [SubController::class, 'mediaYoutube'])->name('youtube');
});

Route::prefix('registration')->name('registration.')->group(function () {
    Route::get('/', [SubController::class, 'registrationIndex'])->name('index');
    Route::get('/register', [SubController::class, 'registrationRegister'])->name('register');
    Route::get('/confirm', [SubController::class, 'registrationConfirm'])->name('confirm');
});

Route::prefix('announcements')->name('announcements.')->group(function () {
    Route::get('/', [SubController::class, 'announcementsIndex'])->name('index');
    Route::get('/view', [SubController::class, 'announcementsView'])->name('view');
});

// 인증 관련 라우트
Route::prefix('auth')->name('auth.')->group(function () {
    // 로그인
    Route::get('/login', [LoginController::class, 'showLoginForm'])
        ->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');

    // 회원가입
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])
        ->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // 비밀번호 재설정
    Route::prefix('password')->name('password.')->group(function () {
        Route::get('/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
            ->name('request');
        Route::post('/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
            ->name('email');
        Route::get('/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
            ->name('reset');
        Route::post('/reset', [ResetPasswordController::class, 'reset'])
            ->name('update');
    });
});

// =============================================================================
// 분리된 라우트 파일들 포함
// =============================================================================

// 백오피스 라우트 (관리자 전용)
require __DIR__.'/backoffice.php';
