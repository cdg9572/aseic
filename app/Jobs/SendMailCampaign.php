<?php

namespace App\Jobs;

use App\Mail\CampaignMail;
use App\Models\MailCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendMailCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public readonly int $campaignId) {}

    public function handle(): void
    {
        $campaign = MailCampaign::query()->with('recipients')->findOrFail($this->campaignId);
        $campaign->update(['status' => MailCampaign::STATUS_SENDING]);
        $sent = 0;
        $failed = 0;

        foreach ($campaign->recipients()->where('status', 'pending')->get() as $recipient) {
            try {
                Mail::to($recipient->email, $recipient->name)->send(new CampaignMail($campaign));
                $recipient->update(['status' => 'sent', 'sent_at' => now(), 'error_message' => null]);
                $sent++;
            } catch (Throwable $exception) {
                $recipient->update(['status' => 'failed', 'error_message' => mb_substr($exception->getMessage(), 0, 1000)]);
                $failed++;
            }
        }

        $campaign->update([
            'status' => $failed === 0 ? MailCampaign::STATUS_SENT : ($sent > 0 ? MailCampaign::STATUS_PARTIAL : MailCampaign::STATUS_FAILED),
            'sent_at' => $sent > 0 ? now() : null,
        ]);
    }
}
