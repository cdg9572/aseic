<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegistrationApplicant extends Model
{
    use SoftDeletes;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'registration_page_id',
        'name',
        'email',
        'phone',
        'country',
        'affiliation',
        'position',
        'participation_type',
        'status',
        'note',
        'agreed_privacy',
        'submitted_at',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'agreed_privacy' => 'boolean',
            'submitted_at' => 'datetime',
        ];
    }

    public function registrationPage(): BelongsTo
    {
        return $this->belongsTo(RegistrationPage::class);
    }
}
