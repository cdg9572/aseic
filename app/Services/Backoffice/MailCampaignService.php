<?php

namespace App\Services\Backoffice;

use App\Jobs\SendMailCampaign;
use App\Models\AddressBook;
use App\Models\MailCampaign;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class MailCampaignService
{
    /** @param array<string, mixed> $filters */
    public function getCampaigns(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = MailCampaign::query()->with('creator:id,name')->withCount('recipients');
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }
        if (! empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }
        if (! empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(fn ($builder) => $builder->where('subject', 'like', '%'.$keyword.'%')->orWhere('sender_name', 'like', '%'.$keyword.'%')->orWhere('sender_email', 'like', '%'.$keyword.'%'));
        }

        return $query->latest('id')->paginate($perPage)->withQueryString();
    }

    public function addressBooks(): Collection
    {
        return AddressBook::query()->withCount('contacts')->latest('id')->get();
    }

    /** @param array<string, mixed> $data @param array<int, UploadedFile> $uploads */
    public function create(array $data, array $uploads, ?int $adminId): MailCampaign
    {
        return $this->save(null, $data, $uploads, [], $adminId);
    }

    /** @param array<string, mixed> $data @param array<int, UploadedFile> $uploads @param array<int, int|string> $removedIndexes */
    public function update(MailCampaign $campaign, array $data, array $uploads, array $removedIndexes, ?int $adminId): MailCampaign
    {
        return $this->save($campaign, $data, $uploads, $removedIndexes, $adminId);
    }

    public function delete(MailCampaign $campaign): void
    {
        $paths = collect((array) $campaign->attachments)->pluck('path')->filter()->all();
        $campaign->delete();
        Storage::disk('public')->delete($paths);
    }

    /** @param array<int, int|string> $ids */
    public function deleteMany(array $ids): int
    {
        $campaigns = MailCampaign::query()->whereIn('id', $ids)->get();
        foreach ($campaigns as $campaign) {
            $this->delete($campaign);
        }

        return $campaigns->count();
    }

    public function queue(MailCampaign $campaign): int
    {
        if (! in_array($campaign->status, [MailCampaign::STATUS_DRAFT, MailCampaign::STATUS_FAILED, MailCampaign::STATUS_PARTIAL], true)) {
            throw ValidationException::withMessages(['campaign' => '현재 상태에서는 다시 발송할 수 없습니다.']);
        }

        $recipients = $this->resolveRecipients($campaign);
        if ($recipients === []) {
            throw ValidationException::withMessages(['campaign' => '발송할 수신자가 없습니다.']);
        }

        DB::transaction(function () use ($campaign, $recipients): void {
            $campaign->recipients()->delete();
            $campaign->recipients()->createMany(array_map(fn ($recipient) => [...$recipient, 'status' => 'pending'], $recipients));
            $campaign->update(['status' => MailCampaign::STATUS_QUEUED, 'queued_at' => now(), 'sent_at' => null]);
        });

        SendMailCampaign::dispatch($campaign->id);

        return count($recipients);
    }

    /** @param array<string, mixed> $data @param array<int, UploadedFile> $uploads @param array<int, int|string> $removedIndexes */
    private function save(?MailCampaign $campaign, array $data, array $uploads, array $removedIndexes, ?int $adminId): MailCampaign
    {
        $newPaths = [];
        $oldPaths = [];

        try {
            $existing = (array) ($campaign?->attachments ?? []);
            $removed = array_unique(array_map('intval', $removedIndexes));
            $attachments = [];
            foreach ($existing as $index => $file) {
                if (in_array($index, $removed, true)) {
                    $oldPaths[] = $file['path'];
                } else {
                    $attachments[] = $file;
                }
            }
            foreach ($uploads as $upload) {
                $path = $upload->store('mail/attachments', 'public');
                $newPaths[] = $path;
                $attachments[] = ['path' => $path, 'name' => $upload->getClientOriginalName(), 'size' => $upload->getSize()];
            }

            $addressBookIds = array_map('intval', (array) ($data['address_book_ids'] ?? []));
            unset($data['address_book_ids'], $data['remove_attachments'], $data['return_url'], $data['attachments']);
            $payload = [...$data, 'attachments' => $attachments ?: null, 'updated_by' => $adminId];

            $campaign = DB::transaction(function () use ($campaign, $payload, $addressBookIds, $adminId): MailCampaign {
                if ($campaign) {
                    $campaign->update($payload);
                } else {
                    $campaign = MailCampaign::query()->create([...$payload, 'status' => MailCampaign::STATUS_DRAFT, 'created_by' => $adminId]);
                }
                $campaign->addressBooks()->sync($addressBookIds);

                return $campaign;
            });
            Storage::disk('public')->delete($oldPaths);

            return $campaign->fresh(['addressBooks', 'creator']);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($newPaths);
            throw $exception;
        }
    }

    /** @return array<int, array{name: string|null, email: string}> */
    private function resolveRecipients(MailCampaign $campaign): array
    {
        $recipients = [];
        if ($campaign->target_type === MailCampaign::TARGET_ADDRESS_BOOK) {
            $subscribed = $campaign->subscription_status === 'subscribed';
            $contacts = $campaign->addressBooks()->with(['contacts' => fn ($query) => $query->where('is_subscribed', $subscribed)])->get()->pluck('contacts')->flatten();
            foreach ($contacts as $contact) {
                $recipients[strtolower($contact->email)] = ['name' => $contact->name, 'email' => strtolower($contact->email)];
            }
        } else {
            foreach (preg_split('/[\s,;]+/', (string) $campaign->direct_recipients, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $email) {
                $email = strtolower(trim($email));
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $recipients[$email] = ['name' => null, 'email' => $email];
                }
            }
        }

        return array_values($recipients);
    }
}
