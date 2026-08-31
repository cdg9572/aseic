<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegistrationPage extends Model
{
    use HasFactory, SoftDeletes;

    public const MODE_PARTICIPATING = 'participating';

    public const MODE_NOT_PARTICIPATING = 'not_participating';

    protected $fillable = [
        'page_title',
        'subtitle',
        'participation_mode',
        'period_text',
        'guide_step_1',
        'guide_step_2',
        'guide_step_3',
        'registration_start_date',
        'registration_end_date',
        'use_custom_end_text',
        'registration_end_text',
        'closed_notice',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'registration_start_date' => 'date',
            'registration_end_date' => 'date',
            'use_custom_end_text' => 'boolean',
        ];
    }

    public function mainPageLink(): MorphOne
    {
        return $this->morphOne(MainPageLink::class, 'linkable');
    }

    public function applicants(): HasMany
    {
        return $this->hasMany(RegistrationApplicant::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
