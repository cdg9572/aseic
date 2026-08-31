<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AboutVenueDetail extends Model
{
    public const FORMAT_ONLINE_OFFLINE = 'online_offline';

    public const FORMAT_ONLINE = 'online';

    public const FORMAT_OFFLINE = 'offline';

    protected $fillable = [
        'about_page_id',
        'forum_location',
        'postal_code',
        'address',
        'address_detail',
        'venue_name',
        'venue_description',
        'event_date',
        'format',
        'bus_content',
        'subway_content',
        'taxi_content',
    ];

    public function aboutPage(): BelongsTo
    {
        return $this->belongsTo(AboutPage::class);
    }

    /** @return array<string, string> */
    public static function formatOptions(): array
    {
        return [
            self::FORMAT_ONLINE_OFFLINE => 'Online & Offline',
            self::FORMAT_ONLINE => 'Online',
            self::FORMAT_OFFLINE => 'Offline',
        ];
    }
}
