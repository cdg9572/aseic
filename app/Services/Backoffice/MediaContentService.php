<?php

namespace App\Services\Backoffice;

use App\Models\Category;
use App\Models\MediaContent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MediaContentService
{
    /** @param array<string, mixed> $filters */
    public function getContents(string $type, ?int $parentId, array $filters, int $perPage): LengthAwarePaginator
    {
        $query = MediaContent::query()
            ->with(['creator:id,name', 'category:id,name'])
            ->where('type', $type);

        if (! $this->usesCategories($type) || $parentId !== null) {
            $query->where('parent_id', $parentId);
        }

        if ($this->usesCategories($type) && ! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (($filters['is_visible'] ?? '') !== '') {
            $query->where('is_visible', $filters['is_visible'] === '1');
        }
        if (! empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }
        if (! empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }
        if (! empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($builder) use ($keyword): void {
                $builder->where('page_title', 'like', '%'.$keyword.'%')
                    ->orWhere('title', 'like', '%'.$keyword.'%')
                    ->orWhere('content', 'like', '%'.$keyword.'%');
            });
        }

        return $query->orderBy('sort_order')->latest('id')->paginate($perPage)->withQueryString();
    }

    public function categoryGroup(string $groupCode): ?Category
    {
        return Category::query()
            ->where('code', $groupCode)
            ->where('depth', 0)
            ->whereNull('parent_id')
            ->first();
    }

    public function categoriesForGroup(string $groupCode): Collection
    {
        $group = $this->categoryGroup($groupCode);
        if (! $group) {
            return collect();
        }

        return Category::query()
            ->where('parent_id', $group->id)
            ->where('depth', 1)
            ->active()
            ->orderByDesc('display_order')
            ->orderBy('id')
            ->get(['id', 'name']);
    }

    /** @param array<string, mixed> $data */
    public function create(string $type, ?MediaContent $parent, array $data, ?UploadedFile $image, ?int $adminId): MediaContent
    {
        $newPath = null;

        try {
            if ($image) {
                $newPath = $image->store('media/photos', 'public');
                $data['image_path'] = $newPath;
                $data['image_name'] = $image->getClientOriginalName();
                $data['image_size'] = $image->getSize();
            }

            return MediaContent::query()->create([
                ...$this->cleanData($data),
                'type' => $type,
                'parent_id' => $parent?->id,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ]);
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            throw $exception;
        }
    }

    /** @param array<string, mixed> $data */
    public function update(MediaContent $content, array $data, ?UploadedFile $image, bool $removeImage, ?int $adminId): MediaContent
    {
        $newPath = null;
        $oldPath = null;

        try {
            if ($image) {
                $newPath = $image->store('media/photos', 'public');
                $data['image_path'] = $newPath;
                $data['image_name'] = $image->getClientOriginalName();
                $data['image_size'] = $image->getSize();
                $oldPath = $content->image_path;
            } elseif ($removeImage) {
                $oldPath = $content->image_path;
                $data['image_path'] = null;
                $data['image_name'] = null;
                $data['image_size'] = null;
            }

            $content->update([...$this->cleanData($data), 'updated_by' => $adminId]);
            if ($oldPath) {
                Storage::disk('public')->delete($oldPath);
            }

            return $content->fresh();
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            throw $exception;
        }
    }

    public function delete(MediaContent $content): void
    {
        $contents = $content->children()->withTrashed()->get()->push($content);
        $paths = $contents->pluck('image_path')->filter()->unique()->values()->all();

        DB::transaction(function () use ($content): void {
            $content->children()->delete();
            $content->delete();
        });

        Storage::disk('public')->delete($paths);
    }

    /** @param array<int, int|string> $ids */
    public function deleteMany(string $type, ?int $parentId, array $ids): int
    {
        $query = MediaContent::query()
            ->where('type', $type)
            ->whereIn('id', $ids);

        if (! $this->usesCategories($type) || $parentId !== null) {
            $query->where('parent_id', $parentId);
        }

        $contents = $query->get();

        foreach ($contents as $content) {
            $this->delete($content);
        }

        return $contents->count();
    }

    /** @param array<string, mixed> $data */
    private function cleanData(array $data): array
    {
        unset($data['return_url'], $data['remove_image'], $data['image']);

        return $data;
    }

    private function usesCategories(string $type): bool
    {
        return in_array($type, [MediaContent::TYPE_PHOTO_ITEM, MediaContent::TYPE_NEWS_ITEM], true);
    }
}
