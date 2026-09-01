<?php

namespace App\Services\Backoffice;

use App\Models\AboutPage;
use App\Models\MainPage;
use App\Models\MainPageLink;
use App\Models\ProgrammePage;
use App\Models\RegistrationPage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class MainPageService
{
    public function __construct(private readonly MainPageAssetService $mainPageAssetService) {}

    /**
     * @param  array<string, mixed>  $filters
     */
    public function getMainPages(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = MainPage::query()->with('creator:id,name');

        if (($filters['is_visible'] ?? '') !== '') {
            $query->where('is_visible', (bool) $filters['is_visible']);
        }

        if (! empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (! empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }

        if (! empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($keywordQuery) use ($keyword): void {
                $keywordQuery->where('folder_name', 'like', '%'.$keyword.'%')
                    ->orWhere('event_name', 'like', '%'.$keyword.'%');
            });
        }

        return $query->latest('id')->paginate($perPage)->withQueryString();
    }

    /**
     * @return array<string, Collection<int, Model>>
     */
    public function linkOptions(): array
    {
        $options = [];

        foreach (MainPageLink::labels() as $slot => $label) {
            $options[$slot] = collect();
        }

        foreach (MainPageLink::aboutPageTypeMap() as $slot => $type) {
            $options[$slot] = AboutPage::query()
                ->where('type', $type)
                ->latest('id')
                ->get(['id', 'page_title']);
        }

        foreach (MainPageLink::programmePageTypeMap() as $slot => $type) {
            $options[$slot] = ProgrammePage::query()
                ->where('type', $type)
                ->latest('id')
                ->get(['id', 'page_title']);
        }

        $options[MainPageLink::SLOT_REGISTRATION] = RegistrationPage::query()
            ->latest('id')
            ->get(['id', 'page_title']);

        return $options;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, UploadedFile|array<int, UploadedFile>|null>  $uploads
     * @param  array<int, int|string>  $speakerIds
     * @param  array<string, int|string|null>  $links
     */
    public function createMainPage(
        array $data,
        array $uploads,
        array $speakerIds,
        array $links,
        ?int $adminId,
    ): MainPage {
        $storedPaths = [];
        $createdTemplatePath = null;

        try {
            $this->applyCreateUploads($data, $uploads, $storedPaths);
            $data['created_by'] = $adminId;
            $data['updated_by'] = $adminId;

            $mainPage = DB::transaction(function () use (
                $data,
                $speakerIds,
                $links,
                &$createdTemplatePath,
            ): MainPage {
                $mainPage = MainPage::query()->create($data);
                $this->mainPageAssetService->normalizeSelections($mainPage);
                $this->syncSpeakers($mainPage, $speakerIds);
                $this->syncLinks($mainPage, $links);
                $createdTemplatePath = $this->createFrontendTemplateFolder($mainPage->folder_name);

                return $mainPage;
            });

            return $mainPage->fresh(['speakers', 'links', 'creator']);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);
            if ($createdTemplatePath !== null) {
                File::deleteDirectory($createdTemplatePath);
            }

            throw $exception;
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, UploadedFile|array<int, UploadedFile>|null>  $uploads
     * @param  array<string, bool|array<int, int|string>>  $removals
     * @param  array<int, int|string>  $speakerIds
     * @param  array<string, int|string|null>  $links
     */
    public function updateMainPage(
        MainPage $mainPage,
        array $data,
        array $uploads,
        array $removals,
        array $speakerIds,
        array $links,
        ?int $adminId,
    ): MainPage {
        $newPaths = [];
        $oldPaths = [];

        try {
            $this->applyUpdateUploads($mainPage, $data, $uploads, $removals, $newPaths, $oldPaths);
            $data['updated_by'] = $adminId;

            DB::transaction(function () use ($mainPage, $data, $speakerIds, $links): void {
                $mainPage->update($data);
                $this->mainPageAssetService->normalizeSelections($mainPage);
                $this->syncSpeakers($mainPage, $speakerIds);
                $this->syncLinks($mainPage, $links);
            });

            Storage::disk('public')->delete(array_values(array_unique($oldPaths)));

            return $mainPage->fresh(['speakers', 'links', 'creator']);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($newPaths);
            throw $exception;
        }
    }

    public function deleteMainPage(MainPage $mainPage): void
    {
        // 행사별 Blade는 운영 중 직접 수정될 수 있으므로 소프트 삭제 시 보존합니다.
        $mainPage->delete();
    }

    /**
     * @param  array<int, int|string>  $ids
     */
    public function deleteMainPages(array $ids): int
    {
        return MainPage::query()->whereIn('id', $ids)->delete();
    }

    public function mapContent(MainPage $mainPage, string $slot, Model $content): MainPageLink
    {
        $expectedModel = MainPageLink::modelMap()[$slot] ?? null;
        if ($expectedModel === null || ! ($content instanceof $expectedModel)) {
            throw new RuntimeException('해당 Main Page 연결 슬롯에 사용할 수 없는 콘텐츠입니다.');
        }

        $expectedAboutType = MainPageLink::aboutPageTypeMap()[$slot] ?? null;
        if ($content instanceof AboutPage && $expectedAboutType !== null && $content->type !== $expectedAboutType) {
            throw new RuntimeException('해당 Main Page 연결 슬롯에 사용할 수 없는 ABOUT 콘텐츠입니다.');
        }

        $expectedProgrammeType = MainPageLink::programmePageTypeMap()[$slot] ?? null;
        if ($content instanceof ProgrammePage && $expectedProgrammeType !== null && $content->type !== $expectedProgrammeType) {
            throw new RuntimeException('해당 Main Page 연결 슬롯에 사용할 수 없는 Programme 콘텐츠입니다.');
        }

        $existingTargetLink = $mainPage->links()->where('slot', $slot)->first();

        if ($content instanceof AboutPage || $content instanceof ProgrammePage || $content instanceof RegistrationPage) {
            MainPageLink::query()
                ->where('slot', $slot)
                ->where('linkable_type', $content->getMorphClass())
                ->where('linkable_id', $content->getKey())
                ->where('main_page_id', '!=', $mainPage->getKey())
                ->delete();
        }

        $link = $mainPage->links()->updateOrCreate(
            ['slot' => $slot],
            [
                'linkable_type' => $content->getMorphClass(),
                'linkable_id' => $content->getKey(),
            ],
        );

        if ($content instanceof AboutPage) {
            $content->updateQuietly(['is_main_page_visible' => true]);

            if ($existingTargetLink
                && $existingTargetLink->linkable_type === $content->getMorphClass()
                && (int) $existingTargetLink->linkable_id !== (int) $content->getKey()) {
                AboutPage::query()->whereKey($existingTargetLink->linkable_id)
                    ->update(['is_main_page_visible' => false]);
            }
        }

        return $link;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, UploadedFile|array<int, UploadedFile>|null>  $uploads
     * @param  array<int, string>  $storedPaths
     */
    private function applyCreateUploads(array &$data, array $uploads, array &$storedPaths): void
    {
        $this->storeSingleImage($data, $uploads['programme_background'] ?? null, 'programme_background', $storedPaths);
        $this->storeSingleImage($data, $uploads['register_background'] ?? null, 'register_background', $storedPaths);

        foreach (['host_images', 'organizer_images', 'co_organizer_images'] as $field) {
            $data[$field] = $this->storeImageList((array) ($uploads[$field] ?? []), $field, $storedPaths) ?: null;
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, UploadedFile|array<int, UploadedFile>|null>  $uploads
     * @param  array<string, bool|array<int, int|string>>  $removals
     * @param  array<int, string>  $newPaths
     * @param  array<int, string>  $oldPaths
     */
    private function applyUpdateUploads(
        MainPage $mainPage,
        array &$data,
        array $uploads,
        array $removals,
        array &$newPaths,
        array &$oldPaths,
    ): void {
        $this->replaceSingleImage(
            $mainPage,
            $data,
            $uploads['programme_background'] ?? null,
            (bool) ($removals['programme_background'] ?? false),
            'programme_background',
            $newPaths,
            $oldPaths,
        );
        $this->replaceSingleImage(
            $mainPage,
            $data,
            $uploads['register_background'] ?? null,
            (bool) ($removals['register_background'] ?? false),
            'register_background',
            $newPaths,
            $oldPaths,
        );

        $listFields = [
            'host_images' => $mainPage->host_image_files,
            'organizer_images' => $mainPage->organizer_image_files,
            'co_organizer_images' => $mainPage->co_organizer_image_files,
        ];

        foreach ($listFields as $field => $existingFiles) {
            $removeIndexes = (array) ($removals[$field] ?? []);
            $keptFiles = [];
            $removeLookup = array_flip(array_map('intval', $removeIndexes));

            foreach ($existingFiles as $index => $file) {
                if (isset($removeLookup[$index])) {
                    $oldPaths[] = $file['path'];

                    continue;
                }

                $keptFiles[] = $file;
            }

            $newFiles = $this->storeImageList((array) ($uploads[$field] ?? []), $field, $newPaths);
            $combinedFiles = array_values(array_merge($keptFiles, $newFiles));
            $data[$field] = $combinedFiles ?: null;
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $storedPaths
     */
    private function storeSingleImage(
        array &$data,
        mixed $file,
        string $field,
        array &$storedPaths,
    ): void {
        if (! $file instanceof UploadedFile) {
            return;
        }

        $path = $file->store('main-pages/'.str_replace('_', '-', $field), 'public');
        $storedPaths[] = $path;
        $data[$field.'_path'] = $path;
        $data[$field.'_name'] = $file->getClientOriginalName();
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $newPaths
     * @param  array<int, string>  $oldPaths
     */
    private function replaceSingleImage(
        MainPage $mainPage,
        array &$data,
        mixed $file,
        bool $remove,
        string $field,
        array &$newPaths,
        array &$oldPaths,
    ): void {
        $pathColumn = $field.'_path';
        $nameColumn = $field.'_name';

        if ($file instanceof UploadedFile) {
            $path = $file->store('main-pages/'.str_replace('_', '-', $field), 'public');
            $newPaths[] = $path;
            $data[$pathColumn] = $path;
            $data[$nameColumn] = $file->getClientOriginalName();
            if ($mainPage->{$pathColumn}) {
                $oldPaths[] = $mainPage->{$pathColumn};
            }
        } elseif ($remove) {
            $data[$pathColumn] = null;
            $data[$nameColumn] = null;
            if ($mainPage->{$pathColumn}) {
                $oldPaths[] = $mainPage->{$pathColumn};
            }
        }
    }

    /**
     * @param  array<int, UploadedFile>  $files
     * @param  array<int, string>  $storedPaths
     * @return array<int, array{path: string, name: string, size: int|null}>
     */
    private function storeImageList(array $files, string $field, array &$storedPaths): array
    {
        $storedFiles = [];
        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $path = $file->store('main-pages/'.str_replace('_', '-', $field), 'public');
            $storedPaths[] = $path;
            $storedFiles[] = [
                'path' => $path,
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
            ];
        }

        return $storedFiles;
    }

    /**
     * @param  array<int, int|string>  $speakerIds
     */
    private function syncSpeakers(MainPage $mainPage, array $speakerIds): void
    {
        $syncData = [];
        foreach (array_values(array_unique(array_map('intval', $speakerIds))) as $index => $speakerId) {
            $syncData[$speakerId] = ['sort_order' => $index + 1];
        }

        $mainPage->speakers()->sync($syncData);
    }

    /**
     * @param  array<string, int|string|null>  $links
     */
    private function syncLinks(MainPage $mainPage, array $links): void
    {
        foreach (MainPageLink::modelMap() as $slot => $modelClass) {
            $contentId = $links[$slot] ?? null;
            if ($contentId === null || $contentId === '' || $modelClass === null) {
                $existingLink = $mainPage->links()->where('slot', $slot)->first();
                $mainPage->links()->where('slot', $slot)->delete();
                if ($existingLink && $existingLink->linkable_type === (new AboutPage)->getMorphClass()) {
                    AboutPage::query()->whereKey($existingLink->linkable_id)
                        ->update(['is_main_page_visible' => false]);
                }

                continue;
            }

            $content = $modelClass::query()->findOrFail((int) $contentId);
            $this->mapContent($mainPage, $slot, $content);
        }
    }

    /**
     * @return string|null 생성된 폴더의 절대 경로
     */
    private function createFrontendTemplateFolder(string $folderName): ?string
    {
        $sourcePath = resource_path('views/forums/default');
        $targetPath = resource_path('views/forums/'.$folderName);

        if (! File::isDirectory($sourcePath)) {
            throw new RuntimeException('포럼 기본 Blade 템플릿 폴더를 찾을 수 없습니다.');
        }

        if (File::exists($targetPath)) {
            throw new RuntimeException('동일한 연도(폴더명)의 Blade 폴더가 이미 존재합니다.');
        }

        try {
            $copied = File::copyDirectory($sourcePath, $targetPath);
        } catch (Throwable) {
            if (File::exists($targetPath)) {
                File::deleteDirectory($targetPath);
            }

            throw new RuntimeException('행사별 Blade 템플릿 폴더를 생성할 수 없습니다.');
        }

        if (! $copied) {
            if (File::exists($targetPath)) {
                File::deleteDirectory($targetPath);
            }

            throw new RuntimeException('행사별 Blade 템플릿 폴더 복제에 실패했습니다.');
        }

        return $targetPath;
    }
}
