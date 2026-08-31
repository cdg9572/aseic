<?php

namespace App\Mail;

use App\Models\MailCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly MailCampaign $campaign) {}

    public function build(): self
    {
        $mail = $this->subject($this->campaign->subject)
            ->from($this->campaign->sender_email, $this->campaign->sender_name)
            ->view('emails.campaign');

        foreach ((array) $this->campaign->attachments as $attachment) {
            $mail->attachFromStorageDisk('public', $attachment['path'], $attachment['name']);
        }

        return $mail;
    }
}
