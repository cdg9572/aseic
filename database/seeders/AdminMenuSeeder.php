<?php

namespace Database\Seeders;

use App\Models\AdminMenu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminMenuSeeder extends Seeder
{
    /**
     * ASEIC 관리자 사이트맵을 현재 기획 기준으로 동기화합니다.
     */
    public function run(): void
    {
        $menus = [
            $this->menu(1, null, '대시보드', '/backoffice', 'fa-tachometer-alt', 1, 'dashboard'),

            $this->menu(31, null, '기본 설정', null, 'fa-cogs', 2, 'settings'),
            $this->menu(32, 31, '기본 설정', '/backoffice/setting', null, 1, 'settings.general'),
            $this->menu(33, 31, '관리자 관리', '/backoffice/admins', null, 2, 'settings.admins'),
            $this->menu(34, 31, '권한그룹 관리', '/backoffice/admin-groups', null, 3, 'settings.admin-groups'),
            $this->menu(35, 31, '메뉴 관리', '/backoffice/admin-menus', null, 4, 'settings.admin-menus'),
            $this->menu(42, 31, '코드 관리', '/backoffice/categories', null, 5, 'settings.categories'),

            $this->menu(20, null, 'Homepage', null, 'fa-home', 3, 'homepage'),
            $this->menu(21, 20, 'Main Page', '/backoffice/main-pages', null, 1, 'homepage.main-page'),
            $this->menu(22, 20, '배너관리', '/backoffice/banners', null, 2, 'homepage.banners'),
            $this->menu(23, 20, '팝업관리', '/backoffice/popups', null, 3, 'homepage.popups'),
            $this->menu(24, 20, 'Speakers 관리', '/backoffice/speakers', null, 4, 'homepage.speakers'),
            $this->menu(25, 20, 'Organized 관리', '/backoffice/organized', null, 5, 'homepage.organized'),
            $this->menu(26, 20, 'Partnership 관리', '/backoffice/partnerships', null, 6, 'homepage.partnerships'),

            $this->menu(2, null, 'About', null, 'fa-info-circle', 4, 'about'),
            $this->menu(3, 2, 'About the Forum', '/backoffice/about-the-forum', null, 1, 'about.forum'),
            $this->menu(4, 2, 'Steering Committee', '/backoffice/steering-committee', null, 2, 'about.steering'),
            $this->menu(5, 2, 'Co-Organizers', '/backoffice/co-organizers', null, 3, 'about.co-organizers'),
            $this->menu(6, 2, 'Venue', '/backoffice/venue', null, 4, 'about.venue'),

            $this->menu(7, null, 'Programme', null, 'fa-calendar-alt', 5, 'programme'),
            $this->menu(8, 7, 'Theme', '/backoffice/programme/theme', null, 1, 'programme.theme'),
            $this->menu(36, 7, 'Programme', '/backoffice/programme', null, 2, 'programme.manage'),
            $this->menu(37, 7, 'Speakers', '/backoffice/programme/speakers', null, 3, 'programme.speakers'),
            $this->menu(38, 7, 'Programme Book', '/backoffice/programme/book', null, 4, 'programme.book'),

            $this->menu(17, null, 'Archive', null, 'fa-archive', 6, 'archive'),
            $this->menu(18, 17, 'Past Forums (2025~) - Theme', '/backoffice/archives/2025-plus/theme', null, 1, 'archive.current.theme'),
            $this->menu(39, 17, 'Past Forums (2025~) - Programme', '/backoffice/archives/2025-plus/programme', null, 2, 'archive.current.programme'),
            $this->menu(40, 17, 'Past Forums (2025~) - Speakers', '/backoffice/archives/2025-plus/speakers', null, 3, 'archive.current.speakers'),
            $this->menu(19, 17, 'Past Forums (2015~2024)', '/backoffice/archives/2015-2024', null, 4, 'archive.legacy'),

            $this->menu(9, null, 'Media', null, 'fa-photo-video', 7, 'media'),
            $this->menu(10, 9, 'Photo Gallery', '/backoffice/media/photo-gallery', null, 1, 'media.photo-gallery'),
            $this->menu(11, 9, 'News Clippings', '/backoffice/media/news-clippings', null, 2, 'media.news-clippings'),
            $this->menu(12, 9, 'YouTube Channel', '/backoffice/media/youtube', null, 3, 'media.youtube'),

            $this->menu(15, null, 'Registration', null, 'fa-clipboard-list', 8, 'registration'),
            $this->menu(16, 15, 'Registration', '/backoffice/registration', null, 1, 'registration.manage'),
            $this->menu(41, 15, '신청자 확인', '/backoffice/registration/applicants', null, 2, 'registration.applicants'),

            $this->menu(13, null, 'Announcements', null, 'fa-bullhorn', 9, 'announcements'),
            $this->menu(14, 13, 'Announcements', '/backoffice/board-posts/notices', null, 1, 'announcements.manage'),

            $this->menu(28, null, '메일발송', null, 'fa-envelope', 10, 'mail'),
            $this->menu(29, 28, '주소록 관리', '/backoffice/address-books', null, 1, 'mail.address-books'),
            $this->menu(30, 28, '메일 발송', '/backoffice/mail-campaigns', null, 2, 'mail.campaigns'),
        ];

        DB::transaction(function () use ($menus): void {
            foreach ($menus as $menu) {
                // 동일한 메뉴 ID는 갱신하여 기존 관리자·그룹 권한 연결을 보존합니다.
                AdminMenu::query()->updateOrCreate(
                    ['id' => $menu['id']],
                    $menu,
                );
            }

            // 최신 사이트맵에서 제거된 메뉴만 정리합니다.
            AdminMenu::query()
                ->whereNotIn('id', array_column($menus, 'id'))
                ->delete();
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function menu(
        int $id,
        ?int $parentId,
        string $name,
        ?string $url,
        ?string $icon,
        int $order,
        string $permissionKey,
    ): array {
        return [
            'id' => $id,
            'parent_id' => $parentId,
            'name' => $name,
            'url' => $url,
            'icon' => $icon,
            'order' => $order,
            'is_active' => true,
            'permission_key' => $permissionKey,
        ];
    }
}
