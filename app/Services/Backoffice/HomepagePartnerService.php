<?php

namespace App\Services\Backoffice;

use App\Models\HomepagePartner;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class HomepagePartnerService
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function getPartners(string $type, array $filters, int $perPage): LengthAwarePaginator
    {
        $query = HomepagePartner::query()
            ->with('creator:id,name')
            ->where('type', $type);

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
     */
    public function createPartner(
        string $type,
        array $data,
        ?UploadedFile $profileImage,
        ?int $adminId,
    ): HomepagePartner {
        $storedPath = null;

        try {
            if ($profileImage) {
                $storedPath = $profileImage->store('homepage-partners/profiles', 'public');
                $data['profile_image'] = $storedPath;
                $data['profile_image_name'] = $profileImage->getClientOriginalName();
            }

            $data['type'] = $type;
            $data['created_by'] = $adminId;
            $data['updated_by'] = $adminId;

            return DB::transaction(fn (): HomepagePartner => HomepagePartner::query()->create($data));
        } catch (Throwable $exception) {
            if ($storedPath) {
                Storage::disk('public')->delete($storedPath);
            }

            throw $exception;
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updatePartner(
        HomepagePartner $partner,
        array $data,
        ?UploadedFile $profileImage,
        bool $removeProfileImage,
        ?int $adminId,
    ): HomepagePartner {
        $newPath = null;
        $oldPath = null;

        try {
            if ($profileImage) {
                $newPath = $profileImage->store('homepage-partners/profiles', 'public');
                $data['profile_image'] = $newPath;
                $data['profile_image_name'] = $profileImage->getClientOriginalName();
                $oldPath = $partner->profile_image;
            } elseif ($removeProfileImage) {
                $data['profile_image'] = null;
                $data['profile_image_name'] = null;
                $oldPath = $partner->profile_image;
            }

            $data['updated_by'] = $adminId;

            DB::transaction(function () use ($partner, $data): void {
                $partner->update($data);
            });

            if ($oldPath) {
                Storage::disk('public')->delete($oldPath);
            }

            return $partner->refresh();
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }

            throw $exception;
        }
    }

    public function deletePartner(HomepagePartner $partner): void
    {
        $partner->delete();
    }

    /**
     * @param  array<int, int|string>  $ids
     */
    public function deletePartners(string $type, array $ids): int
    {
        return HomepagePartner::query()
            ->where('type', $type)
            ->whereIn('id', $ids)
            ->delete();
    }
}
