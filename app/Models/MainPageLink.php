<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MainPageLink extends Model
{
    public const SLOT_ABOUT_FORUM = 'about_forum';

    public const SLOT_STEERING_COMMITTEE = 'steering_committee';

    public const SLOT_CO_ORGANIZERS = 'co_organizers';

    public const SLOT_VENUE = 'venue';

    public const SLOT_PROGRAMME_THEME = 'programme_theme';

    public const SLOT_PROGRAMME = 'programme';

    public const SLOT_PROGRAMME_SPEAKERS = 'programme_speakers';

    public const SLOT_PROGRAMME_BOOK = 'programme_book';

    public const SLOT_ARCHIVE_THEME = 'archive_theme';

    public const SLOT_ARCHIVE_PROGRAMME = 'archive_programme';

    public const SLOT_ARCHIVE_SPEAKERS = 'archive_speakers';

    public const SLOT_ARCHIVE_LEGACY = 'archive_legacy';

    public const SLOT_REGISTRATION = 'registration';

    protected $fillable = [
        'main_page_id',
        'slot',
        'linkable_type',
        'linkable_id',
    ];

    public function mainPage(): BelongsTo
    {
        return $this->belongsTo(MainPage::class);
    }

    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::SLOT_ABOUT_FORUM => 'About the Forum',
            self::SLOT_STEERING_COMMITTEE => 'Steering Committee',
            self::SLOT_CO_ORGANIZERS => 'Co-Organizers',
            self::SLOT_VENUE => 'Venue',
            self::SLOT_PROGRAMME_THEME => 'Theme',
            self::SLOT_PROGRAMME => 'Programme',
            self::SLOT_PROGRAMME_SPEAKERS => 'Speakers',
            self::SLOT_PROGRAMME_BOOK => 'Programme Book',
            self::SLOT_ARCHIVE_THEME => 'Past Forum (2025~) Theme',
            self::SLOT_ARCHIVE_PROGRAMME => 'Past Forum (2025~) Programme',
            self::SLOT_ARCHIVE_SPEAKERS => 'Past Forum (2025~) Speakers',
            self::SLOT_ARCHIVE_LEGACY => 'Past Forum (2015~2024)',
            self::SLOT_REGISTRATION => 'Registration',
        ];
    }

    /**
     * 현재 구현된 서브페이지 모델만 연결 대상으로 노출합니다.
     *
     * @return array<string, class-string<\Illuminate\Database\Eloquent\Model>|null>
     */
    public static function modelMap(): array
    {
        return [
            self::SLOT_ABOUT_FORUM => AboutPage::class,
            self::SLOT_STEERING_COMMITTEE => AboutPage::class,
            self::SLOT_CO_ORGANIZERS => AboutPage::class,
            self::SLOT_VENUE => AboutPage::class,
            self::SLOT_PROGRAMME_THEME => ProgrammePage::class,
            self::SLOT_PROGRAMME => ProgrammePage::class,
            self::SLOT_PROGRAMME_SPEAKERS => ProgrammePage::class,
            self::SLOT_PROGRAMME_BOOK => ProgrammePage::class,
            self::SLOT_ARCHIVE_THEME => ProgrammePage::class,
            self::SLOT_ARCHIVE_PROGRAMME => ProgrammePage::class,
            self::SLOT_ARCHIVE_SPEAKERS => ProgrammePage::class,
            self::SLOT_ARCHIVE_LEGACY => ProgrammePage::class,
            self::SLOT_REGISTRATION => RegistrationPage::class,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function aboutPageTypeMap(): array
    {
        return [
            self::SLOT_ABOUT_FORUM => AboutPage::TYPE_FORUM,
            self::SLOT_STEERING_COMMITTEE => AboutPage::TYPE_STEERING_COMMITTEE,
            self::SLOT_CO_ORGANIZERS => AboutPage::TYPE_CO_ORGANIZERS,
            self::SLOT_VENUE => AboutPage::TYPE_VENUE,
        ];
    }

    /** @return array<string, string> */
    public static function programmePageTypeMap(): array
    {
        return [
            self::SLOT_PROGRAMME_THEME => ProgrammePage::TYPE_THEME,
            self::SLOT_PROGRAMME => ProgrammePage::TYPE_PROGRAMME,
            self::SLOT_PROGRAMME_SPEAKERS => ProgrammePage::TYPE_SPEAKERS,
            self::SLOT_PROGRAMME_BOOK => ProgrammePage::TYPE_BOOK,
            self::SLOT_ARCHIVE_THEME => ProgrammePage::TYPE_ARCHIVE_THEME,
            self::SLOT_ARCHIVE_PROGRAMME => ProgrammePage::TYPE_ARCHIVE_PROGRAMME,
            self::SLOT_ARCHIVE_SPEAKERS => ProgrammePage::TYPE_ARCHIVE_SPEAKERS,
            self::SLOT_ARCHIVE_LEGACY => ProgrammePage::TYPE_ARCHIVE_LEGACY,
        ];
    }
}
