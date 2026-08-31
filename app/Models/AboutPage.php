<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class AboutPage extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_FORUM = 'forum';

    public const TYPE_STEERING_COMMITTEE = 'steering_committee';

    public const TYPE_CO_ORGANIZERS = 'co_organizers';

    public const TYPE_VENUE = 'venue';

    protected $fillable = [
        'type',
        'page_title',
        'folder_name',
        'subtitle',
        'is_main_page_visible',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_main_page_visible' => 'boolean',
        ];
    }

    public function forumDetail(): HasOne
    {
        return $this->hasOne(AboutForumDetail::class);
    }

    public function venueDetail(): HasOne
    {
        return $this->hasOne(AboutVenueDetail::class);
    }

    public function coOrganizerItems(): HasMany
    {
        return $this->hasMany(AboutCoOrganizerItem::class)->orderBy('sort_order');
    }

    public function steeringOrganizedPartners(): BelongsToMany
    {
        return $this->belongsToMany(HomepagePartner::class, 'about_steering_partners')
            ->wherePivot('group_type', HomepagePartner::TYPE_ORGANIZED)
            ->withPivot(['group_type', 'sort_order'])
            ->orderByPivot('sort_order');
    }

    public function steeringPartnershipPartners(): BelongsToMany
    {
        return $this->belongsToMany(HomepagePartner::class, 'about_steering_partners')
            ->wherePivot('group_type', HomepagePartner::TYPE_PARTNERSHIP)
            ->withPivot(['group_type', 'sort_order'])
            ->orderByPivot('sort_order');
    }

    public function mainPageLink(): MorphOne
    {
        return $this->morphOne(MainPageLink::class, 'linkable');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
