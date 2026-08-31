<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProgrammePageSession extends Model
{
    protected $fillable = [
        'programme_page_id',
        'day_number',
        'session_name',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function programmePage(): BelongsTo
    {
        return $this->belongsTo(ProgrammePage::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class, 'programme_session_speaker')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }
}
