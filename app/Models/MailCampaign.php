<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailCampaign extends Model
{
    use SoftDeletes;

    public const TARGET_ADDRESS_BOOK = 'address_book';

    public const TARGET_DIRECT = 'direct';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_QUEUED = 'queued';

    public const STATUS_SENDING = 'sending';

    public const STATUS_SENT = 'sent';

    public const STATUS_PARTIAL = 'partial';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'sender_name',
        'sender_email',
        'subject',
        'target_type',
        'direct_recipients',
        'subscription_status',
        'content',
        'attachments',
        'status',
        'queued_at',
        'sent_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'queued_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function addressBooks(): BelongsToMany
    {
        return $this->belongsToMany(AddressBook::class, 'mail_campaign_address_book');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(MailCampaignRecipient::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
