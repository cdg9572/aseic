<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AboutCoOrganizerItem extends Model
{
    protected $fillable = [
        'about_page_id',
        'logo_path',
        'logo_name',
        'name',
        'description',
        'url',
        'sort_order',
    ];

    public function aboutPage(): BelongsTo
    {
        return $this->belongsTo(AboutPage::class);
    }
}
