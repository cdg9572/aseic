<?php

namespace App\Services\Backoffice;

use App\Models\Speaker;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SpeakerService
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function getSpeakers(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = Speaker::query()->with('creator:id,name');

        if (($filters['is_active'] ?? '') !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (! empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (! empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }

        if (! empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($nameQuery) use ($keyword): void {
                $nameQuery->where('first_name', 'like', '%'.$keyword.'%')
                    ->orWhere('last_name', 'like', '%'.$keyword.'%')
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ['%'.$keyword.'%']);
            });
        }

        return $query->latest('id')->paginate($perPage)->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, UploadedFile>  $attachments
     */
    public function createSpeaker(
        array $data,
        ?UploadedFile $profileImage,
        array $attachments,
        ?int $adminId,
    ): Speaker {
        $storedPaths = [];

        try {
            if ($profileImage) {
                $data['profile_image'] = $profileImage->store('speakers/profiles', 'public');
                $data['profile_image_name'] = $profileImage->getClientOriginalName();
                $storedPaths[] = $data['profile_image'];
            }

            $attachmentFiles = [];
            foreach ($attachments as $attachment) {
                $path = $attachment->store('speakers/attachments', 'public');
                $storedPaths[] = $path;
                $attachmentFiles[] = [
                    'path' => $path,
                    'name' => $attachment->getClientOriginalName(),
                    'size' => $attachment->getSize(),
                ];
            }

            $data['attachments'] = $attachmentFiles ?: null;
            $data['attachment_path'] = $attachmentFiles[0]['path'] ?? null;
            $data['attachment_name'] = $attachmentFiles[0]['name'] ?? null;

            $data['created_by'] = $adminId;
            $data['updated_by'] = $adminId;

            return DB::transaction(fn (): Speaker => Speaker::query()->create($data));
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);
            throw $exception;
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, UploadedFile>  $attachments
     * @param  array<int, int|string>  $removeAttachmentIndexes
     */
    public function updateSpeaker(
        Speaker $speaker,
        array $data,
        ?UploadedFile $profileImage,
        array $attachments,
        bool $removeProfileImage,
        array $removeAttachmentIndexes,
        ?int $adminId,
    ): Speaker {
        $newPaths = [];
        $oldPaths = [];

        try {
            if ($profileImage) {
                $data['profile_image'] = $profileImage->store('speakers/profiles', 'public');
                $data['profile_image_name'] = $profileImage->getClientOriginalName();
                $newPaths[] = $data['profile_image'];
                if ($speaker->profile_image) {
                    $oldPaths[] = $speaker->profile_image;
                }
            } elseif ($removeProfileImage) {
                $data['profile_image'] = null;
                $data['profile_image_name'] = null;
                if ($speaker->profile_image) {
                    $oldPaths[] = $speaker->profile_image;
                }
            }

            $indexesToRemove = array_flip(array_map('intval', $removeAttachmentIndexes));
            $attachmentFiles = [];

            foreach ($speaker->attachment_files as $index => $attachmentFile) {
                if (isset($indexesToRemove[$index])) {
                    $oldPaths[] = $attachmentFile['path'];
                } else {
                    $attachmentFiles[] = $attachmentFile;
                }
            }

            foreach ($attachments as $attachment) {
                $path = $attachment->store('speakers/attachments', 'public');
                $newPaths[] = $path;
                $attachmentFiles[] = [
                    'path' => $path,
                    'name' => $attachment->getClientOriginalName(),
                    'size' => $attachment->getSize(),
                ];
            }

            $data['attachments'] = $attachmentFiles ?: null;
            $data['attachment_path'] = $attachmentFiles[0]['path'] ?? null;
            $data['attachment_name'] = $attachmentFiles[0]['name'] ?? null;

            $data['updated_by'] = $adminId;

            DB::transaction(function () use ($speaker, $data): void {
                $speaker->update($data);
            });

            Storage::disk('public')->delete($oldPaths);

            return $speaker->refresh();
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($newPaths);
            throw $exception;
        }
    }

    public function deleteSpeaker(Speaker $speaker): void
    {
        $speaker->delete();
    }

    /**
     * @param  array<int, int|string>  $ids
     */
    public function deleteSpeakers(array $ids): int
    {
        return Speaker::query()->whereIn('id', $ids)->delete();
    }
}
