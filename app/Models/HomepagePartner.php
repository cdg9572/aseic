<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomepagePartner extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_ORGANIZED = 'organized';

    public const TYPE_PARTNERSHIP = 'partnership';

    protected $fillable = [
        'type',
        'first_name',
        'last_name',
        'position',
        'affiliation',
        'linkedin_url',
        'profile_image',
        'profile_image_name',
        'is_active',
        'is_image_visible',
        'content',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_image_visible' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }
}
