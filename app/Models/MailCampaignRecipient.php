<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailCampaignRecipient extends Model
{
    protected $fillable = ['mail_campaign_id', 'name', 'email', 'status', 'error_message', 'sent_at'];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(MailCampaign::class, 'mail_campaign_id');
    }
}
