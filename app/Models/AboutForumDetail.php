<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AboutForumDetail extends Model
{
    protected $fillable = [
        'about_page_id',
        'overview',
        'forums_since_2015',
        'participants',
        'countries',
        'organizations',
        'backgrounds',
        'objectives',
    ];

    protected function casts(): array
    {
        return [
            'backgrounds' => 'array',
            'objectives' => 'array',
        ];
    }

    public function aboutPage(): BelongsTo
    {
        return $this->belongsTo(AboutPage::class);
    }
}
